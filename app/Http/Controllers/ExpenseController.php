<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'amount' => 'required|numeric'
        ]);


        $user = $request->user();
        $expense = $user->expenses()->create($validatedData);

        return response()->json($expense);
    }
}
