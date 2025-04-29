<?php

namespace App\Repositories\Company;

use App\Models\MetaResource;
use App\Repositories\BaseRepository;

class CompanyRepository extends BaseRepository {
    public function __construct (protected MetaResource $entity) {

    }
}
