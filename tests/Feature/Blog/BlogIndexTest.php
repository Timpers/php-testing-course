<?php

namespace Tests\Feature\Blog;

use App\Models\BlogPost;
use App\Models\Enums\BlogPostStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogIndexTest extends TestCase
{
    use RefreshDatabase;
    public function test_index_shows_list_of_blog_posts()
    {
        #arrange 
        $this->withoutExceptionHandling();

        $blogPost = BlogPost::factory()
            ->published()->count(2)
            ->sequence(
                ['title' => 'Thoughts on event sourcing', 'date' => '2023-04-18'],
                ['title' => 'Fibers', 'date' => '2023-04-17']
            )
            ->create();

        BlogPost::factory()
            ->create(['title' => 'draft post', 'status' => BlogPostStatus::DRAFT()]);

        ##act

        ###assert
        $this->get('/')
            ->assertSee('Articles on PHP')
            ->assertSeeInOrder(['Thoughts on event sourcing', 'Fibers'])
            ->assertSuccessful()
            ->assertDontSee('draft post');
    }
}
