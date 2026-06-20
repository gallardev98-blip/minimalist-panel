<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Fields\PermissionsField;
use MyLaravelTools\Panel\Support\FieldPayload;
use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;

final class PermissionsFieldTest extends TestCase
{
    public function test_permissions_field_is_excluded_from_model_payload(): void
    {
        $field = PermissionsField::make('permissions');
        $result = $field->dehydrateForStorage(['manage users', 'access panel'], null);

        $this->assertFalse($result['include']);
        $this->assertSame(['manage users', 'access panel'], $result['value']);
    }

    public function test_permissions_field_syncs_permissions_after_save(): void
    {
        $record = new class extends Model
        {
            /** @var array<int, string> */
            public array $syncedPermissions = [];

            public function syncPermissions(mixed $permissions): void
            {
                $this->syncedPermissions = is_array($permissions) ? $permissions : [];
            }
        };

        $field = PermissionsField::make('permissions');
        $field->afterSave($record, ['manage users', 'access panel']);

        $this->assertSame(['manage users', 'access panel'], $record->syncedPermissions);
    }

    public function test_persist_after_save_invokes_permissions_field_hooks(): void
    {
        $record = new class extends Model
        {
            /** @var array<int, string> */
            public array $syncedPermissions = [];

            public function syncPermissions(mixed $permissions): void
            {
                $this->syncedPermissions = is_array($permissions) ? $permissions : [];
            }
        };

        $field = PermissionsField::make('permissions');

        FieldPayload::persistAfterSave(
            [$field],
            ['permissions' => ['view reports']],
            $record,
        );

        $this->assertSame(['view reports'], $record->syncedPermissions);
    }
}
