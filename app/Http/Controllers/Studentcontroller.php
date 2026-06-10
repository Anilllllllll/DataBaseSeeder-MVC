<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function insert()
    {
        DB::table('students')->insert([
            'name' => 'anil',
            'email' => 'anil123@gmail.com',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return "Data inserted successfully. Check in phpMyAdmin.";
    }

    public function update()
    {
        DB::table('students')
            ->where('id', 2)
            ->update([
                'email' => 'abc@gmail.com',
                'updated_at' => Carbon::now()
            ]);

        return "Data updated successfully.";
    }

    public function delete()
    {
        DB::table('students')
            ->where('id', 2)
            ->delete();

        return "Data deleted successfully.";
    }
}