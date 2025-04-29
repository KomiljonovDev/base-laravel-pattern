<?php

namespace App\Repositories\Category;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    protected $entity;

    public function __construct(Category $model)
    {
        $this->entity = $model;
    }
}
