<?php

namespace Tests\Feature\Dashboard;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    const DASHBOARD_PAGE_NAME = 'dashboard';

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_user_can_see_his_posts_in_dashboard()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $anotherUser = User::factory()->create();
        $postsCount = 2;
        $expectedPosts = Post::factory($postsCount)->for($user)->create();
        Post::factory(1)->for($anotherUser)->create();
        $this->get(route(self::DASHBOARD_PAGE_NAME))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('posts')
                ->has('posts.data.0', fn ($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $expectedPosts[0]['title'])
                    ->where('description', $expectedPosts[0]['description'])
                )
                ->has('posts.data.1', fn ($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $expectedPosts[1]['title'])
                    ->where('description', $expectedPosts[1]['description'])
                )
                ->has('posts.data', $postsCount));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_user_can_see_his_posts_with_pagination()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $expectedPosts = Post::factory(12)->for($user)->create();
        $this->get(route(self::DASHBOARD_PAGE_NAME))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('posts')
                ->has('posts.data.0', fn ($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $expectedPosts[0]['title'])
                    ->where('description', $expectedPosts[0]['description'])
                )
                ->has('posts.data.1', fn ($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $expectedPosts[1]['title'])
                    ->where('description', $expectedPosts[1]['description'])
                )
                ->has('posts.data', Post::PAGE_SIZE));
        $this->get(route(self::DASHBOARD_PAGE_NAME).'?'.http_build_query([
            'page' => 2,
        ]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('posts')
                ->has('posts.data', 12 - Post::PAGE_SIZE));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_visitor_user_can_not_see_dashboard()
    {
        $this->get(route(self::DASHBOARD_PAGE_NAME))
            ->assertRedirectToRoute('login');
    }
}
