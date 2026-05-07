<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('students')->insert([
        //     [
        //         'name' => 'A',
        //         'email' => 'abc@gmail.com',
        //         'subject' => 'cse'
        //     ],
        //     [
        //         'name' => 'B',
        //         'email' => 'def@gmail.com',
        //         'subject' => 'AI'
        //     ],
        //     [
        //         'name' => 'C',
        //         'email' => 'ghi@gmail.com',
        //         'subject' => 'ece'
        //     ]
        // ]);
        for ($i = 1; $i <= 10; $i++) {

    DB::table('students')->insert([
        'name' => 'Student' . rand(100, 999),
        'email' => 'student' . rand(100, 999) . '@gmail.com',
        'subject' => 'course' . rand(1, 99)
    ]);

}

    }
}