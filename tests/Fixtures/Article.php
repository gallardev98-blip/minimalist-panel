<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Article extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'published'];

    protected function casts(): array
    {
        return ['published' => 'boolean'];
    }
}
