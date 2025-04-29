<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\ListCompanyRequest;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Services\Company\CompanyService;

class CompanyController extends Controller
{
    public function __construct (public CompanyService $companyService) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListCompanyRequest $request)
    {
        $this->companyService->setRelation('employees');
        $this->companyService->setAttributes(['id','name', 'address', 'email', 'web_site']);
        $this->companyService->setFilters($request->validated());
        return response()->successJson($this->companyService->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        $this->companyService->create($request->validated());
        return response()->successJson('Company created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $company)
    {
        return response()->successJson($this->companyService->show($company));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, string $company)
    {
        $this->companyService->edit($request->validated(), $company);
        return response()->successJson('Company updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $company)
    {
        $this->companyService->delete($company);
        return response()->successJson('Company deleted successfully');
    }
}
