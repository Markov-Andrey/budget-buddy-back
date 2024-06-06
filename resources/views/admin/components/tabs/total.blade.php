<div>
    <x-moonshine::tabs
        :tabs="[
            'loss' => 'Общее',
            'auto' => 'Авто',
            'products' => 'Продукты',
            'permanents' => 'Постоянные расходы',
        ]"
        :contents="[
            'loss' => view('admin.components.tabs.loss', ['categoriesData' => $categoriesData, 'user' => $user])->render(),
            'auto' => view('admin.components.tabs.auto', ['autoData' => $autoData, 'user' => $user, 'subCategoriesDataAuto' => $subCategoriesDataAuto])->render(),
            'products' => view('admin.components.tabs.products', ['subCategoriesData' => $subCategoriesData, 'user' => $user])->render(),
            'permanents' => view('admin.components.tabs.permanents', ['subCategoriesDataPermanent' => $subCategoriesDataPermanent, 'user' => $user])->render(),
        ]"
    />
</div>
