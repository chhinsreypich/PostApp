<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-2xl text-gray-800">
                My Posts
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
                    <form action="{{ route('posts.like', $post->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center gap-1 hover:text-red-500">

                            @if ($post->likes->contains('user_id', auth()->id()))
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                                    class="w-5 h-5 text-red-500">
                                    <path
                                        d="M12 21s-6.716-4.35-9.5-8.5C.5 9 2 5.5 5.5 5.5c2 0 3.5 1.5 3.5 1.5s1.5-1.5 3.5-1.5C17 5.5 18.5 9 21.5 12.5 18.716 16.65 12 21 12 21z" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21.752 6.223c-.542-1.11-1.768-1.973-3.266-1.973-1.042 0-2.05.457-2.736 1.235L12 7.246l-3.75-1.761C7.564 4.707 6.556 4.25 5.514 4.25c-1.498 0-2.724.863-3.266 1.973-.56 1.148-.38 2.61.49 3.67L12 21l9.262-11.107c.87-1.06 1.05-2.522.49-3.67z" />
                                </svg>
                            @endif

                            <span>{{ $post->likes->count() }}</span>
                        </button>
                    </form>

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
    </script>
</x-app-layout>














{{-- <x-app-layout>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold mb-4">My Posts</h1>
                    @if ($posts->isEmpty())
                        <p>You have no posts yet.</p>
                    @else
                        <ul>
                            @foreach ($posts as $post)
                                <li class="mb-2">
                                    <a href="{{ route('posts.my-posts', $post) }}"
                                        class="text-blue-500 hover:underline">{{ $post->title }}</a>
                                    <span class="text-gray-600 text-sm"> -
                                        {{ $post->created_at->format('M d, Y') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}
