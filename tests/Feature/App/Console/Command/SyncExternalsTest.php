<?php
use Tests\TestCase;
use Tests\Fakes\RssRepositoryFake;

class SyncExternalsTest extends TestCase
{
    public function test_external_feeds_are_synced()
    {
        #arrange (set config vars, mock rssREpository, etc)
        RssRepositoryFake::setUp();
        config()->set('services.external_feeds', ['https://a.test/rss', 'https://b.test/rss']);
        #act (call the command)
        $this->artisan('sync:externals')
            ->expectsOutput('Fetching 2 feeds')
            // ->expectsOutput('\t- https://a.test/rss')
            // ->expectsOutput('\t- https://b.test/rss')
            ->expectsOutput('Done')
            ->assertExitCode(0);

        #assert (output is right, exitcode  is 0, database has the right records)
    }
}
