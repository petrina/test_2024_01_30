<?php

namespace Tests\Feature;

use App\Repositories\PostRepository;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;
use App\Http\Controllers\PostController;
use App\Http\Requests\PostStoreRequest;
use App\Services\PostService;
use App\Models\Post;



class PostControllerTest extends TestCase
{
    use WithoutMiddleware;
    /** @test */
    public function testCreatePost()
    {

        $postService = $this->getMockBuilder(PostService::class)
            ->disableOriginalConstructor()
            ->getMock();


        $postService->expects($this->once())
            ->method('createPost')
            ->with([
                'translations' => [
                    'en' => [
                        'title' => 'Test Title',
                        'description' => 'Test Description',
                        'content' => 'Test Content',
                    ],
                ],
                'tag' => 'test-tag',
            ])
            ->willReturn(new Post());


        $postRepository = $this->getMockBuilder(PostRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->app->instance(PostService::class, $postService);
        $this->app->instance(PostRepository::class, $postRepository);

        $controller = new PostController($postService, $postRepository);

        $postData = [
            'translations' => [
                'en' => [
                    'title' => 'Test Title',
                    'description' => 'Test Description',
                    'content' => 'Test Content',
                ],
            ],
            'tag' => 'test-tag',
        ];
        $request = new PostStoreRequest($postData);

        $response = $controller->store($request);
        $this->assertJson($response->getContent());
    }

    /** @test */
    public function testShowPost()
    {

        $postService = $this->getMockBuilder(PostService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $postService->expects($this->once())
            ->method('getAllPosts')
            ->with('en')
            ->willReturn(new LengthAwarePaginator([], 0, 10, 1));

        $postRepository = $this->getMockBuilder(PostRepository::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->app->instance(PostService::class, $postService);
        $this->app->instance(PostRepository::class, $postRepository);

        $controller = new PostController($postService, $postRepository);
        $request = new Request();
        $response = $controller->index($request);

        $this->assertJson($response->getContent());
    }

    /** @test */
    public function testUpdatePost()
    {
        $postService = $this->getMockBuilder(PostService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $postRepository = $this->getMockBuilder(PostRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $postService->expects($this->once())
            ->method('updatePost')
            ->with($this->isType('array'), $this->isType('int'))
            ->willReturn(new \App\Models\Post());

        $controller = new PostController($postService, $postRepository);
        $postData = [
            'translations' => [
                [
                    'language_id' => 1,
                    'title' => 'Updated Title',
                    'description' => 'Updated Description',
                    'content' => 'Updated Content',
                ],
            ],
            'tags' => 't',
        ];
        $request = new Request([], $postData);
        $response = $controller->update( 1);
        $this->assertEquals(['message' => 'Post updated successfully'], json_decode($response->getContent(), true));
    }

    /** @test */
    public function testDeletePost()
    {
        $postService = $this->getMockBuilder(PostService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $postRepository = $this->getMockBuilder(PostRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $postService->expects($this->once())
            ->method('deletePost')
            ->with(1)
            ->willReturn(response()->json(['message' => 'Post deleted successfully']));

        $controller = new PostController($postService, $postRepository);
        $response = $controller->destroy(1);

        $this->assertEquals(['message' => 'Post deleted successfully'], json_decode($response->getContent(), true));
    }
}
