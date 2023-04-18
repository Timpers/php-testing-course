<?php

namespace Tests\Feature\App\Http;

use App\Http\Controllers\BlogPostAdminController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BlogPost;
use App\Models\User;

class BlogAdminControllerTest extends TestCase
{
    public function test_only_a_logged_in_user_can_make_changes_to_a_post()
    {
        $blogPost = BlogPost::factory()->create();

        $sendRequest = fn () => $this->post(
            action([BlogPostAdminController::class, 'update'], $blogPost->slug),
            [
                'title' => 'new title',
                'author' => $blogPost->author,
                'body' => $blogPost->body,
                'status' => $blogPost->status,
                'date' => $blogPost->date->format('Y-m-d')
            ]
        );

        $sendRequest()->assertRedirect(route('login'));

        $this->assertNotEquals($blogPost->refresh()->title, 'new title');

        $user = User::factory()->create();

        $this->login($user);

        $sendRequest()->assertRedirect(action([BlogPostAdminController::class, 'edit'], $blogPost->slug));

        $this->assertEquals($blogPost->refresh()->title, 'new title');
    }
}
