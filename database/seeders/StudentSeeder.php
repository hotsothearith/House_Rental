<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('students')->insert([
            ['id' => 1, 'name' => 'Hot Sothearith', 'age' => 20],
            ['id' => 2, 'name' => 'Dara Rith', 'age' => 22],
        ]);
    }
}
