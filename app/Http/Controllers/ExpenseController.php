<?php

namespace App\Http\Controllers;


use App\Jobs\AttachImageToExpenseJob;
use App\Jobs\CreateExpenseFromImageJob;
use App\Models\Expense;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'description' => 'required',
            'amount' => 'required|numeric',
        ]);

        $total_amount = round($request->amount, 2) * 100;

        $user = $request->user();

        $expense = $user->expenses()->create(['description' => $request->description, 'total_amount' => $total_amount, 'user_id' => $user->id]);

        return response()->json($expense);
    }

    public function attachImageToId(Request $request, string $id)
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
            throw new BadRequestException("Failed to process image");
        }

        $path = $request->file('image')->store('images', 'local');

        $expense->image_path = $path;

        $expense->save();

        AttachImageToExpenseJob::dispatch($expense->id);

        return response()->json($expense);
    }

    public function createExpenseFromImage(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file types and max size as needed
            'description' => 'nullable|string',
        ]);

        if (!$request->hasFile('image')) {
            throw new BadRequestException("Failed to process image");
        }

        $image_path = $request->file('image')->store('images', 'local');

        $expense = $user->expenses()->create(['description' => $request->description, 'image_path' => $image_path]);

        CreateExpenseFromImageJob::dispatch($expense->id);

        return response()->json($expense);

    }

    public function getExpenseById(Request $request, string $id)
    {
        $expense = Expense::findOrFail($id);
        return response()->json($expense);
    }

}
