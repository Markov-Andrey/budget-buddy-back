<?php

namespace App\Http\Controllers;

use App\Models\Receipts;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class ReceiptsController extends Controller
{
    public function store(Request $request)
    {
        if ($request->hasFile('receipt')) {
            $user = $request->user();

            if ($request->file('receipt') instanceof UploadedFile) {
                $receipts = [$request->file('receipt')];
            } else {
                $receipts = $request->file('receipt');
            }

            foreach ($receipts as $receipt) {
                $userDirectory = 'receipts/' . $user->id;
                $imagePath = $receipt->store($userDirectory);

                $image = new Receipts();
                $image->image_path = $imagePath;
                $image->user_id = $user->id;
                $image->processed = false;
                $image->error = false;
                $image->save();
            }

            return response()->json(['message' => 'Receipt(s) uploaded successfully'], 201);
        }

        return response()->json(['message' => 'No receipt uploaded'], 400);
    }
}
