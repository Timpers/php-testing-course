<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\JsonPostController;
use App\Models\BlogPost;
use Illuminate\Testing\Fluent\AssertableJson;

class JsonControllerTest extends TestCase
{
    public function test_index_shows_all_the_blog_posts()
    {
        [$postA, $postB] = BlogPost::factory()
            ->count(2)
            ->published()
            ->create();

        $this->get(action([JsonPostController::class, 'index']))
            ->assertSuccessful()
            ->assertJson(function (AssertableJson $json) use($postA, $postB) {
                $json->has('data', 2)
                    ->has('data.0', function (AssertableJson $json) use ($postA) {
                        $json->has('id')
                            ->has('date')
                            ->whereType('date', 'string')
                            ->whereType('id', 'integer')
                            ->where('id', $postA->id)
                            ->etc();
                    });
            });
    }

    public function test_detail_show_one_blog_post()
    {
        [$postA, $postB] = BlogPost::factory()
            ->count(2)
            ->published()
            ->create();

        $slug = $postA->slug;

        $this->get(action([JsonPostController::class, 'show'], $slug))
            ->assertSuccessful()
            ->assertJson(function (AssertableJson $json) use ($postA) {                
                    $json->has('id')
                    ->whereType('id', 'integer')
                    ->has('date')
                    ->whereType('date', 'string')                    
                    ->where('id', $postA->id)
                    ->has('title')
                    ->whereType('title', 'string')
                    ->where('title', $postA->title)
                    ->etc();
            });
    
    }
}
