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
            if (! Schema::hasIndex('eligible_voters', 'idx_eligible_voters_eligible_program')) {
                $table->index(['is_eligible', 'study_program_id'], 'idx_eligible_voters_eligible_program');
            }
        });

        Schema::table('schedule_change_requests', function (Blueprint $table) {
            if (! Schema::hasIndex('schedule_change_requests', 'idx_schedule_requests_status_election')) {
                $table->index(['status', 'election_id'], 'idx_schedule_requests_status_election');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedule_change_requests', function (Blueprint $table) {
            if (Schema::hasIndex('schedule_change_requests', 'idx_schedule_requests_status_election')) {
                $table->dropIndex('idx_schedule_requests_status_election');
            }

            if (Schema::hasIndex('schedule_change_requests', 'idx_schedule_requests_election_status')) {
                if (! Schema::hasIndex('schedule_change_requests', 'schedule_change_requests_election_id_foreign')) {
                    $table->index('election_id', 'schedule_change_requests_election_id_foreign');
                }
                $table->dropIndex('idx_schedule_requests_election_status');
            }
        });

        Schema::table('eligible_voters', function (Blueprint $table) {
            if (Schema::hasIndex('eligible_voters', 'idx_eligible_voters_eligible_program')) {
                $table->dropIndex('idx_eligible_voters_eligible_program');
            }

            if (Schema::hasIndex('eligible_voters', 'idx_eligible_voters_program_eligible')) {
                if (! Schema::hasIndex('eligible_voters', 'eligible_voters_study_program_id_foreign')) {
                    $table->index('study_program_id', 'eligible_voters_study_program_id_foreign');
                }
                $table->dropIndex('idx_eligible_voters_program_eligible');
            }
        });
    }
};
