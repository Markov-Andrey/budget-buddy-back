<div>
    @if($user)
        @if (!empty($autoData))
            <div>
                {!!
                MoonShine\Metrics\DonutChartMetric::make('Общее по авто')
                    ->values($subCategoriesDataAuto['details'])
                !!}
            </div>
            @foreach ($autoData as $car)
                <div style="margin-bottom: 20px;">
                    <h2 class="font-bold m-1" style="font-size: 25px;">{{ $car['car_name'] }}</h2>
                    @if($car['average_consumption'] && $car['date_difference'])
                        <p>Средний расход топлива: {{ number_format($car['average_consumption'], 2) }} л/сут (показатель
                            за последние {{ $car['date_difference'] }} сут.)</p>
                    @else
                        <p>Средний расход топлива: мало данных</p>
                    @endif

                    @if(count($car['receiptFuel']))
                        <h3 class="font-bold">Заправки:</h3>
                        @foreach ($car['receiptFuel'] as $fuel)
                            <div>
                                {{ $fuel['datetime'] }} - {{ $fuel['weight'] }} л. - {{ $fuel['fuel_name'] }}
                            </div>
                        @endforeach
                    @endif

                    @if(count($car['receiptInsurances']))
                        <h3 class="font-bold">Страховки:</h3>
                        @foreach ($car['receiptInsurances'] as $insurance)
                            <div>
                                с {{ $insurance['datetime'] }} до {{ $insurance['expiry_date'] }}
                            </div>
                        @endforeach
                    @endif

                    @if(count($car['receiptTechInspections']))
                        <h3 class="font-bold">Плановое ТО:</h3>
                        <table>
                            <thead>
                            <tr>
                                <th>Название</th>
                                <th>Количество</th>
                                <th>Объем/вес</th>
                                <th>Цена</th>
                                <th>Пробег</th>
                                <th>Следующее ТО</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($car['receiptTechInspections'] as $techInspection)
                                <tr>
                                    <td>{{ $techInspection['name'] }}</td>
                                    <td>{{ $techInspection['quantity'] }}</td>
                                    <td>{{ $techInspection['weight'] }}</td>
                                    <td>{{ $techInspection['price'] }}</td>
                                    <td>{{ $techInspection['techInspection'] }}</td>
                                    <td>{{ $techInspection['nextTechInspection'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif

                </div>
            @endforeach
        @else
            <p>Нет данных о машинах.</p>
        @endif
    @endif
</div>
