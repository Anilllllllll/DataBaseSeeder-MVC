<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    public function insert()
    {
        Student::create([
            'name' => 'Anil',
            'email' => 'anil@gmail.com',
            'subject' => 'cse'
        ]);

        return "Data inserted successfully";
    }

    public function read()
    {
        $students = Student::all();

        return $students;
    }

    public function update()
    {
        Student::where('id', 1)->update([
            'email' => 'updated@gmail.com'
        ]);

        return "Data updated successfully";
    }

    public function delete()
    {
        Student::where('id', 1)->delete();

        return "Data deleted successfully";
    }
}