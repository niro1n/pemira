<?php

namespace Database\Seeders;

use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

class EligibleVoterSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'code' => 'AB',
                'name' => 'Administrasi Bisnis',
            ],
            [
                'code' => 'AK',
                'name' => 'Akuntansi',
            ],
            [
                'code' => 'PAR',
                'name' => 'Pariwisata',
            ],
            [
                'code' => 'TM',
                'name' => 'Teknik Mesin',
            ],
            [
                'code' => 'TE',
                'name' => 'Teknik Elektro',
            ],
            [
                'code' => 'TS',
                'name' => 'Teknik Sipil',
            ],
            [
                'code' => 'TI',
                'name' => 'Teknologi Informasi',
            ],
        ];

        foreach ($departments as $department) {
            StudyProgram::updateOrCreate(
                ['code' => $department['code']],
                ['name' => $department['name']]
            );
        }
    }
}
