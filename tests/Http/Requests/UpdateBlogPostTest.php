<?php

use App\Http\Controllers\BlogPostAdminController;
use App\Models\BlogPost;
use Tests\TestCase;

class UpdateBlogPostTest extends TestCase
{

    private BlogPost $blogPost;

    public function setUp(): void
    {
        parent::setUp();
        $this->blogPost = BlogPost::factory()->create();
    }
    public function test_required_fields_are_valid()
    {
        $this->login();

        $this->post(action([BlogPostAdminController::class, 'update'], $this->blogPost->slug), [])
            ->assertSessionHasErrors(['title', 'body', 'date', 'author']);
    }

    public function test_date_format_is_validated()
    {
        $this->login();

        $this->post(action([BlogPostAdminController::class, 'update'], $this->blogPost->slug), [
            'title' => $this->blogPost->title,
            'body' => $this->blogPost->body,
            'author' => $this->blogPost->author,
            'date' => 'Today'
        ])
            ->assertSessionHasErrors(['date' => 'The date does not match the format Y-m-d.']);
    }
}
