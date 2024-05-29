<div>
    @if($users)
        <x-moonshine::form.select id="userSelect" style="width: 150px;">
            <x-slot:options>
                <option value="0" {{ $user && $user->id == '0' ? 'selected' : '' }}>-</option>
                @foreach($users as $item)
                    <option value="{{ $item->id }}" {{ $user && $user->id == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </x-slot:options>
        </x-moonshine::form.select>
    @endif
    <hr>
    @if($user)
        <div>
            @if (!empty($autoData))
                <div class="font-bold">Авто-Блок</div>
                @foreach ($autoData as $car)
                    <div style="margin-bottom: 20px;">
                        <h2>{{ $car['car_name'] }}</h2>
                        @if($car['average_consumption'] && $car['date_difference'])
                            <p>Средний расход топлива: {{ number_format($car['average_consumption'], 2) }} л/сут (показатель за последние {{ $car['date_difference'] }} сут.)</p>
                        @else
                            <p>Средний расход топлива: мало данных</p>

                        @endif
                        <h3>Заправки:</h3>
                            @foreach ($car['receipts'] as $receipt)
                                <div>{{ $receipt['fuel_name'] }} - {{ $receipt['weight'] }} л. ({{ $receipt['datetime'] }})</div>
                            @endforeach
                    </div>
                @endforeach
            @else
                <p>Нет данных о машинах.</p>
            @endif
        </div>
        <div class="flex gap-4">
            <span class="mr-4">
                <div class="text-sm font-medium">Income:</div>
                <div style="color:#00b000;" class="text-lg font-semibold">{{ $income }}</div>
            </span>
            <span>
                <div class="text-sm font-medium">Loss:</div>
                <div style="color:#f10000;" class="text-lg font-semibold">{{ $loss }}</div>
            </span>
            <span>
                <div class="text-sm font-medium">Balance:</div>
                    @if($balance > 0)
                        <div style="color:#00b000;" class="text-lg font-semibold">+{{ number_format($balance, 2) }}</div>
                    @else
                        <div style="color:#f10000;" class="text-lg font-semibold">{{ number_format($balance, 2) }}</div>
                    @endif
            </span>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px">
            <div>
                {!!
                MoonShine\Metrics\DonutChartMetric::make('Доходы')
                    ->values($amountData['details'])
                !!}
            </div>
            <div>
                <table>
                    <thead>
                    <td>Категория</td>
                    <td>Цена</td>
                    <td>Доля</td>
                    </thead>
                    @foreach($amountData['details'] as $key => $value)
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{ number_format($value, 2, '.', '') }}</td>
                            <td>{{ number_format($value / $amountData['total'] * 100, 2, '.', '') }}</td>
                        </tr>
                    @endforeach
                    <tfoot>
                    <td>Всего</td>
                    <td>{{ $amountData['total'] }}</td>
                    <td>100</td>
                    </tfoot>
                </table>
            </div>
        </div>

        <hr>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px">
            <div>
                {!!
                MoonShine\Metrics\DonutChartMetric::make('Категории')
                    ->values($categoriesData['details'])
                !!}
            </div>
            <div>
                <table>
                    <thead>
                    <td>Категория</td>
                    <td>Цена</td>
                    <td>Доля</td>
                    </thead>
                    @foreach($categoriesData['details'] as $key => $value)
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{ number_format($value, 2, '.', '') }}</td>
                            <td>{{ number_format($value / $categoriesData['total'] * 100, 2, '.', '') }}</td>
                        </tr>
                    @endforeach
                    <tfoot>
                    <td>Всего</td>
                    <td>{{ $categoriesData['total'] }}</td>
                    <td>100</td>
                    </tfoot>
                </table>
            </div>
        </div>

        <hr>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                {!!
                MoonShine\Metrics\DonutChartMetric::make('Продукты')
                    ->values($subCategoriesData['details'])
                !!}
            </div>
            <div>
                <table>
                    <thead>
                        <td>Субкатегория</td>
                        <td>Цена</td>
                        <td>Доля</td>
                    </thead>
                @foreach($subCategoriesData['details'] as $key => $value)
                    <tr>
                        <td>{{ $key }}</td>
                        <td>{{ number_format($value, 2, '.', '') }}</td>
                        <td>{{ number_format($value / $subCategoriesData['total'] * 100, 2, '.', '') }}</td>
                    </tr>
                @endforeach
                    <tfoot>
                        <td>Всего</td>
                        <td>{{ $subCategoriesData['total'] }}</td>
                        <td>100</td>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
</div>

<script>
    document.getElementById('userSelect').addEventListener('change', function() {
        let selectedValue = this.value;
        let url = new URL(window.location.href);

        if (selectedValue === '0') {
            url.searchParams.delete('user');
        } else {
            url.searchParams.set('user', selectedValue);
        }

        window.location.href = url.toString();
    });
</script>
