<?php



declare(strict_types=1);



namespace MyLaravelTools\Panel\Relations;



use MyLaravelTools\Panel\Fields\Field;

use MyLaravelTools\Panel\Resources\Resource;

use MyLaravelTools\Panel\Support\FormSchema;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\Relation;



final class RelationManager

{

    private ?string $title = null;



    private ?string $foreignKey = null;



    private string $type = 'hasMany';



    private bool $deleteRelated = false;



    /** @param class-string<Resource> $resourceClass */

    private function __construct(

        private readonly string $relationship,

        private readonly string $resourceClass,

    ) {}



    /** @param class-string<Resource> $resourceClass */

    public static function make(string $relationship, string $resourceClass): self

    {

        return new self($relationship, $resourceClass);

    }



    /** @param class-string<Resource> $resourceClass */

    public static function belongsToMany(string $relationship, string $resourceClass): self

    {

        $manager = new self($relationship, $resourceClass);

        $manager->type = 'belongsToMany';



        return $manager;

    }



    public function title(string $title): self

    {

        $this->title = $title;



        return $this;

    }



    public function foreignKey(string $foreignKey): self

    {

        $this->foreignKey = $foreignKey;



        return $this;

    }



    public function deleteRelated(bool $deleteRelated = true): self

    {

        $this->deleteRelated = $deleteRelated;



        return $this;

    }



    public function getRelationship(): string

    {

        return $this->relationship;

    }



    /** @return class-string<Resource> */

    public function getResourceClass(): string

    {

        return $this->resourceClass;

    }



    public function getTitle(): string

    {

        return $this->title ?? $this->resourceClass::label();

    }



    public function isBelongsToMany(): bool

    {

        return $this->type === 'belongsToMany';

    }



    public function shouldDeleteRelated(): bool

    {

        return $this->deleteRelated;

    }



    public function resolveForeignKey(Model $parent): string

    {

        if ($this->foreignKey !== null) {

            return $this->foreignKey;

        }



        /** @var Relation<Model, Model> $relation */

        $relation = $parent->{$this->relationship}();



        return $relation->getForeignKeyName();

    }



    /** @return Builder<Model> */

    public function query(Model $parent): Builder

    {

        /** @var Relation<Model, Model> $relation */

        $relation = $parent->{$this->relationship}();



        return $relation->getQuery()->with($this->resourceClass::with());

    }



    /** @return array<int, Field> */

    public function formFields(Model $parent): array

    {

        $fields = FormSchema::fields($this->resourceClass::form());



        if ($this->isBelongsToMany()) {

            return $fields;

        }



        $foreignKey = $this->resolveForeignKey($parent);



        return array_values(array_filter(

            $fields,

            fn (Field $field): bool => $field->getName() !== $foreignKey,

        ));

    }

}

