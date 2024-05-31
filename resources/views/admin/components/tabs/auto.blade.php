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
                    <h2 class="font-bold">{{ $car['car_name'] }}</h2>
                    @if($car['average_consumption'] && $car['date_difference'])
                        <p>Средний расход топлива: {{ number_format($car['average_consumption'], 2) }} л/сут (показатель
                            за последние {{ $car['date_difference'] }} сут.)</p>
                    @else
                        <p>Средний расход топлива: мало данных</p>
                    @endif

                    @if(count($car['receiptFuel']))
                        <h3>Заправки:</h3>
                        @foreach ($car['receiptFuel'] as $fuel)
                            <div>
                                {{ $fuel['datetime'] }} - {{ $fuel['weight'] }} л. - {{ $fuel['fuel_name'] }}
                            </div>
                        @endforeach
                    @endif

                    @if(count($car['receiptInsurances']))
                        <h3>Страховки:</h3>
                        @foreach ($car['receiptInsurances'] as $insurance)
                            <div>
                                с {{ $insurance['datetime'] }} до {{ $insurance['expiry_date'] }}
                            </div>
                        @endforeach
                    @endif

                </div>
            @endforeach
        @else
            <p>Нет данных о машинах.</p>
        @endif
    @endif
</div>
