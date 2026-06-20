<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Pages;

use MyLaravelTools\Panel\Support\PanelPermission;
use Illuminate\Support\Str;

abstract class Page
{
    protected static ?string $label = null;

    protected static ?string $icon = null;

    protected static ?string $slug = null;

    /** Permiso Spatie/Gate requerido (null = cualquier usuario autenticado del panel). */
    protected static ?string $permission = null;

    /** Vista Blade (relativa a resources/views o namespace panel::). */
    abstract public static function view(): string;

    /** @return array<string, mixed> */
    public static function data(): array
    {
        return [];
    }

    public static function canAccess(): bool
    {
        if (static::$permission === null || static::$permission === '') {
            return true;
        }

        return PanelPermission::check(static::$permission);
    }

    public static function permission(): ?string
    {
        return static::$permission;
    }

    public static function label(): string
    {
        if (static::$label !== null && static::$label !== '') {
            return static::$label;
        }

        return Str::headline(class_basename(static::class));
    }

    public static function icon(): ?string
    {
        return static::$icon;
    }

    public static function slug(): string
    {
        if (static::$slug !== null && static::$slug !== '') {
            return static::$slug;
        }

        return Str::kebab(class_basename(static::class));
    }

    public static function url(): string
    {
        return route('panel.pages.show', ['page' => static::slug()]);
    }
}
