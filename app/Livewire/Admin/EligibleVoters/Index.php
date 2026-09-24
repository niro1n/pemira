<?php

namespace App\Livewire\Admin\EligibleVoters;

use App\Models\AuditLog;
use App\Models\Election;
use App\Models\EligibleVoter;
use App\Models\StudyProgram;
use App\Models\VoterAccount;
use App\Models\VotingParticipation;
use Carbon\Carbon;
use DateTimeImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Throwable;

#[Layout('layouts.admin')]
#[Title('Data Pemilih - Panel Admin')]
class Index extends Component
{
    use WithFileUploads, WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'prodi', history: true)]
    public ?int $studyProgramFilter = null;

    #[Url(as: 'reg', history: true)]
    public string $registrationFilter = 'all';

    #[Url(as: 'vote', history: true)]
    public string $votingFilter = 'all';

    #[Url(as: 'status', history: true)]
    public string $completenessFilter = 'all';

    #[Url(as: 'per_page', history: true)]
    public int $perPage = 25;

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDetailModal = false;

    public bool $showDeleteModal = false;

    public bool $showImportModal = false;

    public bool $showFilterModal = false;

    public ?int $selectedEligibleVoterId = null;

    public ?EligibleVoter $selectedEligibleVoter = null;

    public string $nim = '';

    public string $name = '';

    public ?int $study_program_id = null;

    public string $date_of_birth = '';

    public bool $isNimLocked = false;

    public ?string $deleteError = null;

    public $importFile = null;

    public string $importStep = 'upload';

    public bool $updateExisting = false;

    public array $importSummary = [
        'total' => 0,
        'valid' => 0,
        'complete' => 0,
        'incomplete' => 0,
        'duplicates' => 0,
        'errors' => 0,
    ];

    public array $importPreview = [];

    public array $importErrors = [];

    public int $totalErrorsCount = 0;

    public array $importResult = [
        'inserted' => 0,
        'complete' => 0,
        'incomplete' => 0,
        'duplicates' => 0,
        'failed' => 0,
    ];

    public function mount(): void
    {
        Gate::authorize('access-admin-panel');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStudyProgramFilter(): void
    {
        $this->resetPage();
    }

    public function updatedRegistrationFilter(): void
    {
        $this->resetPage();
    }

    public function updatedVotingFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCompletenessFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->studyProgramFilter = null;
        $this->registrationFilter = 'all';
        $this->votingFilter = 'all';
        $this->completenessFilter = 'all';
        $this->resetPage();
    }

    public function openFilterModal(): void
    {
        $this->showFilterModal = true;
    }

    public function closeFilterModal(): void
    {
        $this->showFilterModal = false;
    }

    public function openFilterDrawer(): void
    {
        $this->openFilterModal();
    }

    public function closeFilterDrawer(): void
    {
        $this->closeFilterModal();
    }

    public function clearFilter(string $filter): void
    {
        if ($filter === 'study_program') {
            $this->studyProgramFilter = null;
        } elseif ($filter === 'completeness') {
            $this->completenessFilter = 'all';
        } elseif ($filter === 'registration') {
            $this->registrationFilter = 'all';
        } elseif ($filter === 'voting') {
            $this->votingFilter = 'all';
        }

        $this->resetPage();
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        $count = 0;

        if (! empty($this->studyProgramFilter)) {
            $count++;
        }

        if ($this->completenessFilter !== 'all') {
            $count++;
        }

        if ($this->registrationFilter !== 'all') {
            $count++;
        }

        if ($this->votingFilter !== 'all') {
            $count++;
        }

        return $count;
    }

    #[Computed]
    public function currentElection(): ?Election
    {
        return Election::current();
    }

    #[Computed]
    public function statistics(): array
    {
        $total = EligibleVoter::count();
        $complete = EligibleVoter::complete()->count();
        $incomplete = EligibleVoter::incomplete()->count();
        $registered = VoterAccount::count();
        $unregistered = max(0, $total - $registered);

        $currentElection = $this->currentElection;
        $voted = null;
        $unvoted = null;

        if ($currentElection) {
            $voted = VotingParticipation::where('election_id', $currentElection->id)->count();
            $unvoted = max(0, $total - $voted);
        }

        return [
            'total' => $total,
            'complete' => $complete,
            'incomplete' => $incomplete,
            'registered' => $registered,
            'unregistered' => $unregistered,
            'has_current_election' => $currentElection !== null,
            'election_name' => $currentElection?->name,
            'voted' => $voted,
            'unvoted' => $unvoted,
        ];
    }

    #[Computed]
    public function studyPrograms(): Collection
    {
        return StudyProgram::orderBy('name')->get();
    }

    #[Computed]
    public function eligibleVoters(): LengthAwarePaginator
    {
        $currentElection = $this->currentElection;

        return EligibleVoter::query()
            ->with('studyProgram')
            ->withExists('voterAccount')
            ->when($currentElection, function ($query) use ($currentElection) {
                $query->withExists([
                    'voterAccount as has_voted' => function ($q) use ($currentElection) {
                        $q->whereHas('votingParticipations', function ($vp) use ($currentElection) {
                            $vp->where('election_id', $currentElection->id);
                        });
                    },
                ]);
            })
            ->when(trim($this->search) !== '', function ($query) {
                $term = '%'.trim($this->search).'%';
                $query->where(function ($q) use ($term) {
                    $q->where('nim', 'like', $term)
                        ->orWhere('name', 'like', $term);
                });
            })
            ->when($this->studyProgramFilter, function ($query) {
                $query->where('study_program_id', $this->studyProgramFilter);
            })
            ->when($this->registrationFilter === 'registered', function ($query) {
                $query->whereHas('voterAccount');
            })
            ->when($this->registrationFilter === 'unregistered', function ($query) {
                $query->whereDoesntHave('voterAccount');
            })
            ->when($this->votingFilter === 'voted' && $currentElection, function ($query) use ($currentElection) {
                $query->whereHas('voterAccount.votingParticipations', function ($q) use ($currentElection) {
                    $q->where('election_id', $currentElection->id);
                });
            })
            ->when($this->votingFilter === 'not_voted' && $currentElection, function ($query) use ($currentElection) {
                $query->whereDoesntHave('voterAccount.votingParticipations', function ($q) use ($currentElection) {
                    $q->where('election_id', $currentElection->id);
                });
            })
            ->when($this->completenessFilter === 'complete', function ($query) {
                $query->complete();
            })
            ->when($this->completenessFilter === 'incomplete', function ($query) {
                $query->incomplete();
            })
            ->orderBy('nim', 'asc')
            ->paginate($this->perPage);
    }

    public function openCreateModal(): void
    {
        Gate::authorize('access-admin-panel');

        $this->resetValidation();
        $this->nim = '';
        $this->name = '';
        $this->study_program_id = null;
        $this->date_of_birth = '';
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function createEligibleVoter(): void
    {
        Gate::authorize('access-admin-panel');

        $validated = $this->validate([
            'nim' => ['required', 'string', 'max:30', 'unique:eligible_voters,nim'],
            'name' => ['nullable', 'string', 'max:255'],
            'study_program_id' => ['nullable', 'exists:study_programs,id'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
        ], [
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM sudah terdaftar dalam sistem.',
            'study_program_id.exists' => 'Jurusan / Program Studi tidak valid.',
            'date_of_birth.date' => 'Format tanggal lahir tidak valid.',
            'date_of_birth.before' => 'Tanggal lahir harus sebelum hari ini.',
        ]);

        $name = trim($validated['name'] ?? '');
        $dob = ! empty($validated['date_of_birth']) ? $validated['date_of_birth'] : null;

        $voter = EligibleVoter::create([
            'nim' => trim($validated['nim']),
            'name' => $name !== '' ? $name : null,
            'study_program_id' => $validated['study_program_id'] ?? null,
            'date_of_birth' => $dob,
            'is_eligible' => true,
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'eligible_voter_created',
            'entity_type' => 'EligibleVoter',
            'entity_id' => (string) $voter->id,
            'description' => "Menambahkan data pemilih eligible NIM {$voter->nim} secara manual",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'nim' => $voter->nim,
                'name' => $voter->name,
                'study_program_id' => $voter->study_program_id,
                'date_of_birth' => $voter->date_of_birth?->format('Y-m-d'),
                'is_complete' => $voter->isComplete(),
            ],
        ]);

        session()->flash('success', "Data mahasiswa NIM {$voter->nim} berhasil ditambahkan.");

        $this->closeCreateModal();
    }

    public function openEditModal(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $this->resetValidation();
        $this->selectedEligibleVoter = EligibleVoter::with('voterAccount')->findOrFail($id);
        $this->selectedEligibleVoterId = $this->selectedEligibleVoter->id;
        $this->nim = $this->selectedEligibleVoter->nim;
        $this->name = $this->selectedEligibleVoter->name ?? '';
        $this->study_program_id = $this->selectedEligibleVoter->study_program_id;
        $this->date_of_birth = $this->selectedEligibleVoter->date_of_birth?->format('Y-m-d') ?? '';
        $this->isNimLocked = $this->selectedEligibleVoter->voterAccount !== null;

        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->selectedEligibleVoter = null;
        $this->selectedEligibleVoterId = null;
        $this->isNimLocked = false;
        $this->resetValidation();
    }

    public function updateEligibleVoter(): void
    {
        Gate::authorize('access-admin-panel');

        $voter = EligibleVoter::with('voterAccount')->findOrFail($this->selectedEligibleVoterId);

        if ($voter->voterAccount !== null && trim($this->nim) !== $voter->nim) {
            throw ValidationException::withMessages([
                'nim' => 'NIM tidak dapat diubah karena mahasiswa sudah memiliki akun pemilih terdaftar.',
            ]);
        }

        $validated = $this->validate([
            'nim' => ['required', 'string', 'max:30', Rule::unique('eligible_voters', 'nim')->ignore($voter->id)],
            'name' => ['nullable', 'string', 'max:255'],
            'study_program_id' => ['nullable', 'exists:study_programs,id'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
        ], [
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM sudah terdaftar pada mahasiswa lain.',
            'study_program_id.exists' => 'Jurusan / Program Studi tidak valid.',
            'date_of_birth.date' => 'Format tanggal lahir tidak valid.',
            'date_of_birth.before' => 'Tanggal lahir harus sebelum hari ini.',
        ]);

        $oldValues = [
            'nim' => $voter->nim,
            'name' => $voter->name,
            'study_program_id' => $voter->study_program_id,
            'date_of_birth' => $voter->date_of_birth?->format('Y-m-d'),
        ];

        $name = trim($validated['name'] ?? '');
        $dob = ! empty($validated['date_of_birth']) ? $validated['date_of_birth'] : null;

        $voter->update([
            'nim' => trim($validated['nim']),
            'name' => $name !== '' ? $name : null,
            'study_program_id' => $validated['study_program_id'] ?? null,
            'date_of_birth' => $dob,
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'eligible_voter_updated',
            'entity_type' => 'EligibleVoter',
            'entity_id' => (string) $voter->id,
            'description' => "Memperbarui data pemilih eligible NIM {$voter->nim}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'old' => $oldValues,
                'new' => [
                    'nim' => $voter->nim,
                    'name' => $voter->name,
                    'study_program_id' => $voter->study_program_id,
                    'date_of_birth' => $voter->date_of_birth?->format('Y-m-d'),
                    'is_complete' => $voter->isComplete(),
                ],
            ],
        ]);

        session()->flash('success', "Data mahasiswa NIM {$voter->nim} berhasil diperbarui.");

        $this->closeEditModal();
    }

    public function openDetailModal(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $currentElection = $this->currentElection;

        $this->selectedEligibleVoter = EligibleVoter::query()
            ->with(['studyProgram', 'voterAccount.votingParticipations'])
            ->withExists('voterAccount')
            ->when($currentElection, function ($query) use ($currentElection) {
                $query->withExists([
                    'voterAccount as has_voted' => function ($q) use ($currentElection) {
                        $q->whereHas('votingParticipations', function ($vp) use ($currentElection) {
                            $vp->where('election_id', $currentElection->id);
                        });
                    },
                ]);
            })
            ->findOrFail($id);

        $this->selectedEligibleVoterId = $this->selectedEligibleVoter->id;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedEligibleVoter = null;
        $this->selectedEligibleVoterId = null;
    }

    public function openDeleteModal(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $this->selectedEligibleVoter = EligibleVoter::with(['voterAccount', 'candidateMembers'])->findOrFail($id);
        $this->selectedEligibleVoterId = $this->selectedEligibleVoter->id;

        if ($this->selectedEligibleVoter->voterAccount !== null || $this->selectedEligibleVoter->candidateMembers->isNotEmpty()) {
            $this->deleteError = 'Data mahasiswa tidak dapat dihapus karena sudah digunakan dalam data pemilih.';
        } else {
            $this->deleteError = null;
        }

        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->selectedEligibleVoter = null;
        $this->selectedEligibleVoterId = null;
        $this->deleteError = null;
    }

    public function deleteEligibleVoter(): void
    {
        Gate::authorize('access-admin-panel');

        $voter = EligibleVoter::with(['voterAccount', 'candidateMembers'])->findOrFail($this->selectedEligibleVoterId);

        if ($voter->voterAccount !== null || $voter->candidateMembers->isNotEmpty()) {
            session()->flash('error', 'Data mahasiswa tidak dapat dihapus karena sudah digunakan dalam data pemilih.');
            $this->closeDeleteModal();

            return;
        }

        $deletedNim = $voter->nim;
        $deletedName = $voter->name;
        $deletedId = $voter->id;

        $voter->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'eligible_voter_deleted',
            'entity_type' => 'EligibleVoter',
            'entity_id' => (string) $deletedId,
            'description' => "Menghapus data pemilih eligible NIM {$deletedNim}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'nim' => $deletedNim,
                'name' => $deletedName,
            ],
        ]);

        session()->flash('success', "Data mahasiswa (NIM: {$deletedNim}) berhasil dihapus.");

        $this->closeDeleteModal();
    }

    public function openImportModal(): void
    {
        Gate::authorize('access-admin-panel');

        $this->cleanupTempFile();
        $this->resetImportState();
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->cleanupTempFile();
        $this->resetImportState();
        $this->showImportModal = false;
    }

    public function resetImportState(): void
    {
        $this->importFile = null;
        $this->importStep = 'upload';
        $this->updateExisting = false;
        $this->importSummary = [
            'total' => 0,
            'valid' => 0,
            'complete' => 0,
            'incomplete' => 0,
            'duplicates' => 0,
            'errors' => 0,
        ];
        $this->importPreview = [];
        $this->importErrors = [];
        $this->totalErrorsCount = 0;
        $this->importResult = [
            'inserted' => 0,
            'complete' => 0,
            'incomplete' => 0,
            'duplicates' => 0,
            'failed' => 0,
        ];
        $this->resetValidation();
    }

    public function updatedUpdateExisting(): void
    {
        $sourceCsv = $this->getTempSourceCsvPath();
        if (file_exists($sourceCsv) && $this->importStep === 'preview') {
            $this->parseAndProcessCsv($sourceCsv);
        }
    }

    public function processUpload(): void
    {
        Gate::authorize('access-admin-panel');

        $this->validate([
            'importFile' => ['required', 'file', 'mimes:csv,txt', 'max:20480'],
        ], [
            'importFile.required' => 'File CSV wajib dipilih.',
            'importFile.file' => 'Upload harus berupa file valid.',
            'importFile.mimes' => 'File harus berformat CSV.',
            'importFile.max' => 'Ukuran file CSV maksimal 20 MB.',
        ]);

        $sourcePath = $this->getTempSourceCsvPath();
        if (! copy($this->importFile->getRealPath(), $sourcePath)) {
            throw ValidationException::withMessages([
                'importFile' => 'Gagal menyimpan file CSV sementara untuk proses import.',
            ]);
        }

        $this->parseAndProcessCsv($sourcePath);
        $this->importStep = 'preview';
    }

    protected function parseAndProcessCsv(string $sourcePath): void
    {
        $handle = fopen($sourcePath, 'r');

        if (! $handle) {
            throw ValidationException::withMessages([
                'importFile' => 'Gagal membuka file CSV yang diunggah.',
            ]);
        }

        $delimiter = $this->detectDelimiter($handle);
        $headerRow = fgetcsv($handle, 0, $delimiter);

        if (! $headerRow) {
            fclose($handle);
            throw ValidationException::withMessages([
                'importFile' => 'File CSV kosong atau tidak memiliki baris header.',
            ]);
        }

        $indexes = $this->detectHeaderIndexes($headerRow);

        if ($indexes === null) {
            fclose($handle);
            throw ValidationException::withMessages([
                'importFile' => 'Header CSV harus memiliki kolom: NIM, Nama, Jurusan, dan Tanggal Lahir.',
            ]);
        }

        $programs = StudyProgram::all();
        $studyProgramMap = $this->getStudyProgramMap($programs);
        $existingNims = EligibleVoter::pluck('id', 'nim')->all();

        $tempPath = $this->getTempFilePath();
        $tempHandle = fopen($tempPath, 'w');

        if (! $tempHandle) {
            fclose($handle);
            throw ValidationException::withMessages([
                'importFile' => 'Gagal menyiapkan penyimpanan sementara untuk proses import.',
            ]);
        }

        $rowNumber = 1;
        $totalRows = 0;
        $completeCount = 0;
        $incompleteCount = 0;
        $duplicateCount = 0;
        $errorCount = 0;
        $toUpdateCount = 0;
        $toInsertCount = 0;
        $seenNimsInFile = [];
        $previewRows = [];
        $errorsList = [];

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowNumber++;

            if (count(array_filter($row, fn ($val) => trim((string) $val) !== '')) === 0) {
                continue;
            }

            $totalRows++;

            $nimRaw = trim((string) ($row[$indexes['nim']] ?? ''));
            $nameRaw = trim((string) ($row[$indexes['name']] ?? ''));
            $jurusanRaw = trim((string) ($row[$indexes['jurusan']] ?? ''));
            $dobRaw = trim((string) ($row[$indexes['dob']] ?? ''));

            if ($nimRaw === '') {
                $errorCount++;
                if (count($errorsList) < 100) {
                    $errorsList[] = [
                        'row' => $rowNumber,
                        'message' => 'NIM kosong (data tidak dapat diidentifikasi).',
                    ];
                }

                continue;
            }

            if (isset($seenNimsInFile[$nimRaw])) {
                $duplicateCount++;
                if (count($errorsList) < 100) {
                    $errorsList[] = [
                        'row' => $rowNumber,
                        'message' => "NIM '{$nimRaw}' terduplikasi di dalam file CSV.",
                    ];
                }

                continue;
            }
            $seenNimsInFile[$nimRaw] = true;

            $alreadyInDb = isset($existingNims[$nimRaw]);

            if ($alreadyInDb && ! $this->updateExisting) {
                $duplicateCount++;
                if (count($errorsList) < 100) {
                    $errorsList[] = [
                        'row' => $rowNumber,
                        'message' => "NIM '{$nimRaw}' sudah ada di database.",
                    ];
                }

                continue;
            }

            if ($alreadyInDb) {
                $toUpdateCount++;
            } else {
                $toInsertCount++;
            }

            $name = $nameRaw !== '' ? $nameRaw : null;
            $studyProgramId = $this->matchStudyProgram($jurusanRaw, $studyProgramMap, $programs);

            $parsedDob = null;
            if ($dobRaw !== '') {
                $parsedDob = $this->parseCsvDate($dobRaw);
                if ($parsedDob === null) {
                    if (count($errorsList) < 100) {
                        $errorsList[] = [
                            'row' => $rowNumber,
                            'message' => "Tanggal lahir '{$dobRaw}' tidak valid (NIM: {$nimRaw}) -> Dikosongkan (status: TIDAK LENGKAP).",
                        ];
                    }
                }
            }

            $isComplete = ($name !== null && $studyProgramId !== null && $parsedDob !== null);

            if ($isComplete) {
                $completeCount++;
            } else {
                $incompleteCount++;
            }

            $record = [
                'nim' => $nimRaw,
                'name' => $name,
                'study_program_id' => $studyProgramId,
                'date_of_birth' => $parsedDob,
                'is_eligible' => 1,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];

            fwrite($tempHandle, json_encode($record)."\n");

            if (count($previewRows) < 10) {
                $previewRows[] = [
                    'nim' => $nimRaw,
                    'name' => $name ?? '—',
                    'jurusan' => $jurusanRaw !== '' ? $jurusanRaw : '—',
                    'date_of_birth' => $parsedDob ?? '—',
                    'is_complete' => $isComplete,
                    'status' => $alreadyInDb
                        ? ($isComplete ? 'LENGKAP (PERBARUI)' : 'PARSIAL (PERBARUI)')
                        : ($isComplete ? 'LENGKAP' : 'TIDAK LENGKAP'),
                    'is_existing' => $alreadyInDb,
                ];
            }
        }

        fclose($handle);
        fclose($tempHandle);

        $validCount = $completeCount + $incompleteCount;

        $this->importSummary = [
            'total' => $totalRows,
            'valid' => $validCount,
            'complete' => $completeCount,
            'incomplete' => $incompleteCount,
            'duplicates' => $duplicateCount,
            'errors' => $errorCount,
            'to_update' => $toUpdateCount,
            'to_insert' => $toInsertCount,
        ];
        $this->importPreview = $previewRows;
        $this->importErrors = $errorsList;
        $this->totalErrorsCount = count($errorsList);
    }

    public function confirmImport(): void
    {
        Gate::authorize('access-admin-panel');

        $tempPath = $this->getTempFilePath();

        if (! file_exists($tempPath) || filesize($tempPath) === 0) {
            throw ValidationException::withMessages([
                'importFile' => 'Tidak ada data valid yang dapat diimpor.',
            ]);
        }

        $tempHandle = fopen($tempPath, 'r');

        if (! $tempHandle) {
            throw ValidationException::withMessages([
                'importFile' => 'Gagal membaca data antrean import.',
            ]);
        }

        $chunk = [];
        $chunkSize = 500;
        $insertedCount = 0;

        DB::transaction(function () use ($tempHandle, &$chunk, $chunkSize, &$insertedCount) {
            while (($line = fgets($tempHandle)) !== false) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                $record = json_decode($line, true);
                if (! is_array($record)) {
                    continue;
                }

                $chunk[] = $record;

                if (count($chunk) >= $chunkSize) {
                    $this->writeChunk($chunk);
                    $insertedCount += count($chunk);
                    $chunk = [];
                }
            }

            if (count($chunk) > 0) {
                $this->writeChunk($chunk);
                $insertedCount += count($chunk);
                $chunk = [];
            }
        });

        fclose($tempHandle);
        $this->cleanupTempFile();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'eligible_voter_import',
            'entity_type' => 'EligibleVoter',
            'entity_id' => null,
            'description' => $this->updateExisting
                ? "Mengimpor & memperbarui {$insertedCount} data mahasiswa eligible dari CSV"
                : "Mengimpor {$insertedCount} data mahasiswa eligible dari CSV",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'total_rows' => $this->importSummary['total'],
                'inserted' => $insertedCount,
                'complete' => $this->importSummary['complete'],
                'incomplete' => $this->importSummary['incomplete'],
                'duplicates' => $this->importSummary['duplicates'],
                'failed' => $this->importSummary['errors'],
                'to_update' => $this->importSummary['to_update'] ?? 0,
                'to_insert' => $this->importSummary['to_insert'] ?? 0,
                'update_existing' => $this->updateExisting,
            ],
        ]);

        $this->importResult = [
            'inserted' => $insertedCount,
            'complete' => $this->importSummary['complete'],
            'incomplete' => $this->importSummary['incomplete'],
            'duplicates' => $this->importSummary['duplicates'],
            'failed' => $this->importSummary['errors'],
        ];

        $this->importStep = 'result';
        session()->flash('success', "Proses import selesai. Sebanyak {$insertedCount} data mahasiswa berhasil diproses ({$this->importSummary['complete']} Lengkap, {$this->importSummary['incomplete']} Tidak Lengkap).");
    }

    protected function writeChunk(array $chunk): void
    {
        if ($this->updateExisting) {
            $driver = DB::connection()->getDriverName();

            if ($driver === 'mysql') {
                $useAlias = (bool) (DB::connection()->getConfig('use_upsert_alias') ?? false);
                $nameRef = $useAlias ? 'laravel_upsert_alias.name' : 'values(name)';
                $progRef = $useAlias ? 'laravel_upsert_alias.study_program_id' : 'values(study_program_id)';
                $dobRef = $useAlias ? 'laravel_upsert_alias.date_of_birth' : 'values(date_of_birth)';
                $updatedRef = $useAlias ? 'laravel_upsert_alias.updated_at' : 'values(updated_at)';

                EligibleVoter::upsert(
                    $chunk,
                    ['nim'],
                    [
                        'name' => DB::raw("COALESCE({$nameRef}, eligible_voters.name)"),
                        'study_program_id' => DB::raw("COALESCE({$progRef}, eligible_voters.study_program_id)"),
                        'date_of_birth' => DB::raw("COALESCE({$dobRef}, eligible_voters.date_of_birth)"),
                        'updated_at' => DB::raw($updatedRef),
                    ]
                );
            } elseif ($driver === 'mariadb') {
                EligibleVoter::upsert(
                    $chunk,
                    ['nim'],
                    [
                        'name' => DB::raw('COALESCE(values(name), eligible_voters.name)'),
                        'study_program_id' => DB::raw('COALESCE(values(study_program_id), eligible_voters.study_program_id)'),
                        'date_of_birth' => DB::raw('COALESCE(values(date_of_birth), eligible_voters.date_of_birth)'),
                        'updated_at' => DB::raw('values(updated_at)'),
                    ]
                );
            } elseif ($driver === 'sqlite' || $driver === 'pgsql') {
                EligibleVoter::upsert(
                    $chunk,
                    ['nim'],
                    [
                        'name' => DB::raw('COALESCE(excluded.name, eligible_voters.name)'),
                        'study_program_id' => DB::raw('COALESCE(excluded.study_program_id, eligible_voters.study_program_id)'),
                        'date_of_birth' => DB::raw('COALESCE(excluded.date_of_birth, eligible_voters.date_of_birth)'),
                        'updated_at' => DB::raw('excluded.updated_at'),
                    ]
                );
            } else {
                EligibleVoter::upsert(
                    $chunk,
                    ['nim'],
                    ['name', 'study_program_id', 'date_of_birth', 'updated_at']
                );
            }
        } else {
            EligibleVoter::insert($chunk);
        }
    }

    protected function detectDelimiter($handle): string
    {
        $firstLine = fgets($handle);
        rewind($handle);

        if (! $firstLine) {
            return ',';
        }

        $commaCount = substr_count($firstLine, ',');
        $semicolonCount = substr_count($firstLine, ';');
        $tabCount = substr_count($firstLine, "\t");

        if ($semicolonCount > $commaCount && $semicolonCount > $tabCount) {
            return ';';
        }

        if ($tabCount > $commaCount && $tabCount > $semicolonCount) {
            return "\t";
        }

        return ',';
    }

    protected function detectHeaderIndexes(array $headerRow): ?array
    {
        $nimIdx = null;
        $nameIdx = null;
        $jurusanIdx = null;
        $dobIdx = null;

        $nimAliases = ['nim', 'nomor induk', 'nomor_induk', 'nomor induk mahasiswa', 'no mhs', 'student id', 'student_id'];
        $nameAliases = ['nama', 'name', 'nama mahasiswa', 'nama lengkap', 'student name', 'student_name', 'full name', 'fullname'];
        $jurusanAliases = ['jurusan', 'prodi', 'program studi', 'program_studi', 'study program', 'study_program', 'department', 'jurusan/prodi'];
        $dobAliases = ['tanggal lahir', 'tgl lahir', 'tanggal_lahir', 'tgl_lahir', 'date of birth', 'date_of_birth', 'dob', 'birth date', 'birth_date', 'birthdate'];

        foreach ($headerRow as $idx => $col) {
            $clean = strtolower(trim(preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/', '', (string) $col)));
            $clean = trim($clean, "\"' \t\n\r\0\x0B");

            if ($nimIdx === null && in_array($clean, $nimAliases, true)) {
                $nimIdx = $idx;
            } elseif ($nameIdx === null && in_array($clean, $nameAliases, true)) {
                $nameIdx = $idx;
            } elseif ($jurusanIdx === null && in_array($clean, $jurusanAliases, true)) {
                $jurusanIdx = $idx;
            } elseif ($dobIdx === null && in_array($clean, $dobAliases, true)) {
                $dobIdx = $idx;
            }
        }

        if ($nimIdx === null || $nameIdx === null || $jurusanIdx === null || $dobIdx === null) {
            return null;
        }

        return [
            'nim' => $nimIdx,
            'name' => $nameIdx,
            'jurusan' => $jurusanIdx,
            'dob' => $dobIdx,
        ];
    }

    protected function getStudyProgramMap($programs = null): array
    {
        $map = [];
        $programs = $programs ?? StudyProgram::all();

        foreach ($programs as $prog) {
            $nameKey = strtolower(trim($prog->name));
            $codeKey = strtolower(trim($prog->code));
            $map[$nameKey] = $prog->id;
            $map[$codeKey] = $prog->id;

            $nameAlpha = preg_replace('/[^a-z0-9]/', '', $nameKey);
            $codeAlpha = preg_replace('/[^a-z0-9]/', '', $codeKey);
            $map[$nameAlpha] = $prog->id;
            $map[$codeAlpha] = $prog->id;
        }

        return $map;
    }

    protected function matchStudyProgram(string $jurusanRaw, array $studyProgramMap, $programs): ?int
    {
        $jurusanRaw = trim($jurusanRaw);
        if ($jurusanRaw === '') {
            return null;
        }

        $clean = strtolower($jurusanRaw);
        if (isset($studyProgramMap[$clean])) {
            return $studyProgramMap[$clean];
        }

        $alphanumeric = preg_replace('/[^a-z0-9]/', '', $clean);
        if (isset($studyProgramMap[$alphanumeric])) {
            return $studyProgramMap[$alphanumeric];
        }

        foreach ($programs as $prog) {
            $nameClean = strtolower(trim($prog->name));
            $codeClean = strtolower(trim($prog->code));

            if (str_contains($clean, $nameClean) || str_contains($clean, $codeClean)) {
                return $prog->id;
            }
        }

        return null;
    }

    protected function parseCsvDate(string $rawDate): ?string
    {
        $rawDate = trim($rawDate);
        if ($rawDate === '') {
            return null;
        }

        $formats = [
            'Y-m-d',
            'd-m-Y',
            'd/m/Y',
            'Y/m/d',
            'd.m.Y',
            'Y.m.d',
            'j-n-Y',
            'j/n/Y',
            'j-m-Y',
            'j/m/Y',
            'd-n-Y',
            'd/n/Y',
            'm/d/Y',
            'm-d-Y',
            'Y-n-j',
            'Y/n/j',
        ];

        foreach ($formats as $format) {
            $parsed = DateTimeImmutable::createFromFormat('!'.$format, $rawDate);
            if ($parsed && $parsed->format($format) === $rawDate) {
                $year = (int) $parsed->format('Y');
                if ($year >= 1950 && $year <= 2030) {
                    return $parsed->format('Y-m-d');
                }
            }
        }

        if (is_numeric($rawDate) && (int) $rawDate >= 20000 && (int) $rawDate <= 60000) {
            try {
                $excelDate = (new DateTimeImmutable('1899-12-30'))->modify("+{$rawDate} days");
                $year = (int) $excelDate->format('Y');
                if ($year >= 1950 && $year <= 2030) {
                    return $excelDate->format('Y-m-d');
                }
            } catch (Throwable) {
            }
        }

        try {
            $carbon = Carbon::parse($rawDate);
            $year = (int) $carbon->format('Y');
            if ($year >= 1950 && $year <= 2030) {
                return $carbon->format('Y-m-d');
            }
        } catch (Throwable) {
            return null;
        }

        return null;
    }

    protected function getTempFilePath(): string
    {
        $dir = storage_path('app/temp');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir.'/eligible_voters_'.Auth::id().'.ndjson';
    }

    protected function getTempSourceCsvPath(): string
    {
        $dir = storage_path('app/temp');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir.'/eligible_voters_source_'.Auth::id().'.csv';
    }

    protected function cleanupTempFile(): void
    {
        $path = $this->getTempFilePath();
        if (file_exists($path)) {
            @unlink($path);
        }

        $sourceCsv = $this->getTempSourceCsvPath();
        if (file_exists($sourceCsv)) {
            @unlink($sourceCsv);
        }
    }

    public function render(): View
    {
        return view('livewire.admin.eligible-voters.index');
    }
}
