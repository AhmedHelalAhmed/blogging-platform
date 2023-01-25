<?php

namespace App\Http\Controllers;

use App\enums\SortByPublicationDateEnum;
use App\Http\Requests\WelcomeRequest;
use App\Services\PostService;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    private PostService $service;

    /**
     * @param PostService $service
     */
    public function __construct(PostService $service)
    {
        $this->service = $service;
    }

    /**
     * @param WelcomeRequest $request
     * @return \Inertia\Response
     */
    public function __invoke(WelcomeRequest $request)
    {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'posts' => $this->service->getAll($request->validated(), $request->input('page', 1)),
            'sortByPublicationDate' => $request->input('sort.published_at'),
            'optionsForSort' => SortByPublicationDateEnum::getOptions()
        ]);
    }
}
