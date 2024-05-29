<?php

namespace App\Http\Controllers;

use App\Models\Receipts;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class ReceiptsController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/receipts/add",
     *     summary="Upload receipts",
     *     security={{"bearer_token":{}}},
     *     tags={"Receipts"},
     *     description="Метод для загрузки и сохранения квитанций.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"receipt"},
     *                 @OA\Property(
     *                     property="receipt",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary"),
     *                     description="Массив файлов квитанций для загрузки"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Receipt(s) uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Receipt(s) uploaded successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No receipt uploaded",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No receipt uploaded")
     *         )
     *     ),
     * )
     */
    public function store(Request $request)
    {
        if ($request->hasFile('receipt')) {
            $user = Auth::user();

            if ($request->file('receipt') instanceof UploadedFile) {
                $receipts = [$request->file('receipt')];
            } else {
                $receipts = $request->file('receipt');
            }

            foreach ($receipts as $receipt) {
                $userDirectory = 'public/receipts';
                $imagePath = $receipt->store($userDirectory);

                $image = new Receipts();
                $image->image_path = str_replace('public/', '', $imagePath);
                $image->user_id = $user->id;
                $image->processed = false;
                $image->error = false;
                $image->save();
            }

            return response()->json(['message' => 'Receipt(s) uploaded successfully'], 201);
        }

        return response()->json(['message' => 'No receipt uploaded'], 400);
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        $receipts = Receipts::with('data', 'data.subcategory')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return response()->json($receipts);
    }

    public function delete($id)
    {
        $receipt = Receipts::where('id', $id)
            ->first();

        if (!$receipt) {
            return response()->json(['error' => 'Receipt not found or does not belong to the current user'], 404);
        }

        $receipt->delete();

        return response()->json(['message' => 'Receipt deleted successfully'], 200);
    }
}
