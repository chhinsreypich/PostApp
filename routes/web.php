<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Route::get('/dashboard', function () {
//     return view('user.index');
// })->middleware(['user', 'verified'])->name('index');

Route::get('/dashboard', function () {

    if (auth()->user()->role == 'admin') {
        return redirect()->route('admin.index');
    }

    return redirect()->route('posts.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.index');
    // Route::get('/admin', [AdminController::class, 'edit'])->name('admin.edit');
    // Route::patch('/admin', [AdminController::class, 'update'])->name('admin.update');
    // Route::delete('/admin', [AdminController::class, 'destroy'])->name('admin.destroy');

    // // Posts routes
    // Route::get('/dashboard', [PostController::class, 'index'])->name('posts.index');
    // Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    // Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    // Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    // Route::get('/my-posts', [PostController::class, 'myPosts'])->name('posts.my-posts');
    // Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    // Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    // Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // // likes routes
    // Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like');

    // // Comments routes
    // Route::get('/posts/{post}/comments', [CommentController::class, 'index'])->name('comments.index');
    // Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    // Route::get('/comments/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit');
    // Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    // Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // // Notifications routes
    // Route::get('/notifications', function () {
    //     $notifications = auth()->user()->notifications;
    //     return view('notifications.index', compact('notifications'));
    // })->name('notifications');
    // Route::get('/notifications/{id}', function ($id) {
    //     $notification = auth()->user()
    //         ->notifications()
    //         ->findOrFail($id);

    //     // mark as read
    //     $notification->markAsRead();

    //     // redirect to post
    //     return redirect()->route('posts.show', $notification->data['post_id']);
    // })->name('notifications.read');
});



Route::middleware(['auth', 'user'])->group(function () {
    // Route::get('/posts/{post}', [ProfileController::class, 'showPost'])->name('posts.show');

    // Posts routes
    // Route::get('/dashboard', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::get('/my-posts', [PostController::class, 'myPosts'])->name('posts.my-posts');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // likes routes
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like');

    // Comments routes
    Route::get('/posts/{post}/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/comments/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Notifications routes
    Route::get('/notifications', function () {
        $notifications = auth()->user()->notifications;
        return view('notifications.index', compact('notifications'));
    })->name('notifications');
    Route::get('/notifications/{id}', function ($id) {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        // mark as read
        $notification->markAsRead();

        // redirect to post
        return redirect()->route('posts.show', $notification->data['post_id']);
    })->name('notifications.read');
});





require __DIR__ . '/auth.php';
