<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\RequiredUnless;

class PostController extends Controller
{
    //
    public function index(Request $request)
    {
        // $posts = $query->latest()->paginate(10);
        // $posts = Post::with(['user', 'comments.user', 'likes'])->latest()->get();
        // $posts = Post::all();
        // $posts = Post::latest()->paginate(10);

        // $posts = Post::when($request->search, function ($query, $search) {
        //     return $query->where('title', 'like', "%{$search}%")
        //         ->orWhere('content', 'like', "%{$search}%");
        // });

        $query = Post::with('user', 'comments');
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('username', 'like', "%{$search}%");
                    });
            });
        }
        $posts = $query->latest()->paginate(10);
        $posts->appends($request->all());

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $data['user_id'] = auth()->id();

        Post::create($data);
        return redirect()->route('posts.index')->with('success', 'Post created successfully.');
    }
    public function show(Post $post)
    {
        $post->load([
            'comments.user',
            'comments.replies.user'
        ]);
        return view('posts.show', compact('post'));
    }

    public function myPosts()
    {
        $posts = Post::where('user_id', auth()->id())->latest()->get();
        return view('posts.my-posts', compact('posts'));
    }

    public function edit(Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            return redirect()->route('posts.index')->with('error', 'Unauthorized action.');
        }
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {

        if (auth()->id() !== $post->user_id) {
            return redirect()->route('posts.index')->with('error', 'Unauthorized action.');
        }
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        $post['user_id'] = auth()->id();
        $post->update($data);
        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            return redirect()->route('posts.index')->with('error', 'Unauthorized action.');
        }
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }
}
