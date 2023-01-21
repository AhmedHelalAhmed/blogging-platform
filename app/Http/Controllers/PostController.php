<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Services\PostService;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Posts/Create');
    }

    /**
     * @param PostService $service
     * @param PostRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PostService $service, PostRequest $request)
    {
        $data = $request->validated();
        $status = $service->store(array_merge($data, ['user_id' => auth()->id(), 'published_at' => now()]));
        if (!$status) {
            Log::error('Can not create a post with data', ['data' => $data]);
        }
        return redirect()->route('dashboard');
    }
}
