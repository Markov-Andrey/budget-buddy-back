<div>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div>
            {!!
            MoonShine\Metrics\DonutChartMetric::make('Постоянные расходы')
                ->values($subCategoriesDataPermanent['details'])
            !!}
        </div>
        <div>
            <table>
                <thead>
                <td>Субкатегория</td>
                <td>Цена</td>
                <td>Доля</td>
                </thead>
                @foreach($subCategoriesDataPermanent['details'] as $key => $value)
                    <tr>
                        <td>{{ $key }}</td>
                        <td>{{ number_format($value, 2, '.', '') }}</td>
                        <td>{{ number_format($value / $subCategoriesDataPermanent['total'] * 100, 2, '.', '') }}</td>
                    </tr>
                @endforeach
                <tfoot>
                <td>Всего</td>
                <td>{{ $subCategoriesDataPermanent['total'] }}</td>
                <td>100</td>
                </tfoot>
            </table>
        </div>
    </div>
</div>
