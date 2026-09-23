<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (Schema::hasTable('eligible_voters')) {
            $defaultProgramId = Schema::hasTable('study_programs')
                ? DB::table('study_programs')->value('id')
                : null;

            DB::table('eligible_voters')
                ->whereNull('name')
                ->update(['name' => 'Tanpa Nama']);

            DB::table('eligible_voters')
                ->whereNull('date_of_birth')
                ->update(['date_of_birth' => '2000-01-01']);

            if ($defaultProgramId) {
                DB::table('eligible_voters')
                    ->whereNull('study_program_id')
                    ->update(['study_program_id' => $defaultProgramId]);
            } else {
                DB::table('eligible_voters')
                    ->whereNull('study_program_id')
                    ->delete();
            }

            Schema::table('eligible_voters', function (Blueprint $table) {
                $table->string('name')->nullable(false)->change();
                $table->date('date_of_birth')->nullable(false)->change();
                $table->foreignId('study_program_id')->nullable(false)->change();
            });
        }
    }
};
