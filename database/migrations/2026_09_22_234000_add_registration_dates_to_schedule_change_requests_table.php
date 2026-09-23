<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_change_requests', function (Blueprint $table) {
            $table->timestamp('old_registration_start_at')->nullable()->after('approved_by');
            $table->timestamp('old_registration_end_at')->nullable()->after('old_registration_start_at');
            $table->timestamp('new_registration_start_at')->nullable()->after('old_registration_end_at');
            $table->timestamp('new_registration_end_at')->nullable()->after('new_registration_start_at');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_change_requests', function (Blueprint $table) {
            $table->dropColumn([
                'old_registration_start_at',
                'old_registration_end_at',
                'new_registration_start_at',
                'new_registration_end_at',
            ]);
        });
    }
};
