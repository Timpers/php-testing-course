<?php
use Tests\TestCase;
use Ramsey\Uuid\Uuid;
use App\Models\BlogPost;
use Livewire\Livewire;
use App\Http\Livewire\VoteButton;
use App\Models\BlogPostLike;

class VoteButtonTest extends TestCase{
    
    public function test_like_can_be_toggled(){
        # Arrange
        $post = BlogPost::factory()->create(['likes' => 10]);

        $likerUuid = Uuid::uuid4();

        $voteButton = Livewire::test(VoteButton::class, ['post' => $post, 'likerUuid' => $likerUuid]);

        $voteButton->assertSee('text-paper');
        #act
        $voteButton->call('like');

        #assert
        $voteButton->assertSee('text-ink');
        $this->assertEquals(10 + 1, $post->refresh()->likes);
        $this->assertDatabaseHas(BlogPostLike::class, [
            'blog_post_id' => $post->id,
            'liker_uuid' => $likerUuid
        ]);

        $voteButton->call('like');
        $this->assertEquals(10, $post->refresh()->likes);
        
        $this->assertDatabaseMissing(BlogPostLike::class, [
            'blog_post_id' => $post->id,
            'liker_uuid' => $likerUuid
        ]);
    }
}