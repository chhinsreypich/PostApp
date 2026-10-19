<x-app-layout>

    <div class="py-6 sm:py-12">
        <div class="max-w-3xl mx-auto px-3 sm:px-0 space-y-6 pb-24">

            <!-- Post -->
            <div class="bg-white shadow rounded-2xl p-4 sm:p-6">
                <div class="flex items-start sm:items-center gap-3">

                    <div
                        class="w-9 h-9 sm:w-10 sm:h-10 bg-gray-300 rounded-full flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($post->user->username, 0, 1)) }}
                    </div>

                    <div>
                        <h1 class="font-semibold text-gray-800 text-sm sm:text-base">
                            {{ $post->user->username }}
                        </h1>
                        <p class="text-xs text-gray-400">
                            {{ $post->created_at->format('M d, Y') }}
                        </p>
                    </div>
                </div>

                <h2 class="text-lg sm:text-xl font-bold mt-4">
                    {{ $post->title }}
                </h2>

                <p class="text-gray-600 mt-2 text-sm sm:text-base leading-relaxed break-words">
                    {{ $post->content }}
                </p>
            </div>

            <!-- Comments -->
            <div class="bg-white shadow rounded-2xl p-3 sm:p-4 space-y-4">

                <h3 class="font-semibold text-gray-800 text-sm sm:text-base">
                    Comments ({{ $post->comments->count() }})
                </h3>

                @forelse ($post->comments->whereNull('parent_id') as $comment)
                    <div class="flex gap-2 sm:gap-3">

                        <!-- Avatar -->
                        <div
                            class="w-7 h-7 sm:w-8 sm:h-8 bg-gray-300 rounded-full flex items-center justify-center text-white text-xs sm:text-sm flex-shrink-0">
                            {{ strtoupper(substr($comment->user->username, 0, 1)) }}
                        </div>

                        <div class="flex-1 min-w-0">

                            <!-- Comment bubble -->
                            <div class="bg-gray-100 rounded-2xl px-3 sm:px-4 py-2">

                                <div class="flex justify-between items-start gap-2">
                                    <span class="font-semibold text-xs sm:text-sm text-gray-800 truncate">
                                        {{ $comment->user->username }}
                                    </span>

                                    <span class="text-[10px] sm:text-xs text-gray-400 whitespace-nowrap">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <!-- VIEW -->
                                <div id="view-{{ $comment->id }}">
                                    <p class="text-xs sm:text-sm text-gray-700 mt-1 break-words">
                                        {{ $comment->content }}
                                    </p>
                                </div>

                                <!-- EDIT -->
                                <div id="edit-{{ $comment->id }}" class="hidden mt-2">
                                    <form action="{{ route('comments.update', $comment->id) }}" method="POST"
                                        class="flex flex-col sm:flex-row gap-2">

                                        @csrf
                                        @method('PUT')

                                        <input type="text" name="content"
                                            value="{{ $comment->content }}"
                                            class="flex-1 border rounded-full px-3 py-2 text-xs sm:text-sm w-full">

                                        <button class="bg-blue-500 text-white px-3 py-2 rounded-full text-xs sm:text-sm">
                                            Save
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-wrap gap-3 text-[11px] sm:text-xs text-gray-500 mt-1 ml-1 sm:ml-2">

                                <button onclick="toggleReply({{ $comment->id }})"
                                    class="hover:text-blue-500">
                                    Reply
                                </button>

                                @if ($comment->user_id === auth()->id())
                                    <button onclick="toggleEdit({{ $comment->id }})"
                                        class="hover:text-blue-500">
                                        Edit
                                    </button>

                                    <form action="{{ route('comments.destroy', $comment->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500">
                                            Delete
                                        </button>
                                    </form>
                                @endif

                            </div>

                            <!-- Reply form -->
                            <form id="reply-{{ $comment->id }}"
                                action="{{ route('comments.store', $post->id) }}"
                                method="POST"
                                class="hidden mt-2 ml-2 sm:ml-4 flex flex-col sm:flex-row gap-2">

                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                                <input type="text" name="content"
                                    placeholder="Write a reply..."
                                    class="flex-1 border rounded-full px-3 py-2 text-xs sm:text-sm">

                                <button class="bg-blue-500 text-white px-3 py-2 rounded-full text-xs sm:text-sm">
                                    Send
                                </button>
                            </form>

                            <!-- Replies -->
                            @if ($comment->replies->count())
                                <div class="ml-4 sm:ml-8 mt-4 border-l-2 border-gray-200 pl-3 sm:pl-4 space-y-3">
                            @endif

                            @foreach ($comment->replies as $reply)
                                <div class="flex gap-2 sm:gap-3">

                                    <div
                                        class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-gray-300 flex items-center justify-center text-[10px] sm:text-xs text-white flex-shrink-0">
                                        {{ strtoupper(substr($reply->user->username, 0, 1)) }}
                                    </div>

                                    <div class="flex-1 min-w-0">

                                        <div class="bg-gray-100 rounded-2xl px-3 py-2">

                                            <div class="flex justify-between items-start gap-2">
                                                <span class="font-semibold text-xs text-gray-800 truncate">
                                                    {{ $reply->user->username }}
                                                </span>

                                                <span class="text-[10px] text-gray-400 whitespace-nowrap">
                                                    {{ $reply->created_at->diffForHumans() }}
                                                </span>
                                            </div>

                                            <p class="text-xs text-gray-700 mt-1 break-words">
                                                {{ $reply->content }}
                                            </p>

                                        </div>

                                        <!-- reply actions -->
                                        @if ($reply->user_id === auth()->id())
                                            <div class="flex gap-3 text-[10px] sm:text-xs text-gray-500 mt-1 ml-2">
                                                <button onclick="toggleReplyEdit({{ $reply->id }})"
                                                    class="hover:text-blue-500">
                                                    Edit
                                                </button>

                                                <form action="{{ route('comments.destroy', $reply->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-500">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            @endforeach

                            @if ($comment->replies->count())
                                </div>
                            @endif

                        </div>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm">No comments yet.</p>
                @endforelse

            </div>
        </div>
    </div>

    <!-- Sticky Comment -->
    <div class="fixed bottom-0 left-0 w-full bg-white border-t p-2 sm:p-3">
        <div class="max-w-3xl mx-auto flex gap-2">

            <form action="{{ route('comments.store', $post->id) }}"
                method="POST"
                class="flex gap-2 w-full">

                @csrf

                <input type="text"
                    name="content"
                    placeholder="Write a comment..."
                    class="flex-1 border rounded-full px-3 sm:px-4 py-2 text-xs sm:text-sm">

                <button class="bg-blue-500 text-white px-3 sm:px-4 py-2 rounded-full text-xs sm:text-sm">
                    Send
                </button>
            </form>

        </div>
    </div>

    <script>
        function toggleEdit(id) {
            document.getElementById('view-' + id).classList.toggle('hidden');
            document.getElementById('edit-' + id).classList.toggle('hidden');
        }

        function toggleReply(id) {
            document.getElementById('reply-' + id).classList.toggle('hidden');
        }

        function toggleReplyEdit(id) {
            document.getElementById('view-reply-' + id)?.classList.toggle('hidden');
            document.getElementById('edit-reply-' + id)?.classList.toggle('hidden');
        }
    </script>

</x-app-layout>