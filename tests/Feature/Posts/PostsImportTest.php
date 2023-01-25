<?php

namespace Tests\Feature\Posts;

use App\Models\Post;
use App\Models\User;
use App\Services\PostImportingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

// TODO cover testins insert external ids in external ids table
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
                'title' => $post['title'],
                'description' => $post['description'],
                'published_at' => Carbon::parse($post['publishedAt']),
                'user_id' => User::ADMIN_USER_ID,
            ]);
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
                'title' => $post['title'],
                'description' => $post['description'],
                'published_at' => Carbon::parse($post['publishedAt']),
            ]);
        }

        $this->assertEquals(count(self::SAMPLE_DATA_FROM_API_OF_FEED), Post::count());
    }

    private function simulateSchedulerRun()
    {
        (new PostImportingService)->__invoke();
    }
}
