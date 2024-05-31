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
     *      description="Этот эндпоинт позволяет получить информацию о балансе пользователя, включая среднемесячный доход и среднемесячные траты за последний год.",
     *      security={ {"bearerAuth": {}} },
     *      @OA\Response(
     *          response=200,
     *          description="Успешный ответ",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="income", type="number", format="float", example="1000.00", description="Среднемесячный доход за последний год."),
     *              @OA\Property(property="loss", type="number", format="float", example="500.00", description="Среднемесячные траты за последний год."),
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
