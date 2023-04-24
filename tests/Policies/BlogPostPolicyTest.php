<?php

use Tests\TestCase;
use App\Models\User;
use App\Models\BlogPost;
use App\Http\Controllers\BlogPostAdminController;

class BlogPostPolicyTest extends TestCase
{

    private BlogPost $post;

    public function setUp(): void
    {
        parent::setUp();
        $this->post = BlogPost::factory()->create();
    }

    public function test_only_admin_users_are_allowed()
    {
        [$guest, $admin] = User::factory()->count(2)->sequence(
            ['is_admin' => false],
            ['is_admin' => true]
        )->create();

        $this->get(action([BlogPostAdminController::class, 'index']))
            ->assertRedirect(route('login'));

        $this->login($guest);

        $this->get(action([BlogPostAdminController::class, 'index']))
            ->assertForbidden();

        $this->login($admin);

        $this->get(action([BlogPostAdminController::class, 'index']))
            ->assertSuccessful();
    }

    /**
     * @dataProvider request
     */
    public function test_guests_are_not_allowed(Closure $sendRequest)
    {
        $this->login(User::factory()->create(['is_admin' => false]));

        /** @var \Illuminate\Testing\TestResponse $response */
        $response = $sendRequest->call($this, $this->post);

        $response->assertForbidden();
    }

    /**
     * @dataProvider request
     */
    public function test_unlogged_users_are_redirected(Closure $sendRequest)
    {
        /** @var \Illuminate\Testing\TestResponse $response */
        $response = $sendRequest->call($this, $this->post);

        $response->assertRedirect(route('login'));
    }

    /**
     * @dataProvider request
     */
    public function test_admin_users_are_allowed(Closure $sendRequest)
    {
        $this->login(User::factory()->create(['is_admin' => true]));

        /** @var \Illuminate\Testing\TestResponse $response */
        $response = $sendRequest->call($this, $this->post);

        $this->assertTrue(in_array($response->status(), [200, 302, 201, 204]));
    }

    public function request(): Generator
    {
        yield [fn (BlogPost $post) => $this->get(action([BlogPostAdminController::class, 'index']))];
        yield [fn (BlogPost $post) => $this->get(action([BlogPostAdminController::class, 'create']))];
        yield [fn (BlogPost $post) => $this->post(action([BlogPostAdminController::class, 'store']))];
    }
}
