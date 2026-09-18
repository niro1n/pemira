<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidate_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('election_id')
                ->constrained('elections')
                ->cascadeOnDelete();

            $table->foreignId('candidate_pair_id')
                ->constrained('candidate_pairs')
                ->cascadeOnDelete();

            $table->foreignId('eligible_voter_id')
                ->constrained('eligible_voters')
                ->restrictOnDelete();

            $table->enum('position', [
                'ketua',
                'wakil',
            ]);

            $table->timestamps();

            $table->unique([
                'candidate_pair_id',
                'position',
            ]);

            $table->unique([
                'candidate_pair_id',
                'eligible_voter_id',
            ]);

            $table->unique([
                'election_id',
                'eligible_voter_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_members');
    }
};
