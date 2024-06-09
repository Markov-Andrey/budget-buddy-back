<?php

namespace App\Http\Controllers;

use App\Models\Auto;
use App\Models\Income;
use App\Models\Receipts;
use App\Models\ReceiptsData;
use Illuminate\Http\JsonResponse;
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

    public function personal(): JsonResponse
    {
        $user = auth()->user();
        $id = $user->id;

        $subCategoriesDataProducts = Receipts::calculatePricesBySubcategory($id, 'Продукты');
        $subCategoriesDataAuto = Receipts::calculatePricesBySubcategory($id, 'Автомобиль');
        $subCategoriesDataPermanent = Receipts::calculatePricesBySubcategory($id, 'Постоянные');
        $categoriesData = Receipts::calculatePricesByCategory($id);
        $amountData = Income::calculateByCategory($id);
        $incomeAverage = Income::averageMonthlyLastYear($id);
        $lossAverage = Receipts::averageMonthlyLastYear($id);
        $autoData = Auto::getAutoDataByUserId($id);

        // Соберем всю информацию в массив
        $userInfo = [
            'subCategoriesDataProducts' => $subCategoriesDataProducts,
            'subCategoriesDataAuto' => $subCategoriesDataAuto,
            'subCategoriesDataPermanent' => $subCategoriesDataPermanent,
            'categoriesData' => $categoriesData,
            'amountData' => $amountData,
            'incomeAverage' => $incomeAverage,
            'lossAverage' => $lossAverage,
            'autoData' => $autoData,
        ];

        return response()->json($userInfo);
    }

    /**
     * @OA\Get(
     *      path="/api/info/running-costs",
     *      operationId="getRunningCostsInfo",
     *      tags={"Balance"},
     *      summary="Получить информацию о текущих расходах",
     *      description="Возвращает информацию о текущих расходах пользователя за выбранный месяц.",
     *      @OA\Response(
     *          response=200,
     *          description="Успешный запрос. Возвращает информацию о текущих расходах пользователя.",
     *          @OA\JsonContent(
     *              type="object",
     *              properties={
     *                  @OA\Property(
     *                      property="dailyExpenses",
     *                      type="number",
     *                      description="Сумма расходов за каждый день текущего месяца"
     *                  ),
     *                  @OA\Property(
     *                      property="cumulativeExpensesArray",
     *                      type="array",
     *                      @OA\Items(
     *                          type="number",
     *                          description="Массив кумулятивных расходов за каждый день текущего месяца"
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="incomeAverage",
     *                      type="number",
     *                      description="Средний ежемесячный доход за последний год"
     *                  ),
     *                  @OA\Property(
     *                      property="lossAverage",
     *                      type="number",
     *                      description="Средние ежемесячные расходы за последний год"
     *                  ),
     *                  @OA\Property(
     *                      property="topPriceItem",
     *                      type="number",
     *                      description="Список самых дорогих покупок пользователя за последний месяц"
     *                  )
     *              }
     *          )
     *      )
     * )
     */
    public function runningCosts(): JsonResponse
    {
        $user = auth()->user();
        $id = $user->id;

        $de = Receipts::amountByDayFromDate($id);
        $cea = Receipts::cumulativeAmountByDayFromDate($id);
        $incomeAverage = Income::averageMonthlyLastYear($id);
        $lossAverage = Receipts::averageMonthlyLastYear($id);
        $topPriceItem = ReceiptsData::topItems($id);

        $userInfo = [
            'dailyExpenses' => $de,
            'cumulativeExpensesArray' => $cea,
            'incomeAverage' => $incomeAverage,
            'lossAverage' => $lossAverage,
            'topPriceItem' => $topPriceItem,
        ];

        return response()->json($userInfo);
    }
}
