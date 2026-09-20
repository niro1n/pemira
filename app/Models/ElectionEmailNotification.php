<?php

namespace App\Models;

use App\Enums\ElectionNotificationType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['election_id', 'user_id', 'type', 'sent_at'])]
class ElectionEmailNotification extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => ElectionNotificationType::class,
            'sent_at' => 'datetime',
        ];
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
