<?php

namespace Tests\Feature\App\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BlogPost;
use App\Models\BlogPostLike;
use App\Models\User;

class BlogPostTest extends TestCase
{
    public function test_with_factories()
    {
        $post = BlogPost::factory()
            ->has(BlogPostLike::factory()
            ->has(User::factory(), 'author') // this won't work, but is a good example of the syntax
            ->count(5), 'postLikes')
            ->create();

        $this->assertCount(5, $post->postLikes);

        $postLike = BlogPostLike::factory()
            ->for(BlogPost::factory()
                ->published())
            ->create();

            $this->assertTrue($postLike->blogPost->isPublished());
    }
}
