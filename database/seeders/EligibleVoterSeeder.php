<?php

namespace Database\Seeders;

use App\Models\EligibleVoter;
use App\Models\StudyProgram;
use App\Models\VoterAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class EligibleVoterSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        VoterAccount::truncate();
        EligibleVoter::truncate();
        StudyProgram::truncate();
        Schema::enableForeignKeyConstraints();

        $departments = [
            'AB' => StudyProgram::create([
                'code' => 'AB',
                'name' => 'Administrasi Bisnis',
            ]),
            'AK' => StudyProgram::create([
                'code' => 'AK',
                'name' => 'Akuntansi',
            ]),
            'PAR' => StudyProgram::create([
                'code' => 'PAR',
                'name' => 'Pariwisata',
            ]),
            'TM' => StudyProgram::create([
                'code' => 'TM',
                'name' => 'Teknik Mesin',
            ]),
            'TE' => StudyProgram::create([
                'code' => 'TE',
                'name' => 'Teknik Elektro',
            ]),
            'TS' => StudyProgram::create([
                'code' => 'TS',
                'name' => 'Teknik Sipil',
            ]),
            'TI' => StudyProgram::create([
                'code' => 'TI',
                'name' => 'Teknologi Informasi',
            ]),
        ];

        $voters = [
            [
                'nim' => '2215354001',
                'name' => 'I Putu Gede Raditya',
                'date_of_birth' => '2004-03-15',
                'study_program_id' => $departments['TI']->id,
                'is_eligible' => true,
            ],
            [
                'nim' => '2215644020',
                'name' => 'Kadek Dimas Prasetya',
                'date_of_birth' => '2004-05-10',
                'study_program_id' => $departments['AK']->id,
                'is_eligible' => true,
            ],
            [
                'nim' => '2215744030',
                'name' => 'Putu Sintya Dewi',
                'date_of_birth' => '2004-12-01',
                'study_program_id' => $departments['AB']->id,
                'is_eligible' => true,
            ],
            [
                'nim' => '2215834012',
                'name' => 'Ni Kadek Ayu Lestari',
                'date_of_birth' => '2004-07-22',
                'study_program_id' => $departments['PAR']->id,
                'is_eligible' => true,
            ],
            [
                'nim' => '2215124018',
                'name' => 'I Made Arya Pratama',
                'date_of_birth' => '2003-11-05',
                'study_program_id' => $departments['TM']->id,
                'is_eligible' => true,
            ],
            [
                'nim' => '2215234025',
                'name' => 'I Ketut Wahyu Saputra',
                'date_of_birth' => '2005-09-30',
                'study_program_id' => $departments['TE']->id,
                'is_eligible' => true,
            ],
            [
                'nim' => '2315014032',
                'name' => 'Ni Nyoman Dwi Astuti',
                'date_of_birth' => '2005-01-18',
                'study_program_id' => $departments['TS']->id,
                'is_eligible' => true,
            ],
            [
                'nim' => '2115354099',
                'name' => 'I Wayan Nonaktif',
                'date_of_birth' => '2002-08-14',
                'study_program_id' => $departments['TI']->id,
                'is_eligible' => false,
            ],
        ];

        foreach ($voters as $voterData) {
            EligibleVoter::create($voterData);
        }
    }
}
