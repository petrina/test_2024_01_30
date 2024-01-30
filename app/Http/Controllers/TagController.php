<?php

namespace App\Http\Controllers;

use App\Services\TagService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{

    public function __construct(protected TagService $tagService)
    {
    }

    public function index(): LengthAwarePaginator
    {
        return $this->tagService->getAllTags();
    }

    public function show(int $id): JsonResponse|array
    {
        $tag = $this->tagService->getTagById($id);

        if ($tag) {
            return ['tag' => $tag];
        }

        return response()->json(['message' => 'Tag not found'], 404);
    }

    public function store(array $tagData): array
    {
        $tag = $this->tagService->createTag($tagData);

        return ['tag' => $tag];
    }

    public function update(array $tagData, int $id): array
    {
        $tag = $this->tagService->updateTag($tagData, $id);

        return ['tag' => $tag];
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->tagService->deleteTag($id);
    }
}
