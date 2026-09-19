<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
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

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function voterAccount(): HasOne
    {
        return $this->hasOne(VoterAccount::class);
    }

    /**
     * @return HasMany<CandidateMember, $this>
     */
    public function candidateMembers(): HasMany
    {
        return $this->hasMany(CandidateMember::class);
    }
}
