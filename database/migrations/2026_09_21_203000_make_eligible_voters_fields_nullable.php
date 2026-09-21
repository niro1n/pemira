<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eligible_voters', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->foreignId('study_program_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('eligible_voters', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->date('date_of_birth')->nullable(false)->change();
            $table->foreignId('study_program_id')->nullable(false)->change();
        });
    }
};
