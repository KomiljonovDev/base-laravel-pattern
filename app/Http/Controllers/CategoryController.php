<?php

namespace App\Http\Controllers;

use App\Services\Contracts\CategoryServiceInterface;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Requests\Category\ListCategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    protected CategoryServiceInterface $service;

    public function __construct(CategoryServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index(ListCategoryRequest $request): JsonResponse
    {
        try {
            $filters = $request->validated()['filters'] ?? [];
            $this->service->setFilters($filters);
            $data = $this->service->get();
            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve categorys'], 500);
        }
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $data = $this->service->create($request->validated());
            return response()->json(['data' => $data, 'message' => 'Category created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create category'], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $data = $this->service->show($id);
            if (!$data) {
                throw new ModelNotFoundException();
            }
            return response()->json(['data' => $data], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve category'], 500);
        }
    }

    public function update(UpdateCategoryRequest $request, $id): JsonResponse
    {
        try {
            $data = $this->service->edit($request->validated(), $id);
            if (!$data) {
                throw new ModelNotFoundException();
            }
            return response()->json(['data' => $data, 'message' => 'Category updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update category'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $result = $this->service->delete($id);
            if (!$result) {
                throw new ModelNotFoundException();
            }
            return response()->json(['message' => 'Category deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete category'], 500);
        }
    }
}