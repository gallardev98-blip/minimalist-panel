<?php

declare(strict_types=1);

namespace Panel\Minimalist\Tests\Unit;

use Panel\Minimalist\Fields\RolesField;
use Panel\Minimalist\Support\FieldPayload;
use Panel\Minimalist\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;

final class RolesFieldTest extends TestCase
{
    public function test_roles_field_is_excluded_from_model_payload(): void
    {
        $field = RolesField::make('roles');
        $result = $field->dehydrateForStorage(['admin', 'editor'], null);

        $this->assertFalse($result['include']);
        $this->assertSame(['admin', 'editor'], $result['value']);
    }

    public function test_roles_field_syncs_roles_after_save(): void
    {
        $record = new class extends Model
        {
            /** @var array<int, string> */
            public array $syncedRoles = [];

            public function syncRoles(mixed $roles): void
            {
                $this->syncedRoles = is_array($roles) ? $roles : [];
            }
        };

        $field = RolesField::make('roles');
        $field->afterSave($record, ['admin', 'editor']);

        $this->assertSame(['admin', 'editor'], $record->syncedRoles);
    }

    public function test_persist_after_save_invokes_field_hooks(): void
    {
        $record = new class extends Model
        {
            /** @var array<int, string> */
            public array $syncedRoles = [];

            public function syncRoles(mixed $roles): void
            {
                $this->syncedRoles = is_array($roles) ? $roles : [];
            }
        };

        $field = RolesField::make('roles');

        FieldPayload::persistAfterSave(
            [$field],
            ['roles' => ['viewer']],
            $record,
        );

        $this->assertSame(['viewer'], $record->syncedRoles);
    }
}
