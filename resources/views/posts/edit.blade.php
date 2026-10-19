<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Post') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-8">
        <form action="{{ route('posts.update', $post->id) }}" method="POST" class="bg-white shadow-md rounded-lg p-6 space-y-4">
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div>
                <label for="title" class="block text-gray-700 font-medium mb-1">Title</label>
                <input type="text" name="title" id="title" placeholder="Enter title" value="{{ $post->title }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Content --}}
            <div>
                <label for="content" class="block text-gray-700 font-medium mb-1">Content</label>
                <textarea name="content" id="content" placeholder="Enter content" rows="6"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none">{{ $post->content }}</textarea>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-between">
                <a href="{{ route('posts.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md transition">Back</a>
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-medium px-5 py-2 rounded-md transition">
                    Update Post
                </button>
            </div>
        </form>
    </div>
</x-app-layout>