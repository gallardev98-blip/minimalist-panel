<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Fields\Field;
use MyLaravelTools\Panel\Forms\Section;
use MyLaravelTools\Panel\Forms\Tab;
use InvalidArgumentException;

final class FormSchema
{
    /**
     * @param array<int, Field|Section|Tab> $schema
     * @return array<int, Field>
     */
    public static function fields(array $schema): array
    {
        $fields = [];

        foreach ($schema as $item) {
            if ($item instanceof Tab) {
                $fields = array_merge($fields, self::fields($item->getSchema()));

                continue;
            }

            if ($item instanceof Section) {
                $fields = array_merge($fields, $item->getFields());

                continue;
            }

            if ($item instanceof Field) {
                $fields[] = $item;

                continue;
            }

            throw new InvalidArgumentException('Form schema items must be Field, Section or Tab instances.');
        }

        return $fields;
    }

    /**
     * @param array<int, Field|Section|Tab> $schema
     * @return array<int, Tab>
     */
    public static function tabs(array $schema): array
    {
        $tabs = [];

        foreach ($schema as $item) {
            if ($item instanceof Tab) {
                $tabs[] = $item;
            }
        }

        return $tabs;
    }

    /**
     * @param array<int, Field|Section|Tab> $schema
     */
    public static function hasTabs(array $schema): bool
    {
        foreach ($schema as $item) {
            if ($item instanceof Tab) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int, Field|Section|Tab> $schema
     */
    public static function hasSections(array $schema): bool
    {
        foreach ($schema as $item) {
            if ($item instanceof Section) {
                return true;
            }

            if ($item instanceof Tab) {
                foreach ($item->getSchema() as $child) {
                    if ($child instanceof Section) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
