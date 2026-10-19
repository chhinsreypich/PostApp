<x-app-layout>

    <div class="max-w-2xl mx-auto py-8">

        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            Notifications
        </h2>

        <div class="bg-white shadow rounded-2xl divide-y">

            @forelse ($notifications as $notification)

                <a href="{{ route('notifications.read', $notification->id) }}"
                   class="flex items-start gap-4 p-4 hover:bg-gray-50 transition">

                    <!-- Avatar -->
                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($notification->data['user_name'], 0, 1)) }}
                    </div>

                    <!-- Content -->
                    <div class="flex-1">

                        <!-- Message -->
                        <p class="text-sm text-gray-800">
                            <span class="font-semibold">
                                {{ $notification->data['user_name'] }}
                            </span>
                            {{ $notification->data['message'] }}
                        </p>

                        <!-- Time -->
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>

                    </div>

                    <!-- Unread Dot -->
                    @if (!$notification->read_at)
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                    @endif

                </a>

            @empty
                <div class="p-4 text-gray-400 text-sm text-center">
                    No notifications yet.
                </div>
            @endforelse

        </div>

    </div>

</x-app-layout>