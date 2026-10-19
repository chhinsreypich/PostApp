<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-2xl text-gray-800">
                Dashboard
            </h2>

            <form action="{{ route('posts.index') }}" method="GET" class="flex items-center">
                <input type="text" name="search" placeholder="Search posts..."
                    class="border border-gray-300 rounded-lg py-2 px-4 w-72 focus:outline-none focus:ring-2 focus:ring-blue-400">

                <button type="submit" class="ml-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Search
                </button>
            </form>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-8 space-y-6">

        {{-- Posts --}}
        @foreach ($posts as $post)
            <div onclick="goToPost({{ $post->id }})"
                class="bg-white shadow-sm hover:shadow-md transition rounded-2xl p-5 cursor-pointer">

                <!-- Header -->
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-4">
                        <h2 class="text-lg font-semibold text-gray-800">
                            {{ $post->user->username }}
                        </h2>
                        <p class="text-gray-400 text-sm">
                            {{ $post->created_at->format('F j, Y') }}
                        </p>
                    </div>

                    <!-- 3 dots -->
                    <div class="relative" onclick="event.stopPropagation()">
                        <button onclick="toggleMenu('menu-{{ $post->id }}')"
                            class="p-1 rounded-full hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v.01M12 12v.01M12 18v.01" />
                            </svg>
                        </button>

                        <div id="menu-{{ $post->id }}"
                            class="hidden absolute right-0 mt-2 w-32 bg-white border rounded-lg shadow-md z-10">
                            <a href="{{ route('posts.edit', $post->id) }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Edit
                            </a>

                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="mt-4">
                    <h3 class="text-md font-semibold text-gray-900">
                        {{ $post->title }}
                    </h3>
                    <p class="text-gray-600 mt-2 leading-relaxed">
                        {{ $post->content }}
                    </p>
                </div>

                <!-- Actions -->
                <div class="mt-4 flex items-center gap-6 text-gray-500 text-sm" onclick="event.stopPropagation()">

                    <!-- Like -->
                    <button onclick="toggleLike({{ $post->id }})" id="like-btn-{{ $post->id }}"
                        class="flex items-center gap-1 hover:text-red-500">

                        <span id="like-icon-{{ $post->id }}">
                            @if ($post->likes->contains('user_id', auth()->id()))
                                ❤️
                            @else
                                🤍
                            @endif
                        </span>

                        <span id="like-count-{{ $post->id }}">
                            {{ $post->likes->count() }}
                        </span>
                    </button>

                    <!-- Comment toggle -->
                    <button onclick="toggleComment('comment-{{ $post->id }}')"
                        class="flex items-center gap-1 hover:text-blue-500">
                        💬 Comment ({{ $post->comments->count() }})
                    </button>
                </div>

                <!-- Comment Section -->
                <div id="comment-{{ $post->id }}" class="hidden mt-4" onclick="event.stopPropagation()">

                    <form action="{{ route('comments.store', $post->id) }}" method="POST">
                        @csrf
                        <input type="text" name="content" placeholder="Write a comment..."
                            class="w-full border rounded-lg px-4 py-2">

                        <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-1 rounded-lg text-sm">
                            Post
                        </button>
                    </form>

                    <div class="mt-3 space-y-2">
                        @foreach ($post->comments as $comment)
                            <div class="bg-gray-100 p-2 rounded-lg text-sm">
                                <strong>{{ $comment->user->username }}:</strong>
                                {{ $comment->content }}
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        @endforeach
    </div>

    {{-- Scripts --}}
    <script>
        function toggleMenu(id) {
            const menu = document.getElementById(id);
            menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            document.querySelectorAll('[id^="menu-"]').forEach(menu => {
                if (!menu.contains(event.target) && !menu.previousElementSibling.contains(event.target)) {
                    menu.classList.add('hidden');
                }
            });
        });

        function goToPost(id) {
            window.location.href = "/posts/" + id;
        }

        function toggleComment(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }

        function toggleLike(postId) {
    fetch(`/posts/${postId}/like`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            "Accept": "application/json",
            "Content-Type": "application/json"
        }
    })
    .then(res => res.json())
    .then(data => {
        const icon = document.getElementById(`like-icon-${postId}`);
        const count = document.getElementById(`like-count-${postId}`);

        count.innerText = data.count;

        if (data.liked) {
            icon.innerHTML = "❤️";
        } else {
            icon.innerHTML = "🤍";
        }
    });
}
    </script>
</x-app-layout>
