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
        Schema::table('eligible_voters', function (Blueprint $table) {
            $table->index(['study_program_id', 'is_eligible'], 'idx_eligible_voters_program_eligible');
        });

        Schema::table('schedule_change_requests', function (Blueprint $table) {
            $table->index(['election_id', 'status'], 'idx_schedule_requests_election_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedule_change_requests', function (Blueprint $table) {
            $table->dropIndex('idx_schedule_requests_election_status');
        });

        Schema::table('eligible_voters', function (Blueprint $table) {
            $table->dropIndex('idx_eligible_voters_program_eligible');
        });
    }
};
