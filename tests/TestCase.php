<?php

namespace Tests;

use App\Jobs\CreateOgImageJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Bus;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Http\Request;


abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Bus::fake([CreateOgImageJob::class]); // fakes this for any invocation of the job class
        
    }

    public function login(User $user = null)
    {
        $user ??= User::factory()->create();

        $this->actingAs($user);

        return $user;
    }

    public function createRequest($method, $uri): Request{
        $symfonyRequest = SymfonyRequest::create($uri, $method);

        return Request::createFromBase($symfonyRequest);
    }
}
