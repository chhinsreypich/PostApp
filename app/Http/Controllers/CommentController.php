<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Notifications\NewCommentNotification;
use App\Notifications\NewReplyNotification;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $comment = Comment::create([
            'content' => $request->content,
            'user_id' => auth()->id(),
            'post_id' => $postId,
            'parent_id' => $request->parent_id,
        ]);

        //  CASE 1: Reply to a comment
        if ($comment->parent_id) {

            $parentComment = Comment::find($comment->parent_id);
            $commentOwner = $parentComment->user;

            // don't notify yourself
            if ($commentOwner->id !== auth()->id()) {
                $commentOwner->notify(new NewReplyNotification($comment));
            }
        } else {

            // CASE 2: Normal comment → notify post owner
            $postOwner = $comment->post->user;

            if ($postOwner->id !== auth()->id()) {
                $postOwner->notify(new NewCommentNotification($comment));
            }
        }
        return back();
    }

    //

    // public function store(Request $request, $postId)
    // {
    //     $request->validate([
    //         'content' => 'required|string|max:255',
    //     ]);

    //     $comment = new Comment();
    //     $comment->content = $request->input('content');
    //     $comment->user_id = auth()->id();
    //     $comment->post_id = $postId;
    //     $comment->parent_id = $request->input('parent_id');
    //     $comment->save();

    //     return redirect()->route('posts.show', ['post' => $postId])->with('success', 'Comment added successfully.');
    // }

    public function edit(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to edit this comment.');
        }

        return view('comments.edit', compact('comment'));
    }

    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to update this comment.');
        }

        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $comment->content = $request->input('content');
        $comment->save();

        return redirect()->route('posts.show', ['post' => $comment->post_id])->with('success', 'Comment updated successfully.');
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to delete this comment.');
        }

        $comment->delete();
        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }
}
