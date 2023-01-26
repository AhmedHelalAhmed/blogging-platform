<?php

namespace Tests\Feature;

use App\enums\SortByPublicationDateEnum;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class WelcomeTest extends TestCase
{
    use LazilyRefreshDatabase;

    const WELCOME_PAGE_NAME = 'welcome';

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_welcome_page_ok_with_correct_vue_component()
    {
        $this->get(route(self::WELCOME_PAGE_NAME))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Welcome')->has('optionsForSort'));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_welcome_page_return_posts_pagianted()
    {
        $url = route(self::WELCOME_PAGE_NAME);
        Post::factory(12)->create();
        $this->get($url)
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('posts')
                ->has('posts.data.0', fn ($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                )
                ->has('posts.data', Post::PAGE_SIZE)
            );
        $this->get($url.'?'.http_build_query([
            'page' => 2,
        ]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('posts')
                ->has('posts.data.0', fn ($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                )
                ->has('posts.data', 2)
            );
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_welcome_page_sort_by_publication_date()
    {
        $url = route(self::WELCOME_PAGE_NAME);
        $yesterdayPost = Post::factory()->create([
            'published_at' => Carbon::now()->subDay(),
        ]);
        $todayPost = Post::factory()->create([
            'published_at' => Carbon::now(),
        ]);

        $this->get($url.'?'.http_build_query([
            'sort' => [
                'published_at' => SortByPublicationDateEnum::NEW_TO_OLD->value,
            ],
        ]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('posts')
                ->has('posts.data.0', fn ($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $todayPost->title)
                    ->where('description', $todayPost->description)
                )
                ->has('posts.data.1', fn ($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $yesterdayPost->title)
                    ->where('description', $yesterdayPost->description)
                )
                ->has('posts.data', 2)
            );

        $this->get($url.'?'.http_build_query([
            'sort' => [
                'published_at' => SortByPublicationDateEnum::OLD_TO_NEW->value,
            ],
        ]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('posts')
                ->has('posts.data.0', fn ($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $yesterdayPost->title)
                    ->where('description', $yesterdayPost->description)
                )
                ->has('posts.data.1', fn ($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $todayPost->title)
                    ->where('description', $todayPost->description)
                )
                ->has('posts.data', 2)
            );
    }

    public function test_welcome_page_sort_by_publication_date_in_case_of_api_posts()
    {
        $url = route(self::WELCOME_PAGE_NAME);
        $todayPost = Post::factory()->create([
            'published_at' => Carbon::now(),
        ]);
        $yesterdayPost = Post::factory()->create([
            'published_at' => Carbon::now()->subDay(),
        ]);
        $this->get($url.'?'.http_build_query([
            'sort' => [
                'published_at' => SortByPublicationDateEnum::NEW_TO_OLD->value,
            ],
        ]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('posts')
                ->has('posts.data.0', fn ($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $todayPost->title)
                    ->where('description', $todayPost->description)
                )
                ->has('posts.data.1', fn ($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $yesterdayPost->title)
                    ->where('description', $yesterdayPost->description)
                )
                ->has('posts.data', 2)
            );

        $this->get($url.'?'.http_build_query([
            'sort' => [
                'published_at' => SortByPublicationDateEnum::OLD_TO_NEW->value,
            ],
        ]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('posts')
                ->has('posts.data.0', fn ($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $yesterdayPost->title)
                    ->where('description', $yesterdayPost->description)
                )
                ->has('posts.data.1', fn ($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $todayPost->title)
                    ->where('description', $todayPost->description)
                )
                ->has('posts.data', 2)
            );
    }

    public function test_welcome_page_sort_by_publication_date_with_pagination()
    {
        $url = route(self::WELCOME_PAGE_NAME);
        Post::factory(11)->create([
            'published_at' => Carbon::now()->subDay(),
        ]);
        $todayPost = Post::factory()->create([
            'published_at' => Carbon::now(),
        ]);

        $this->get($url.'?'.http_build_query([
            'sort' => [
                'published_at' => SortByPublicationDateEnum::NEW_TO_OLD->value,
            ],
        ]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('posts')
                ->has('posts.data.0', fn ($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                    ->where('title', $todayPost->title)
                    ->where('description', $todayPost->description)
                )
                ->has('posts.data', 10)
            );

        $this->get($url.'?'.http_build_query([
            'page' => 2,
            'sort' => [
                'published_at' => SortByPublicationDateEnum::NEW_TO_OLD->value,
            ],
        ]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('posts')
                ->has('posts.data.0', fn ($page) => $page
                    ->has('title')
                    ->has('description')
                    ->has('published_at')
                )
                ->has('posts.data', 2)
            );
    }
}
