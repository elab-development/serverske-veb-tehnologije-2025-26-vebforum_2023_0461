<?php

namespace App\Http\Controllers;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Topic::query();

    if ($request->filled('title')) {
        $query->where('title', 'like', '%' . $request->input('title') . '%');
    }

    return TopicResource::collection($query->paginate(10));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->json([
            'message' => 'Create topic form'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function store(Request $request)
{
    $topic = Topic::create($request->all());

    return new TopicResource($topic);
}

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function show(Topic $topic)
{
    return new TopicResource($topic);
}

    public function posts(Topic $topic)
    {
        return response()->json($topic->posts);
    }

    public function search(Request $request)
{
    $data = $request->validate([
        'query' => 'required|string'
    ]);

    $topics = Topic::where('title', 'like', '%' . $data['query'] . '%')
        ->orWhere('body', 'like', '%' . $data['query'] . '%')
        ->get();

    return TopicResource::collection($topics);
}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function edit(Topic $topic)
    {
        return response()->json([
            'message' => 'Edit topic form'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Topic $topic)
{
    $topic->update($request->all());

    return new TopicResource($topic);
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function destroy(Topic $topic)
    {
        $topic->delete();
        return response()->json([
            'message' => 'Topic deleted'
        ]);
    }
}
