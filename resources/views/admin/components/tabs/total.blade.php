<div>
    <x-moonshine::tabs
        :tabs="[
            'loss' => 'Общее',
            'auto' => 'Авто',
            'products' => 'Продукты',
            'permanents' => 'Постоянные расходы',
        ]"
        :contents="[
            'loss' => view('admin.components.tabs.loss', ['categoriesData' => $categoriesData])->render(),
            'auto' => view('admin.components.tabs.auto', ['autoData' => $autoData, 'subCategoriesDataAuto' => $subCategoriesDataAuto])->render(),
            'products' => view('admin.components.tabs.products', ['subCategoriesData' => $subCategoriesData])->render(),
            'permanents' => view('admin.components.tabs.permanents', ['subCategoriesDataPermanent' => $subCategoriesDataPermanent])->render(),
        ]"
    />
</div>
