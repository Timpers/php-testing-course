<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\BlogPost;
use App\Http\Controllers\BlogPostController;

class VoteButtonTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function test_vote_button_increments_counter()
    {
        #create a post

        $post = BlogPost::factory()->create(['likes' => 10]);

        #navigate to the post
        $this->browse(function (Browser $browser) use ($post) {
            #check the likes counter
            $browser->visit(action([BlogPostController::class, 'show'], $post->slug))
                ->with('@vote-button', function (Browser $button) {
                    $button->assertSee(10);
                })
                #click the vote button
                ->click('@vote-button')                
                ->pause(500)                 
                ->screenshot('vote-button-clicked')
                ->with('@vote-button', function (Browser $button) {
                    $button->assertSee(11);
                });
        });

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('My Blog');
        });
    }
}
