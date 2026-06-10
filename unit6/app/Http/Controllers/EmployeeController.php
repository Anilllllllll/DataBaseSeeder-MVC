<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function show()
    {
        return view('studentform');
    }

    public function insert(Request $request)
    {
        DB::table('employees')->insert([
            'name' => $request->name,
            'email' => $request->email
        ]);

        return "Data Inserted Successfully";
    }

    public function read()
    {
        $data = DB::table('employees')->get();

        return view('read', compact('data'));
    }

    public function edit($id)
    {
        $data1 = DB::table('employees')
                    ->where('id', $id)
                    ->first();

        return view('edit', compact('data1'));
    }
}