<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Fixtures;

use MyLaravelTools\Panel\Pages\Page;

final class SettingsPage extends Page
{
    protected static ?string $label = 'Settings';

    protected static ?string $slug = 'settings';

    protected static ?string $permission = 'manage settings';

    public static function view(): string
    {
        return 'panel::livewire.dashboard';
    }
}
