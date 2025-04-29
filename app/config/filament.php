<?php

return [
    'auth' => [
        'guard' => 'filament',
    ],
    
    'middleware' => [
        'auth' => [
            'base' => 'auth:filament',
        ],
    ],
    
    'default_filesystem_disk' => 'public',
    
    'livewire_loading_delay' => 'default',
    
    'models' => [
        'user' => \App\Models\AdminUser::class,
    ],
];