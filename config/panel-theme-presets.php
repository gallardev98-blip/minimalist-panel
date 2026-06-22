<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Presets de tema del panel
|--------------------------------------------------------------------------
|
| Usa 'preset' => 'corporate' en config/panel.php y sobrescribe solo lo que necesites.
|
*/

return [

    'minimal' => [
        'default' => 'dark',
        'font' => 'Plus Jakarta Sans',
        'radius' => '0.75rem',
        'sidebar_width' => '16rem',
        'colors' => [
            'primary' => '#000000',
            'primary_hover' => '#262626',
            'primary_dark' => '#ffffff',
            'primary_hover_dark' => '#e5e5e5',
            'accent' => '#525252',
            'accent_dark' => '#a3a3a3',
            'success' => '#16a34a',
            'danger' => '#dc2626',
            'warning' => '#ca8a04',
        ],
        'light' => [
            'bg' => '#ffffff',
            'surface' => '#fafafa',
            'card' => '#ffffff',
            'elevated' => '#f5f5f5',
            'border' => '#e5e5e5',
            'heading' => '#0a0a0a',
            'text' => '#404040',
            'muted' => '#737373',
            'input_bg' => '#ffffff',
            'input_border' => '#d4d4d4',
        ],
        'dark' => [
            'bg' => '#0a0a0a',
            'surface' => '#111111',
            'card' => '#141414',
            'elevated' => '#1a1a1a',
            'border' => '#262626',
            'heading' => '#fafafa',
            'text' => '#d4d4d4',
            'muted' => '#737373',
            'input_bg' => '#0a0a0a',
            'input_border' => '#404040',
        ],
    ],

    'corporate' => [
        'default' => 'light',
        'font' => 'Inter',
        'radius' => '0.5rem',
        'sidebar_width' => '17rem',
        'colors' => [
            'primary' => '#1e40af',
            'primary_hover' => '#1d4ed8',
            'primary_dark' => '#3b82f6',
            'primary_hover_dark' => '#60a5fa',
            'accent' => '#475569',
            'accent_dark' => '#94a3b8',
            'success' => '#059669',
            'danger' => '#dc2626',
            'warning' => '#d97706',
        ],
        'light' => [
            'bg' => '#f8fafc',
            'surface' => '#f1f5f9',
            'card' => '#ffffff',
            'elevated' => '#e2e8f0',
            'border' => '#cbd5e1',
            'heading' => '#0f172a',
            'text' => '#334155',
            'muted' => '#64748b',
            'input_bg' => '#ffffff',
            'input_border' => '#cbd5e1',
        ],
        'dark' => [
            'bg' => '#0f172a',
            'surface' => '#1e293b',
            'card' => '#1e293b',
            'elevated' => '#334155',
            'border' => '#475569',
            'heading' => '#f8fafc',
            'text' => '#cbd5e1',
            'muted' => '#94a3b8',
            'input_bg' => '#0f172a',
            'input_border' => '#475569',
        ],
    ],

    'contrast' => [
        'default' => 'light',
        'font' => 'IBM Plex Sans',
        'radius' => '0.25rem',
        'sidebar_width' => '15rem',
        'colors' => [
            'primary' => '#000000',
            'primary_hover' => '#171717',
            'primary_dark' => '#ffffff',
            'primary_hover_dark' => '#f5f5f5',
            'accent' => '#000000',
            'accent_dark' => '#ffffff',
            'success' => '#15803d',
            'danger' => '#b91c1c',
            'warning' => '#a16207',
        ],
        'light' => [
            'bg' => '#ffffff',
            'surface' => '#ffffff',
            'card' => '#ffffff',
            'elevated' => '#fafafa',
            'border' => '#000000',
            'heading' => '#000000',
            'text' => '#000000',
            'muted' => '#404040',
            'input_bg' => '#ffffff',
            'input_border' => '#000000',
        ],
        'dark' => [
            'bg' => '#000000',
            'surface' => '#0a0a0a',
            'card' => '#0a0a0a',
            'elevated' => '#171717',
            'border' => '#ffffff',
            'heading' => '#ffffff',
            'text' => '#ffffff',
            'muted' => '#d4d4d4',
            'input_bg' => '#000000',
            'input_border' => '#ffffff',
        ],
    ],

    'ocean' => [
        'default' => 'dark',
        'font' => 'DM Sans',
        'radius' => '0.875rem',
        'sidebar_width' => '16rem',
        'colors' => [
            'primary' => '#0e7490',
            'primary_hover' => '#0891b2',
            'primary_dark' => '#22d3ee',
            'primary_hover_dark' => '#67e8f9',
            'accent' => '#64748b',
            'accent_dark' => '#94a3b8',
            'success' => '#10b981',
            'danger' => '#f43f5e',
            'warning' => '#f59e0b',
        ],
        'light' => [
            'bg' => '#f0fdfa',
            'surface' => '#ecfeff',
            'card' => '#ffffff',
            'elevated' => '#cffafe',
            'border' => '#a5f3fc',
            'heading' => '#164e63',
            'text' => '#155e75',
            'muted' => '#64748b',
            'input_bg' => '#ffffff',
            'input_border' => '#67e8f9',
        ],
        'dark' => [
            'bg' => '#042f2e',
            'surface' => '#134e4a',
            'card' => '#115e59',
            'elevated' => '#0f766e',
            'border' => '#14b8a6',
            'heading' => '#ccfbf1',
            'text' => '#99f6e4',
            'muted' => '#5eead4',
            'input_bg' => '#042f2e',
            'input_border' => '#0d9488',
        ],
    ],

];
