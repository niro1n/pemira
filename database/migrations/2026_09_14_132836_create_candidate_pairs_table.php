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
        Schema::create('candidate_pairs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('election_id')
                ->constrained('elections')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('candidate_number');

            $table->string('photo')->nullable();

            $table->text('vision');
            $table->text('mission');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique([
                'election_id',
                'candidate_number',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_pairs');
    }
};
