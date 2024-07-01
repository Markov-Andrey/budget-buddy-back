<div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Ивестиции</th>
                <th>Количество</th>
                <th>Сумма вложений, $</th>
                <th>Усредненная стоимость за единицу, $</th>
                <th>Текущая цена, $</th>
                <th>На дату</th>
                <th>Цена на текущую дату</th>
                <th>% прибыли/убытков</th>
            </tr>
            </thead>
            <tbody>
            @foreach($investmentData as $item)
                <tr>
                    <td>
                        <span>{{ $item['investment_type_name'] }}</span>
                        @if(!empty($item['investment_type_code']))
                            <span>({{ $item['investment_type_code'] }})</span>
                        @endif
                    </td>
                    <td>{{ $item['total_size'] }}</td>
                    <td>{{ $item['total_value'] }}</td>
                    <td>{{ $item['average_cost_per_unit'] }}</td>
                    <td>{{ $item['latest_price'] }}</td>
                    <td>{{ $item['latest_price_date'] }}</td>
                    <td>{{ $item['latest_amount'] }}</td>
                    <td style="color: {{ $item['latest_percent'] < 0 ? 'red' : 'green' }};">
                        {{ $item['latest_percent'] < 0 ? $item['latest_percent'] : '+' . $item['latest_percent'] }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td>Сумма</td>
                <td>-</td>
                <td>{{$sumInvestmentData}}</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>{{$sumInvestmentCurrentData}}</td>
                <td>-</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
