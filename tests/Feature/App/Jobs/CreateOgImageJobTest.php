<?php

use Tests\TestCase;
use App\Models\BlogPost;
use App\Jobs\CreateOgImageJob;
use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;


class CreateOgImageJobTest extends TestCase
{
    public function test_job_is_dispatched_correctly()
    {
        Bus::fake();

        #create post triggers job
        $post = BlogPost::factory()->create();
        Bus::assertDispatched(CreateOgImageJob::class);
        #update title triggers job
        Bus::fake();
        $post->fresh()->update(['title' => 'New Title']);
        Bus::assertDispatched(CreateOgImageJob::class);
        #update date doesn't trigger job
        Bus::fake();
        $post->fresh()->update(['date' => now()]);
        Bus::assertNotDispatched(CreateOgImageJob::class);
    }
    
    public function test_file_is_generated_correctly()
    {
        Bus::swap(app(Dispatcher::class));
        Storage::fake('public');

        $post = BlogPost::factory()->create();

        Storage::disk('public')->assertExists($post->ogImagePath());
    }
}