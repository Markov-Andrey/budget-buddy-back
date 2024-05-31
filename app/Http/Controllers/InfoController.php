<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Receipts;
use Illuminate\Support\Facades\Auth;

class InfoController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/info/balance",
     *      operationId="getBalanceInfo",
     *      tags={"Balance"},
     *      summary="Получить информацию о балансе пользователя",
     *      description="Этот эндпоинт позволяет получить информацию о балансе пользователя, включая доход, расход и текущий баланс.",
     *      security={ {"bearerAuth": {}} },
     *      @OA\Response(
     *          response=200,
     *          description="Успешный ответ",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="income", type="number", example="1000.00"),
     *              @OA\Property(property="loss", type="number", example="500.00"),
     *              @OA\Property(property="balance", type="number", example="500.00"),
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
    public function balance()
    {
        $user = Auth::user();
        $incomeAverage = Income::averageMonthlyLastYear($user->id);
        $lossAverage = Receipts::averageMonthlyLastYear($user->id);

        return response()->json([
            'income' => $incomeAverage,
            'loss' => $lossAverage,
        ], 200);
    }
}
