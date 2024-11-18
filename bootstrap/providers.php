<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\RouteServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,
    App\Providers\SettingServiceProvider::class,

    Spatie\Permission\PermissionServiceProvider::class,
    Spatie\TranslationLoader\TranslationServiceProvider::class,
];
