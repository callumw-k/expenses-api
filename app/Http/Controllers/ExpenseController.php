<?php

namespace App\Http\Controllers;


use App\Jobs\ProcessOcr;
use App\Models\Expense;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'amount' => 'required|numeric',
        ]);


        $user = $request->user();

        $expense = $user->expenses()->create(['name' => $request->name, 'amount' => $request->amount, 'user_id' => $user->id]);

        return response()->json($expense);
    }

    public function uploadImage(Request $request, string $id)
    {
        $user = $request->user();
        $expense = Expense::findOrFail($id);


        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file types and max size as needed
        ]);

        if ($user->id !== $expense->user_id) {
            throw new AccessDeniedHttpException();
        }

        if (!$request->hasFile('image')) {
            return response()->json(['status' => 'not_saved']);
        }

        $path = $request->file('image')->store('images', 'local');
        $expense->file_path = $path;
        $expense->save();
        ProcessOcr::dispatch($expense->id);

        return response()->json(['status' => 'saved']);
    }
}
