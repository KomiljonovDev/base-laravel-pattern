<?php

namespace App\Services\Category;

use App\Services\Contracts\CategoryServiceInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\BaseService;

class CategoryService extends BaseService implements CategoryServiceInterface
{
    public function __construct(CategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
