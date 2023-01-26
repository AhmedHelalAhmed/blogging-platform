<?php

namespace Tests\Feature\Posts;

use App\Models\Post;
use App\Models\User;
use App\Services\PostImportingService;
use App\Services\TextInputFilterService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PostsImportTest extends TestCase
{
    use LazilyRefreshDatabase;

    const SAMPLE_DATA_FROM_API_OF_FEED = [
        [
            'id' => 15,
            'title' => 'Tesla vs. the S&P 500: Which Is the Better First Investment?',
            'description' => 'Choose your first investment with the goal of protecting your money and your investing confidence.',
            'publishedAt' => '2022-08-31T09:52:00Z',
        ],
        [
            'id' => 13,
            'title' => 'Climate Crisis Forces China To Ration Electricity',
            'description' => "Provinces in China are being forced to reduce power consumption as forest fires, droughts, and heatwaves ravage the country. The Guardian reports: here were still some streetlights on the Bund, one of the main roads in central Shanghai. But the decorative lig\u2026",
            'publishedAt' => '2022-08-31T10:00:00Z',
        ],
    ];

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_import_post()
    {
        $this->assertEquals(0, Post::count());
        Http::fake([
            config('app.feed') => Http::response(['articles' => self::SAMPLE_DATA_FROM_API_OF_FEED]),
        ]);
        $this->simulateSchedulerRun();
        foreach (self::SAMPLE_DATA_FROM_API_OF_FEED as $post) {
            $this->assertDatabaseHas('posts', [
                'title' => TextInputFilterService::storeFilter($post['title']),
                'description' => TextInputFilterService::storeFilter($post['description']),
                'published_at' => Carbon::parse($post['publishedAt']),
                'user_id' => User::ADMIN_USER_ID,
            ]);
            $this->assertDatabaseHas('external_posts_ids', ['external_id' => $post['id']]);
        }

        $this->assertEquals(count(self::SAMPLE_DATA_FROM_API_OF_FEED), Post::count());
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_import_posts_not_duplicate()
    {
        $this->assertEquals(0, Post::count());
        Http::fake([
            config('app.feed') => Http::response(['articles' => self::SAMPLE_DATA_FROM_API_OF_FEED]),
        ]);
        // simulate the same data comes again and again
        $this->simulateSchedulerRun();
        $this->simulateSchedulerRun();
        $this->simulateSchedulerRun();
        foreach (self::SAMPLE_DATA_FROM_API_OF_FEED as $post) {
            $this->assertDatabaseHas('posts', [
                'title' => TextInputFilterService::storeFilter($post['title']),
                'description' => TextInputFilterService::storeFilter($post['description']),
                'published_at' => Carbon::parse($post['publishedAt']),
            ]);
        }

        $this->assertEquals(count(self::SAMPLE_DATA_FROM_API_OF_FEED), Post::count());
    }

    public function test_import_posts_validation_no_id_for_external_post()
    {
        Log::shouldReceive('error')->once();
        $this->assertEquals(0, Post::count());
        Http::fake([
            config('app.feed') => Http::response(['articles' => [
                [
                    'title' => 'Tesla vs. the S&P 500: Which Is the Better First Investment?',
                    'description' => 'Choose your first investment with the goal of protecting your money and your investing confidence.',
                    'publishedAt' => '2022-08-31T09:52:00Z',
                ],

            ]]),
        ]);
        $this->simulateSchedulerRun();
        $this->assertEquals(0, Post::count());
    }

    public function test_import_posts_validation_title_validation()
    {
        Log::shouldReceive('error')->once();

        $this->assertEquals(0, Post::count());
        Http::fake([
            config('app.feed') => Http::response(['articles' => [
                [
                    'id' => 1,
                    'title' => 'a',
                    'description' => 'Choose your first investment with the goal of protecting your money and your investing confidence.',
                    'publishedAt' => '2022-08-31T09:52:00Z',
                ],

            ]]),
        ]);
        $this->simulateSchedulerRun();
        $this->assertEquals(0, Post::count());
    }

    public function test_import_posts_description_validation()
    {
        Log::shouldReceive('error')->once();

        $this->assertEquals(0, Post::count());
        Http::fake([
            config('app.feed') => Http::response(['articles' => [
                [
                    'id' => 1,
                    'title' => 'Tesla vs. the S&P 500: Which Is the Better First Investment?',
                    'publishedAt' => '2022-08-31T09:52:00Z',
                ],

            ]]),
        ]);
        $this->simulateSchedulerRun();
        $this->assertEquals(0, Post::count());
    }

    public function test_import_posts_published_at_validation()
    {
        Log::shouldReceive('error')->once();

        $this->assertEquals(0, Post::count());
        Http::fake([
            config('app.feed') => Http::response(['articles' => [
                [
                    'id' => 1,
                    'title' => 'Tesla vs. the S&P 500: Which Is the Better First Investment?',
                    'description' => 'Choose your first investment with the goal of protecting your money and your investing confidence.',
                    'publishedAt' => null,
                ],

            ]]),
        ]);
        $this->simulateSchedulerRun();
        $this->assertEquals(0, Post::count());
    }

    private function simulateSchedulerRun()
    {
        (new PostImportingService)->__invoke();
    }
}
