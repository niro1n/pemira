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
        Schema::create('ballots', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignId('election_id')
                ->constrained('elections')
                ->restrictOnDelete();

            $table->foreignId('candidate_pair_id')
                ->constrained('candidate_pairs')
                ->restrictOnDelete();

            $table->index([
                'election_id',
                'candidate_pair_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ballots');
    }
};
