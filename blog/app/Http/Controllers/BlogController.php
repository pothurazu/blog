<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::when($request->search, function ($query) use ($request) {
            $search = $request->search;

            return $query->where('title', 'like', "%$search%")
                            ->orWhere('body', 'like', "%$search%");
                            })->with('tags', 'category', 'user')
                                ->withCount('comments')
                                ->published()
                                ->orderBy('id','desc')
                                ->simplePaginate(100000000000000);
                                $results = Post::where('is_published' ,'=', '1')->get();
                               
        return view('frontend.index', compact('posts','results'));
    }

    public function post(Post $post)
    {   
        $post = $post->load(['comments.user', 'tags', 'user', 'category']);
        $results = Post::inRandomOrder()->get();
        return view('frontend.post', compact('post','results'));
    }

    public function comment(Request $request, Post $post)
    {
        $this->validate($request, ['body' => 'required']);

        $post->comments()->create([
            'body' => $request->body,
        ]);
        
        flash()->overlay('Comment successfully created');

        return redirect("/posts/{$post->id}");
    }
}
