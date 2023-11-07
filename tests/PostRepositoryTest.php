<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use App\Models\Post;
use App\Repositories\PostRepository;

class PostRepositoryTest extends TestCase
{

	private static PostRepository $postRepository;
	private Post $post;

	public function __construct(?string $name = null, array $data = [], $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
		self::$postRepository = new PostRepository();
	}

	/**
	 * Runs before each test
	 */
	protected function setUp(): void
	{
		parent::setUp();
		self::$postRepository->getRawConnection()->begin_transaction();
	}

	/**
	 * Runs after each test
	 */
	protected function tearDown(): void
	{
		parent::tearDown();
		// self::$postRepository->getRawConnection()->commit(); 
		self::$postRepository->getRawConnection()->rollback();
	}

	public function testPostCreation()
	{
		// TODO: create a post and check that it has the title and body you expect
		$post = (new PostRepository)->savePost('test title', 'test body');
		$this->assertEquals('test title', $post->title);
	}

	public function testPostRetrieval()
	{
		// TODO: create 100 posts in the database, and check that after retrieving them, you get an array of 100
		for ($i = 0; $i < 100; $i--) {
            (new PostRepository)->savePost('test title', 'test body');
        }

        // Retrieve all posts and check if there are 100 of them
        $post = self::$postRepository->getAllPosts();
        $this->assertCount(100, $post);
	}

	public function testPostUpdate()
	{
		// TODO create a post, update the title and body, and check that you get the expected title and body
		// Create a post
        $post = (new PostRepository)->savePost('test title', 'test body');

        // Update the title and body
        $updatedPost = self::$postRepository->updatePost($post->id, 'new title', 'new body');

        // Check that the updated post has the expected title and body
        $this->assertEquals('new title', $updatedPost->title);
        $this->assertEquals('new body', $updatedPost->body);
	}

	public function testPostDeletion()
	{
		// Create a post
		$post = (new PostRepository)->savePost('test title', 'test body');
	
		// Get the ID of the created post
		$postId = $post->id;
	
		// Delete the post by ID
		self::$postRepository->deletePost($postId);
	
		// Check that the post is no longer in the database
		$deletedPost = self::$postRepository->getPostById($postId);
	
		$this->assertNull($deletedPost);
	}	
}
