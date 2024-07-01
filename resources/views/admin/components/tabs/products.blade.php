<div>
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
</div>
