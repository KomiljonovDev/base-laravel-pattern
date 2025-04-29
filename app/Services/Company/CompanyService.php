<?php

namespace App\Services\Company;

use App\Repositories\Company\CompanyRepository;
use App\Services\BaseService;

class CompanyService extends BaseService {
    public function __construct (public CompanyRepository $repo) {

    }
}
