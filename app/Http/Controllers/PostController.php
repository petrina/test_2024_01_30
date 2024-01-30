<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Repositories\PostRepository;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{

    public function __construct(protected PostService $postService, protected PostRepository $postRepository)
    {

    }

    public function index(): JsonResponse
    {
        $language = request()->query('lang', 'en');
        $post = $this->postService->getAllPosts($language);
        return response()->json(['post' => $post]);
    }

    public function show(int $postId): JsonResponse
    {
        $language = request()->query('lang', 'en');
        $post = $this->postService->getPostWithTranslation($postId, $language);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        return response()->json(['post' => $post]);
    }

    public function store(PostStoreRequest $request): JsonResponse
    {
        $postData = $request->only(['translations', 'tag']);
        $post = $this->postService->createPost($postData);
        return response()->json(['message' => 'Post with id = '. $post['id'] .' created successfully'], 201);
    }

    public function update(int $postId): JsonResponse
    {
        $postData = request()->all();
        $this->postService->updatePost($postData, $postId);
        return response()->json(['message' => 'Post updated successfully'], 201);
    }

    public function destroy(int $postId): JsonResponse
    {
        return $this->postService->deletePost($postId);
    }

    public function search(): JsonResponse
    {
        $searchLanguage = request()->input('lang');
        $searchTerm = request()->input('search');
        $postIterator = $this->postRepository->searchPosts($searchLanguage, $searchTerm);
        $posts = iterator_to_array($postIterator);

        if (empty($posts)) {
            return response()->json(['message' => 'Nothing found'], 404);
        }
        return response()->json(['posts' => $posts]);
    }

    public function updateTag(int $postId): JsonResponse
    {
        $tagData = request()->all();
        return $this->postService->updateTag($tagData, $postId);
    }
}
