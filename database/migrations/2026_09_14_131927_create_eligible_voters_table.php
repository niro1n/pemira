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
        Schema::create('eligible_voters', function (Blueprint $table) {
            $table->id();

            $table->string('nim', 30)->unique();
            $table->string('name');

            $table->date('date_of_birth');

            $table->foreignId('study_program_id')
                ->constrained('study_programs')
                ->restrictOnDelete();

            $table->boolean('is_eligible')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eligible_voters');
    }
};
