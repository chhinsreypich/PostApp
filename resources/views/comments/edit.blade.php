<x-app-layout>

    <div class="py-12">
        <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow">

            <h2 class="text-lg font-semibold mb-4">Edit Comment</h2>

            <form action="{{ route('comments.update', $comment->id) }}" method="POST">
                @csrf
                @method('PUT')

                <input type="text" name="content" value="{{ $comment->content }}"
                    class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">

                <button type="submit"
                    class="mt-3 bg-blue-500 text-white px-4 py-2 rounded-lg">
                    Update
                </button>
            </form>

        </div>
    </div>

</x-app-layout>