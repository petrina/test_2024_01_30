<?php

namespace App\Services;

use App\Repositories\PostRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use App\Models\{Language, Post, PostTranslation, Tag};


class PostService
{
    const SUPPORTED_LANGUAGES = ['en', 'ru', 'ua'];
    protected PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getAllPosts(string $language): LengthAwarePaginator
    {
        return $this->postRepository->getAllPosts($language);
    }

    public function getPostWithTranslation(int $postId, string $language): Builder|Model
    {
        return $this->postRepository->getPostWithTranslation($postId, $language);
    }

    public function createPost(array $postData): Post
    {
        $post = new Post();
        $post->save();

        $languages = array_column($postData['translations'], null, 'language');

        foreach (self::SUPPORTED_LANGUAGES as $language) {
            $translationData = $languages[$language];
            $languageModel = (new Language)->firstOrCreate(['locale' => $language, 'prefix' => $language]);

            $postTranslation = new PostTranslation([
                'title' => $translationData['title'],
                'description' => $translationData['description'],
                'content' => $translationData['content'],
            ]);

            $postTranslation->language()->associate($languageModel);
            $post->translations()->save($postTranslation);
        }

        if (isset($postData['tag'])) {
            $tag = (new Tag)->firstOrCreate(['name' => $postData['tag']]);
            $post->tags()->attach($tag->id);
        }

        return $post;
    }

    public function updatePost(array $data, int $postId): Post
    {
        $post = (new Post)->findOrFail($postId);
        $post->update($data);

        if (isset($data['translations'])) {
            foreach ($data['translations'] as $translation) {
                $post->translations()->updateOrCreate(
                    ['language_id' => $translation['language_id']],
                    $translation
                );
            }
        }
        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }
        return $post;
    }

    public function updateTag(array $tagData, int $postId): JsonResponse
    {
        $validator = validator($tagData, [
            'tag' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $tag = (new Tag)->firstOrCreate(['name' => $tagData['tag']]);
        $post = (new Post)->findOrFail($postId);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->tags()->syncWithoutDetaching([$tag->id]);
        return response()->json(['message' => 'Tag updated successfully', 'post' => $post]);
    }

    public function deletePost(int $postId): JsonResponse
    {
        return $this->postRepository->deletePost($postId);
    }
}
