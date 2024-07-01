<div>
    <div class="flex">
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
        @if($groups)
            <x-moonshine::form.select id="groupSelect" style="width: 150px;">
                <x-slot:options>
                    <option value="0" {{ $group && $group->id == '0' ? 'selected' : '' }}>-</option>
                    @foreach($groups as $item)
                        <option value="{{ $item->id }}" {{ $group && $group->id == $item->id ? 'selected' : '' }}>
                            {{ $item->title }}
                            @php
                                $userNames = $item->groupMemberships->pluck('user.name')->toArray();
                                echo '(' . implode(', ', $userNames) . ')';
                            @endphp
                        </option>
                    @endforeach
                </x-slot:options>
            </x-moonshine::form.select>
        @endif
    </div>
    <hr>
    @if(request()->has('user') || request()->has('group'))
        <div style="border: 1px solid black; border-radius: 12px" class="flex gap-4 p-2">
            <span>
                <div class="text-sm font-medium">Среднемесячный доход:
                    <p>{{ number_format($incomeAverage, 2) }}</p>
                </div>
            </span>
            <span>
                <div class="text-sm font-medium">Среднемесячные траты:
                    <p>{{ number_format($lossAverage, 2) }}</p>
                </div>
            </span>
        </div>
         <x-moonshine::tabs
                :tabs="[
            'income' => 'Доходы',
            'total' => 'Расходы',
            'invest' => 'Инвестиции',
        ]"
                :contents="[
            'income' => view('admin.components.tabs.income', [
                'amountData' => $amountData,
            ])->render(),
            'total' => view('admin.components.tabs.total', [
                'categoriesData' => $categoriesData,
                'autoData' => $autoData,
                'subCategoriesDataAuto' => $subCategoriesDataAuto,
                'subCategoriesData' => $subCategoriesData,
                'subCategoriesDataPermanent' => $subCategoriesDataPermanent,
            ])->render(),
            'invest' => view('admin.components.tabs.invest', [
                'sumInvestmentData' => $sumInvestmentData,
                'investmentData' => $investmentData,
            ])->render(),
        ]"
        />
    @endif
</div>

<script>
    document.getElementById('userSelect').addEventListener('change', function() {
        let selectedValue = this.value;
        let url = new URL(window.location.href);

        if (selectedValue === '0') {
            url.searchParams.delete('user');
            url.searchParams.delete('group');
        } else {
            url.searchParams.delete('group');
            url.searchParams.set('user', selectedValue);
        }

        window.location.href = url.toString();
    });
    document.getElementById('groupSelect').addEventListener('change', function() {
        let selectedValue = this.value;
        let url = new URL(window.location.href);

        if (selectedValue === '0') {
            url.searchParams.delete('user');
            url.searchParams.delete('group');
        } else {
            url.searchParams.delete('user');
            url.searchParams.set('group', selectedValue);
        }

        window.location.href = url.toString();
    });
</script>
