<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_missions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('candidate_pair_id')
                ->constrained('candidate_pairs')
                ->cascadeOnDelete();

            $table->text('content');
            $table->unsignedSmallInteger('sort_order')->default(1);

            $table->timestamps();

            $table->index(['candidate_pair_id', 'sort_order']);
        });

        if (Schema::hasTable('candidate_pairs') && Schema::hasColumn('candidate_pairs', 'mission')) {
            Schema::table('candidate_pairs', function (Blueprint $table) {
                $table->text('mission')->nullable()->change();
            });

            $existingPairs = DB::table('candidate_pairs')
                ->whereNotNull('mission')
                ->where('mission', '!=', '')
                ->get();

            foreach ($existingPairs as $pair) {
                DB::table('candidate_missions')->insert([
                    'candidate_pair_id' => $pair->id,
                    'content' => $pair->mission,
                    'sort_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_missions');
    }
};
