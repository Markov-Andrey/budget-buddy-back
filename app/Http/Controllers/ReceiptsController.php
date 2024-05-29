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

    /**
     * @OA\Post(
     *      path="/api/receipts/show",
     *      operationId="getReceipts",
     *      tags={"Receipts"},
     *      summary="Получить список чеков пользователя",
     *      description="Этот эндпоинт возвращает список чеков пользователя.",
     *      security={ {"bearerAuth": {}} },
     *      @OA\Response(
     *          response=200,
     *          description="Успешный ответ",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="current_page", type="integer"),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Receipt")),
     *              @OA\Property(property="first_page_url", type="string"),
     *              @OA\Property(property="from", type="integer"),
     *              @OA\Property(property="last_page", type="integer"),
     *              @OA\Property(property="last_page_url", type="string"),
     *              @OA\Property(property="next_page_url", type="string", nullable=true),
     *              @OA\Property(property="path", type="string"),
     *              @OA\Property(property="per_page", type="integer"),
     *              @OA\Property(property="prev_page_url", type="string", nullable=true),
     *              @OA\Property(property="to", type="integer"),
     *              @OA\Property(property="total", type="integer"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Неавторизованный запрос",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      )
     * )
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        $receipts = Receipts::with('data', 'data.subcategory')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return response()->json($receipts);
    }

    /**
     * @OA\Delete(
     *      path="/api/receipts/delete/{id}",
     *      operationId="deleteReceipt",
     *      tags={"Receipts"},
     *      summary="Удалить чек",
     *      description="Этот эндпоинт позволяет удалить чек пользователя по его ID.",
     *      security={ {"bearerAuth": {}} },
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID чека для удаления",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешный ответ",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Receipt deleted successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Чек не найден",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Receipt not found or does not belong to the current user")
     *          )
     *      )
     * )
     */
    public function delete($id)
    {
        $receipt = Receipts::where('id', $id)
            ->first();

        if (!$receipt) {
            return response()->json(['error' => 'Чек не найден'], 404);
        }

        $receipt->delete();

        return response()->json(['message' => 'Receipt deleted successfully'], 200);
    }
}
