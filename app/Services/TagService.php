<?php

namespace App\Services;

use App\Repositories\TagRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class TagService
{
    protected TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function getAllTags(): LengthAwarePaginator
    {
        return $this->tagRepository->getAllTags();
    }

    public function getTagById(int $id): ?array
    {
        $tag = $this->tagRepository->getTagById($id);

        return $tag?->toArray();
    }

    public function createTag(array $tagData): array
    {
        $tag = $this->tagRepository->createTag($tagData);

        return $tag->toArray();
    }

    public function updateTag(array $tagData, int $id): array
    {
        $tag = $this->tagRepository->updateTag($tagData, $id);

        return $tag->toArray();
    }

    public function deleteTag(int $id): JsonResponse
    {
        return $this->tagRepository->deleteTag($id);
    }
}
