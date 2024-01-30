<?php

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class TagRepository
{
    private const NUMBER_OF_RECORDS = 10;

    public function __construct(protected Tag $tag)
    {
    }
    public function getAllTags(): LengthAwarePaginator
    {
        return $this->tag->paginate(self::NUMBER_OF_RECORDS);
    }

    public function getTagById($id): ?Tag
    {
        return $this->tag->find($id);
    }

    public function createTag(array $tagData): Tag
    {
        return $this->tag->create($tagData);
    }

    public function updateTag(array $tagData, int $id): Tag
    {
        $tag = $this->tag->find($id);
        $tag->update($tagData);

        return $tag;
    }

    public function deleteTag(int $id): JsonResponse
    {
        $tag = $this->tag->find($id);

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }
        if ($tag->delete()) {
            return response()->json(['message' => 'Tag deleted successfully']);
        }
        return response()->json(['message' => 'Tag deleted failed'], 500);
    }
}
