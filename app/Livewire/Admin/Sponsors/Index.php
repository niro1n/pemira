<?php

namespace App\Livewire\Admin\Sponsors;

use App\Models\AuditLog;
use App\Models\Sponsor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Manajemen Sponsor - Panel Admin')]
class Index extends Component
{
    use WithFileUploads, WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDetailModal = false;

    public bool $showDeleteModal = false;

    public ?int $selectedSponsorId = null;

    public ?Sponsor $selectedSponsor = null;

    public string $name = '';

    /** @var TemporaryUploadedFile|UploadedFile|null */
    public $logo = null;

    public ?string $existingLogo = null;

    public ?string $website_url = null;

    public bool $is_active = true;

    public int $sort_order = 0;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->selectedSponsorId = null;
        $this->selectedSponsor = null;
        $this->name = '';
        $this->logo = null;
        $this->existingLogo = null;
        $this->website_url = null;
        $this->is_active = true;
        $this->sort_order = 0;
    }

    public function openCreateModal(): void
    {
        Gate::authorize('access-admin-panel');

        $this->resetValidation();
        $this->resetForm();

        $maxOrder = (int) Sponsor::max('sort_order');
        $this->sort_order = $maxOrder + 1;
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function cancelNewLogo(): void
    {
        $this->logo = null;
    }

    public function createSponsor(): void
    {
        Gate::authorize('access-admin-panel');

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ], [
            'name.required' => 'Nama sponsor wajib diisi.',
            'logo.required' => 'Logo sponsor wajib diunggah.',
            'logo.image' => 'File harus berupa gambar.',
            'logo.mimes' => 'Format gambar yang diperbolehkan adalah JPEG, PNG, JPG, atau WebP.',
            'logo.max' => 'Ukuran logo maksimal adalah 2MB.',
            'website_url.url' => 'Format URL website tidak valid.',
            'sort_order.required' => 'Urutan sponsor wajib diisi.',
            'sort_order.integer' => 'Urutan sponsor harus berupa angka.',
            'sort_order.min' => 'Urutan sponsor minimal 0.',
        ]);

        $logoPath = $this->logo->store('sponsors', 'public');

        $sponsor = Sponsor::create([
            'name' => trim($this->name),
            'logo' => $logoPath,
            'website_url' => ! empty($this->website_url) ? trim($this->website_url) : null,
            'is_active' => $this->is_active,
            'sort_order' => (int) $this->sort_order,
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_sponsor',
            'entity_type' => 'Sponsor',
            'entity_id' => (string) $sponsor->id,
            'description' => "Menambahkan sponsor '{$sponsor->name}'",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'name' => $sponsor->name,
                'website_url' => $sponsor->website_url,
                'is_active' => $sponsor->is_active,
                'sort_order' => $sponsor->sort_order,
            ],
        ]);

        session()->flash('success', "Sponsor '{$sponsor->name}' berhasil ditambahkan.");

        $this->closeCreateModal();
        $this->resetPage();
    }

    public function openEditModal(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $sponsor = Sponsor::findOrFail($id);

        $this->resetValidation();
        $this->resetForm();

        $this->selectedSponsorId = $sponsor->id;
        $this->selectedSponsor = $sponsor;
        $this->name = $sponsor->name;
        $this->website_url = $sponsor->website_url;
        $this->is_active = $sponsor->is_active;
        $this->sort_order = $sponsor->sort_order;
        $this->existingLogo = $sponsor->logo;
        $this->logo = null;

        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function updateSponsor(): void
    {
        Gate::authorize('access-admin-panel');

        if (! $this->selectedSponsorId) {
            return;
        }

        $sponsor = Sponsor::findOrFail($this->selectedSponsorId);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ], [
            'name.required' => 'Nama sponsor wajib diisi.',
            'logo.image' => 'File harus berupa gambar.',
            'logo.mimes' => 'Format gambar yang diperbolehkan adalah JPEG, PNG, JPG, atau WebP.',
            'logo.max' => 'Ukuran logo maksimal adalah 2MB.',
            'website_url.url' => 'Format URL website tidak valid.',
            'sort_order.required' => 'Urutan sponsor wajib diisi.',
            'sort_order.integer' => 'Urutan sponsor harus berupa angka.',
            'sort_order.min' => 'Urutan sponsor minimal 0.',
        ]);

        $oldLogoToDelete = null;
        $logoPath = $sponsor->logo;

        if ($this->logo) {
            $oldLogoToDelete = $sponsor->logo;
            $logoPath = $this->logo->store('sponsors', 'public');
        }

        $sponsor->update([
            'name' => trim($this->name),
            'logo' => $logoPath,
            'website_url' => ! empty($this->website_url) ? trim($this->website_url) : null,
            'is_active' => $this->is_active,
            'sort_order' => (int) $this->sort_order,
        ]);

        if ($oldLogoToDelete && $oldLogoToDelete !== $logoPath && Storage::disk('public')->exists($oldLogoToDelete)) {
            Storage::disk('public')->delete($oldLogoToDelete);
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_sponsor',
            'entity_type' => 'Sponsor',
            'entity_id' => (string) $sponsor->id,
            'description' => "Memperbarui data sponsor '{$sponsor->name}'",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'name' => $sponsor->name,
                'website_url' => $sponsor->website_url,
                'is_active' => $sponsor->is_active,
                'sort_order' => $sponsor->sort_order,
            ],
        ]);

        session()->flash('success', "Sponsor '{$sponsor->name}' berhasil diperbarui.");

        $this->closeEditModal();
    }

    public function openDetailModal(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $this->selectedSponsor = Sponsor::findOrFail($id);
        $this->selectedSponsorId = $this->selectedSponsor->id;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedSponsor = null;
        $this->selectedSponsorId = null;
    }

    public function openDeleteModal(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $this->selectedSponsor = Sponsor::findOrFail($id);
        $this->selectedSponsorId = $this->selectedSponsor->id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->selectedSponsor = null;
        $this->selectedSponsorId = null;
    }

    public function deleteSponsor(): void
    {
        Gate::authorize('access-admin-panel');

        if (! $this->selectedSponsorId) {
            return;
        }

        $sponsor = Sponsor::findOrFail($this->selectedSponsorId);
        $sponsorName = $sponsor->name;
        $logoToDelete = $sponsor->logo;

        if ($logoToDelete && Storage::disk('public')->exists($logoToDelete)) {
            Storage::disk('public')->delete($logoToDelete);
        }

        $sponsor->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_sponsor',
            'entity_type' => 'Sponsor',
            'entity_id' => (string) $this->selectedSponsorId,
            'description' => "Menghapus sponsor '{$sponsorName}'",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        session()->flash('success', "Sponsor '{$sponsorName}' berhasil dihapus.");

        $this->closeDeleteModal();
    }

    public function toggleStatus(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $sponsor = Sponsor::findOrFail($id);
        $newStatus = ! $sponsor->is_active;

        $sponsor->update([
            'is_active' => $newStatus,
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'toggle_sponsor_status',
            'entity_type' => 'Sponsor',
            'entity_id' => (string) $sponsor->id,
            'description' => "Mengubah status sponsor '{$sponsor->name}' menjadi ".($newStatus ? 'Aktif' : 'Nonaktif'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'is_active' => $newStatus,
            ],
        ]);

        session()->flash('success', "Status sponsor '{$sponsor->name}' diubah menjadi ".($newStatus ? 'Aktif' : 'Nonaktif').'.');
    }

    public function render(): View
    {
        $query = Sponsor::query();

        if (trim($this->search) !== '') {
            $term = trim($this->search);
            $query->where('name', 'like', "%{$term}%");
        }

        $sponsors = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('livewire.admin.sponsors.index', [
            'sponsors' => $sponsors,
        ]);
    }
}
