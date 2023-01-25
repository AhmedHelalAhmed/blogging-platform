<?php

namespace Tests\Feature\Dashboard;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CreatePostTest extends TestCase
{
    use LazilyRefreshDatabase;

    const CREATE_POST_PAGE_NAME = 'posts.create';

    const STORE_POST_PAGE_NAME = 'posts.store';

    const DASHBOARD_PAGE_NAME = 'dashboard';

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_visitor_can_not_create_a_post()
    {
        $this->get(route(self::CREATE_POST_PAGE_NAME))
            ->assertRedirectToRoute('login');
        $this->post(route(self::STORE_POST_PAGE_NAME))
            ->assertRedirectToRoute('login');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_user_can_create_post()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->get(route(self::CREATE_POST_PAGE_NAME))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Posts/Create'));
        $post = Post::factory()->make();
        $this->post(route(self::STORE_POST_PAGE_NAME), $post->toArray())
            ->assertRedirectContains(self::DASHBOARD_PAGE_NAME);
        $this->assertDatabaseHas('posts',
            array_merge($post->only('title', 'description'), ['user_id' => $user->id])
        );
    }

    public function test_post_store_validation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->post(route(self::STORE_POST_PAGE_NAME))
            ->assertRedirect()
            ->assertSessionHasErrors()
            ->assertSessionHasErrors(
                [
                    'title' => 'The title field is required.',
                    'description' => 'The description field is required.',
                ]
            );

        $this->post(route(self::STORE_POST_PAGE_NAME), ['title' => 'a', 'description' => 'b'])
            ->assertRedirect()
            ->assertSessionHasErrors()
            ->assertSessionHasErrors(
                [
                    'title' => 'The title must be between 3 and 255 characters.',
                    'description' => 'The description must be between 30 and 600 characters.',
                ]
            );
    }

    public function test_post_store_success()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->post(route(self::STORE_POST_PAGE_NAME), Post::factory()->make()->toArray())
            ->assertRedirect()
            ->assertSessionHas('message');
    }
}
