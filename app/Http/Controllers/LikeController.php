<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Notifications\PostLikedNotification;

class LikeController extends Controller
{
    //
    public function toggle(Post $post)
    {
        $user = auth()->user();

        $like = $post->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $post->likes()->create([
                'user_id' => $user->id,
            ]);

            $liked = true;

            if ($post->user_id !== $user->id) {
                $post->user->notify(new PostLikedNotification($post, $user));
            }
        }

        return response()->json([
            'liked' => $liked,
            'count' => $post->likes()->count(),
        ]);
    }
}
