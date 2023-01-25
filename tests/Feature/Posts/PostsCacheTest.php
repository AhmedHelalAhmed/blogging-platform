<?php

namespace Tests\Feature\Posts;

use App\enums\SortByPublicationDateEnum;
use App\Models\Post;
use App\Models\User;
use App\Services\CachingPostService;
use App\Services\PostImportingService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class PostsCacheTest extends TestCase
{
    use LazilyRefreshDatabase;

    const WELCOME_PAGE_NAME = 'welcome';

    const FIRST_PAGE = 1;

    const STORE_POST_PAGE_NAME = 'posts.store';

    private $user;

    public function test_when_visit_posts_page_it_does_not_cache_when_there_is_no_posts()
    {
        $service = new CachingPostService;
        $this->assertFalse(
            $service->existsInCache(self::FIRST_PAGE, ['sort' => ['published_at' => SortByPublicationDateEnum::NEW_TO_OLD->value]])
        );
        $this->visitPage();
        $this->assertFalse(
            $service->existsInCache(self::FIRST_PAGE, ['sort' => ['published_at' => SortByPublicationDateEnum::NEW_TO_OLD->value]])
        );
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_when_visit_posts_page_it_return_from_cache_in_second_request_by_default_new_then_old_sort_published_date()
    {
        $service = new CachingPostService;
        $this->assertFalse(
            $service->existsInCache(self::FIRST_PAGE, [])
        );
        $this
            ->seedSomePosts();

        $posts = [];
        $this->visitPage()
            ->assertInertia(function (AssertableInertia $page) use (&$posts) {
                $posts = Arr::get($page->toArray(), 'props.posts');
            });
        // the key set successfully set to cache

        $this->assertTrue(
            $service->existsInCache(self::FIRST_PAGE, [])
        );

        // the values in cache match the one that comes from database
        $this->assertSame(
            $posts,
            $service->getFromCache()->toArray()
        );
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_when_visit_posts_page_it_return_from_cache_in_second_request_old_then_new_sort_published_date()
    {
        $service = new CachingPostService;
        $this->assertFalse(
            $service->existsInCache(self::FIRST_PAGE, ['sort' => ['published_at' => SortByPublicationDateEnum::OLD_TO_NEW->value]])
        );
        $this
            ->seedSomePosts();

        $posts = [];
        $this->visitPage(self::FIRST_PAGE, SortByPublicationDateEnum::OLD_TO_NEW->value)
            ->assertInertia(function (AssertableInertia $page) use (&$posts) {
                $posts = Arr::get($page->toArray(), 'props.posts');
            });

        // the key set successfully set to cache
        $this->assertTrue(
            $service->existsInCache(self::FIRST_PAGE, ['sort' => ['published_at' => SortByPublicationDateEnum::OLD_TO_NEW->value]])
        );

        // the values in cache match the one that comes from database
        $this->assertSame(
            $posts,
            $service->getFromCache()->toArray()
        );
    }

    public function test_cache_expiry()
    {
        $service = new CachingPostService;
        $this->assertFalse(
            $service->existsInCache(self::FIRST_PAGE, [])
        );
        $this
            ->seedSomePosts();

        $this->visitPage();
        $this->visitPage(self::FIRST_PAGE, SortByPublicationDateEnum::OLD_TO_NEW->value);
        // the key set successfully set to cache
        $this->assertTrue(
            $service->existsInCache(self::FIRST_PAGE, [])
        );
        $this->assertTrue(
            $service->existsInCache(self::FIRST_PAGE, ['sort' => ['published_at' => SortByPublicationDateEnum::OLD_TO_NEW->value]])
        );

        // cache expire in 1 hour so after 1 hour and 1 seconds it should not exist
        $this->travel(1)->hour();
        $this->travel(1)->second();
        $this->assertFalse(
            $service->existsInCache(self::FIRST_PAGE, [])
        );
        $this->assertFalse(
            $service->existsInCache(self::FIRST_PAGE, ['sort' => ['published_at' => SortByPublicationDateEnum::OLD_TO_NEW->value]])
        );
    }

    public function test_when_a_user_create_a_post_cache_will_reset()
    {
        $service = new CachingPostService;
        $this->assertFalse(
            $service->existsInCache(self::FIRST_PAGE, [])
        );
        $this
            ->seedSomePosts();
        $this->visitPage();
        $this->visitPage(self::FIRST_PAGE, SortByPublicationDateEnum::OLD_TO_NEW->value);

        // the key set successfully set to cache
        $this->assertTrue(
            $service->existsInCache(self::FIRST_PAGE, [])
        );
        $this->assertTrue(
            $service->existsInCache(self::FIRST_PAGE, ['sort' => ['published_at' => SortByPublicationDateEnum::OLD_TO_NEW->value]])
        );

        $this->createPost();

        $this->assertFalse(
            $service->existsInCache(self::FIRST_PAGE, [])
        );
        $this->assertFalse(
            $service->existsInCache(self::FIRST_PAGE, ['sort' => ['published_at' => SortByPublicationDateEnum::OLD_TO_NEW->value]])
        );
    }

    private function createPost()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->post(route(self::STORE_POST_PAGE_NAME), Post::factory()->make()->toArray())
            ->assertRedirect()
            ->assertSessionHas('message');
    }

    public function test_when_api_import_posts_job_from_api_run_cache_will_reset()
    {
        $service = new CachingPostService;
        $this->assertFalse(
            $service->existsInCache(self::FIRST_PAGE, [])
        );
        $this
            ->seedSomePosts();

        $this->visitPage();
        $this->visitPage(self::FIRST_PAGE, SortByPublicationDateEnum::OLD_TO_NEW->value);
        // the key set successfully set to cache
        $this->assertTrue(
            $service->existsInCache(self::FIRST_PAGE, [])
        );
        $this->assertTrue(
            $service->existsInCache(self::FIRST_PAGE, ['sort' => ['published_at' => SortByPublicationDateEnum::OLD_TO_NEW->value]])
        );

        $this->simulateSchedulerRunWithSomePostsImportedFromAPI();

        $this->assertFalse(
            $service->existsInCache(self::FIRST_PAGE, [])
        );
        $this->assertFalse(
            $service->existsInCache(self::FIRST_PAGE, ['sort' => ['published_at' => SortByPublicationDateEnum::OLD_TO_NEW->value]])
        );
    }

    private function simulateSchedulerRunWithSomePostsImportedFromAPI()
    {
        Http::fake([
            config('app.feed') => Http::response(['articles' => PostsImportTest::SAMPLE_DATA_FROM_API_OF_FEED]),
        ]);
        (new PostImportingService)->__invoke();
    }

    /**
     * @param  int  $page
     * @param  int  $sort
     */
    private function visitPage(int $page = self::FIRST_PAGE, int $sort = null)
    {
        if (is_null($sort)) {
            $sort = SortByPublicationDateEnum::getDefaultSort();
        }

        return $this->get(route(self::WELCOME_PAGE_NAME).'?'.http_build_query([
            'page' => $page,
            'sort' => [
                'published_at' => $sort,
            ],
        ]));
    }

    /**
     * @return void
     */
    private function seedSomePosts()
    {
        // Add some posts for test
        Post::factory()->create([
            'published_at' => Carbon::now(),
        ]);

        // simulate post come from api
        Post::factory()->create([
            'published_at' => Carbon::now()->subDay(),
        ]);

        // Add more tests
        Post::factory(20)->create();
    }
}
