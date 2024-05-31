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
    @if($users)
        <div style="border: 1px solid black; border-radius: 12px" class="flex gap-4 p-2">
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
    @endif
    <x-moonshine::tabs
        :tabs="[
            'income' => 'Доходы',
            'total' => 'Расходы',
        ]"
        :contents="[
            'income' => view('admin.components.tabs.income', ['amountData' => $amountData, 'user' => $user])->render(),
            'total' => view('admin.components.tabs.total', [
                'categoriesData' => $categoriesData,
                'autoData' => $autoData,
                'subCategoriesDataAuto' => $subCategoriesDataAuto,
                'subCategoriesData' => $subCategoriesData,
                'user' => $user
            ])->render(),
        ]"
    />
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
