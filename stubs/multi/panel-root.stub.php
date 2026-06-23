<?php

declare(strict_types=1);

/*
| Raíz multi-panel — solo default, panels y documentation global.
| Generado por php artisan panel:install --multi
*/

return [

    'default' => 'admin',

    'panels' => [
        'admin' => require __DIR__.'/panel-admin.php',
        'cliente' => require __DIR__.'/panel-cliente.php',
    ],

    'documentation' => [
        'enabled' => true,
        'path' => 'playground',
        'middleware' => ['web'],
    ],

];
