<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\ListPostRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Services\Contracts\PostServiceInterface;

class PostController extends Controller
{
    protected $postService;
    
    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(ListPostRequest $request)
    {
        $this->postService->setRelation('user');
        $this->postService->setAttributes(['id', 'title', 'user_id', 'is_published', 'created_at']);
        $this->postService->setFilters($request->validated());
        return response()->json($this->postService->get());
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $post = $this->postService->create($request->validated());
        return response()->json(['message' => 'Post created successfully', 'data' => $post]);
    }
    
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json($this->postService->show($id));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, $id)
    {
        $post = $this->postService->edit($request->validated(), $id);
        return response()->json(['message' => 'Post updated successfully', 'data' => $post]);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->postService->delete($id);
        return response()->json(['message' => 'Post deleted successfully']);
    }
}

