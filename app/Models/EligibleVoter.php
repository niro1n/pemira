<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['nim', 'name', 'date_of_birth', 'study_program_id', 'is_eligible'])]
class EligibleVoter extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_eligible' => 'boolean',
        ];
    }

    public function isComplete(): bool
    {
        return ! empty($this->nim)
            && ! empty(trim((string) $this->name))
            && ! empty($this->study_program_id)
            && ! empty($this->date_of_birth);
    }

    public function missingFields(): array
    {
        $missing = [];

        if (empty($this->nim)) {
            $missing[] = 'Nomor Induk Mahasiswa (NIM)';
        }

        if (empty(trim((string) $this->name))) {
            $missing[] = 'Nama Lengkap';
        }

        if (empty($this->study_program_id)) {
            $missing[] = 'Jurusan';
        }

        if (empty($this->date_of_birth)) {
            $missing[] = 'Tanggal Lahir';
        }

        return $missing;
    }

    public function scopeComplete(Builder $query): Builder
    {
        return $query->whereNotNull('name')
            ->where('name', '!=', '')
            ->whereNotNull('study_program_id')
            ->whereNotNull('date_of_birth');
    }

    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('name')
                ->orWhere('name', '')
                ->orWhereNull('study_program_id')
                ->orWhereNull('date_of_birth');
        });
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function voterAccount(): HasOne
    {
        return $this->hasOne(VoterAccount::class);
    }

    public function candidateMembers(): HasMany
    {
        return $this->hasMany(CandidateMember::class);
    }
}
