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
    @if(request()->has('user'))
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
    @endif
    @if(request()->has('user'))
            <x-moonshine::tabs
                :tabs="[
            'income' => 'Доходы',
            'total' => 'Расходы',
            'invest' => 'Инвестиции',
        ]"
                :contents="[
            'income' => view('admin.components.tabs.income', ['amountData' => $amountData, 'user' => $user])->render(),
            'total' => view('admin.components.tabs.total', [
                'categoriesData' => $categoriesData,
                'autoData' => $autoData,
                'subCategoriesDataAuto' => $subCategoriesDataAuto,
                'subCategoriesData' => $subCategoriesData,
                'subCategoriesDataPermanent' => $subCategoriesDataPermanent,
                'user' => $user
            ])->render(),
            'invest' => view('admin.components.tabs.invest', ['investmentData' => $investmentData, 'user' => $user])->render(),
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
        } else {
            url.searchParams.set('user', selectedValue);
        }

        window.location.href = url.toString();
    });
</script>
