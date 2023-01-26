<?php

namespace Tests\Feature\Dashboard;

use App\Models\Post;
use App\Models\User;
use App\Services\TextInputFilterService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
    public function test_user_can_see_his_posts_in_dashboard_new_posts_first_by_default()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $anotherUser = User::factory()->create();
        $postsCount = 2;
        Post::factory($postsCount)->for($user)->create();
        $expectedPosts = DB::table('posts')
            ->select('title', 'description', 'published_at')
            ->where('user_id', $user->id)
            ->orderBy('published_at', 'desc')
            ->get();
        Post::factory(1)->for($anotherUser)->create();
        $this->get(route(self::DASHBOARD_PAGE_NAME))
            ->assertOk()
            ->assertInertia(fn($page) => $page->has('posts')
                ->has('posts.data.0', fn($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $expectedPosts[0]->title)
                    ->where('description', Str::limit(TextInputFilterService::displayFilter($expectedPosts[0]->description), Post::LIMIT_LENGTH_FOR_DESCRIPTION))
                )
                ->has('posts.data.1', fn($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $expectedPosts[1]->title)
                    ->where('description', Str::limit(TextInputFilterService::displayFilter($expectedPosts[1]->description), Post::LIMIT_LENGTH_FOR_DESCRIPTION))
                )
                ->has('posts.data', $postsCount));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_user_can_see_his_posts_new_posts_first_by_default_with_pagination()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Post::factory(12)->for($user)->create();
        $expectedPosts = DB::table('posts')
            ->select('title', 'description', 'published_at')
            ->where('user_id', $user->id)
            ->orderBy('published_at', 'desc')
            ->get();
        $this->get(route(self::DASHBOARD_PAGE_NAME))
            ->assertOk()
            ->assertInertia(fn($page) => $page->has('posts')
                ->has('posts.data.0', fn($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $expectedPosts[0]->title)
                    ->where('description', Str::limit(TextInputFilterService::displayFilter($expectedPosts[0]->description), Post::LIMIT_LENGTH_FOR_DESCRIPTION))
                )
                ->has('posts.data.1', fn($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $expectedPosts[1]->title)
                    ->where('description', Str::limit(TextInputFilterService::displayFilter($expectedPosts[1]->description), Post::LIMIT_LENGTH_FOR_DESCRIPTION))
                )
                ->has('posts.data', Post::PAGE_SIZE));
        $this->get(route(self::DASHBOARD_PAGE_NAME) . '?' . http_build_query([
                'page' => 2,
            ]))
            ->assertOk()
            ->assertInertia(fn($page) => $page->has('posts')
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
