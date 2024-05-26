<div>
    @if($users)
        <select id="userSelect">
            <option value="0" {{ $user && $user->id == '0' ? 'selected' : '' }}>-</option>
            @foreach($users as $item)
                <option value="{{ $item->id }}" {{ $user && $user->id == $item->id ? 'selected' : '' }}>
                    {{ $item->name }}
                </option>
            @endforeach
        </select>
    @endif
    @if($user)
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px">
            <div>
                {!!
                MoonShine\Metrics\DonutChartMetric::make('Категории')
                    ->values($categoriesData)
                !!}
            </div>
            <div>
                <table>
                    <thead>
                    <td>Категория</td>
                    <td>Цена</td>
                    <td>Доля</td>
                    </thead>
                    @foreach($categoriesData as $key => $value)
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{ number_format($value, 2, '.', '') }}</td>
                            <td>{{ number_format($value / $dataTotal * 100, 2, '.', '') }}</td>
                        </tr>
                    @endforeach
                    <tfoot>
                    <td>Всего</td>
                    <td>{{ $dataTotal }}</td>
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
                    ->values($data)
                !!}
            </div>
            <div>
                <table>
                    <thead>
                        <td>Субкатегория</td>
                        <td>Цена</td>
                        <td>Доля</td>
                    </thead>
                @foreach($data as $key => $value)
                    <tr>
                        <td>{{ $key }}</td>
                        <td>{{ number_format($value, 2, '.', '') }}</td>
                        <td>{{ number_format($value / $dataTotal * 100, 2, '.', '') }}</td>
                    </tr>
                @endforeach
                    <tfoot>
                        <td>Всего</td>
                        <td>{{ $dataTotal }}</td>
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
