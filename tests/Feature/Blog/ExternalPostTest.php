<?php

namespace Tests\Feature;

use App\Http\Controllers\ExternalPostSuggestionController;
use App\Http\Controllers\BlogPostController;
use App\Mail\ExternalPostSuggestedMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\ExternalPost;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class ExternalPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_external_post_can_be_submitted()
    {
        Mail::fake();

        $user = User::factory()->create();

        $this->post(action(ExternalPostSuggestionController::class, [
            'title' => 'My title',
            'url' => 'https://example.com',
        ]))
            ->assertRedirect(action([BlogPostController::class, 'index']))
            ->assertSessionHas('laravel_flash_message', [
                'class' => 'bg-ink text-white',
                'message' => 'Thanks for your suggestion',
                'level' => null
            ]);

        $this->assertDatabaseHas(ExternalPost::class, [
            'title' => 'My title',
            'url' => 'https://example.com',
        ]);

        Mail::assertSent(function (ExternalPostSuggestedMail $mail) use ($user) {
            return $mail->to[0]['address'] === $user->email;
        });
    }
}
