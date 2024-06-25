<div>
    @if($user)
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Ивестиции</th>
                    <th>Количество</th>
                    <th>Сумма вложений, $</th>
                    <th>Усредненная стоимость за единицу, $</th>
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
                    </tr>
                @endforeach
                <tr>
                    <td>Сумма</td>
                    <td>-</td>
                    <td>{{$sumInvestmentData}}</td>
                    <td>-</td>
                </tr>
                </tbody>
            </table>
        </div>
    @endif
</div>
