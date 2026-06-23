<?php

declare(strict_types=1);

/*
| Panel cliente — ejemplo multi-panel (portal de cliente).
| Regístralo en config/panel.php → 'panels' => ['cliente' => require __DIR__.'/panel-cliente.php']
*/

return [
    'path' => 'cliente',
    'brand' => [
        'name' => 'Portal Cliente',
        'logo' => null,
        'tagline' => 'Área de clientes',
    ],
    'locale' => 'es',
    'auth' => [
        'enabled' => true,
        'register' => false,
    ],
    'resources' => [],
    'widgets' => [],
    'navigation' => null,
];
