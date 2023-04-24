<?php

namespace Tests\Feature\Actions;

use App\Support\Rss\RssEntry;
use App\Support\Rss\RssRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery\MockInterface;
use Carbon\CarbonImmutable;
use App\Actions\SyncExternalPost;
use App\Models\ExternalPost;

class SyncExternalPostTest extends TestCase
{
    public function test_sync_posts_are_stored_in_the_database()
    {
        #arrange

        /**
         * @var RssRepository $rss
         */
        $rss = $this->mock(RssRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetch')
                ->andReturn(collect([new RssEntry(
                    title: 'test',
                    url: 'https://test.com',
                    date: CarbonImmutable::make('2023-04-24')
                )]));
        });


        $sync = new SyncExternalPost($rss);
        ##act
        $sync('https://test.com/feed');
        ###assert
        $this->assertDatabaseHas(ExternalPost::class, [
            'title' => 'test',
            'url' => 'https://test.com',
            
        ]);
    }
}
