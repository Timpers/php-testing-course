<?php

namespace Tests\Feature\App\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BlogPost;
use App\Models\BlogPostLike;
use App\Models\User;
use App\Models\Enums\BlogPostStatus;
use Carbon\Carbon;

class BlogPostTest extends TestCase
{

    public function test_published_scope()
    {
        Blogpost::factory()->create([
            'date' => '2023-06-01',
            'status' => BlogPostStatus::PUBLISHED()
        ]);

        $this->assertEquals(
            0,
            BlogPost::query()
                ->wherePublished()
                ->count()
        );

        $this->travelTo(Carbon::make('2023-01-01'));

        Blogpost::factory()->create([
            'date' => '2022-06-01',
            'status' => BlogPostStatus::PUBLISHED()
        ]);

        $this->travelTo(Carbon::make('2023-06-02'));

        $this->assertEquals(
            2,
            BlogPost::query()
                ->wherePublished()
                ->count()
        );
        $this->travelTo(Carbon::make('2023-07-01'));
        $this->travel(-2)->weeks(); // 2 weeks from the prevous travelTo

        $this->travelBack(); // back to now

    }

    public function test_with_factories()
    {
        $post = BlogPost::factory()
            ->has(BlogPostLike::factory()
                //  ->has(User::factory(), 'author') // this won't work, but is a good example of the syntax
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
