<div>
    @if($user)
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
    @endif
</div>
