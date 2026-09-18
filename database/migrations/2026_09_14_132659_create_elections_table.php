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
        Schema::create('elections', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('year');

            $table->timestamp('registration_start_at');
            $table->timestamp('registration_end_at');

            $table->timestamp('voting_start_at');
            $table->timestamp('voting_end_at');

            $table->timestamps();

            $table->index(['voting_start_at', 'voting_end_at']);
            $table->index(['registration_start_at', 'registration_end_at']);
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elections');
    }
};
