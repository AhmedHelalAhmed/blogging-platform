<?php

namespace App\Http\Controllers;

use App\Http\Requests\WelcomeRequest;
use App\Services\PostService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    private PostService $service;

    public function __construct(PostService $service)
    {
        $this->service = $service;
    }

    public function __invoke(WelcomeRequest $request)
    {
        return Inertia::render('Dashboard', [
            'posts' => $this->service->getAll(['filter' => [
                'authorId' => auth()->id(),
            ]], $request->input('page', 1)),
        ]);
    }
}
