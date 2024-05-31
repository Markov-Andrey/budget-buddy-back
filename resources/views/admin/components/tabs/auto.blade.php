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
                    <h2>{{ $car['car_name'] }}</h2>
                    @if($car['average_consumption'] && $car['date_difference'])
                        <p>Средний расход топлива: {{ number_format($car['average_consumption'], 2) }} л/сут (показатель
                            за последние {{ $car['date_difference'] }} сут.)</p>
                    @else
                        <p>Средний расход топлива: мало данных</p>
                    @endif
                    <h3>Заправки:</h3>
                    @foreach ($car['receiptFuel'] as $fuel)
                        <div>
                            {{ $fuel['datetime'] }} - {{ $fuel['weight'] }} л. - {{ $fuel['fuel_name'] }}
                        </div>
                    @endforeach
                </div>
            @endforeach
        @else
            <p>Нет данных о машинах.</p>
        @endif
    @endif
</div>
