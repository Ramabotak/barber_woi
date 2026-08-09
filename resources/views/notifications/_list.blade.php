    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Notifikasi</h1>
        @if($notifications->where('is_read', false)->count() > 0)
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="text-sm text-blue-600 hover:underline">Tandai semua dibaca</button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow divide-y">
        @forelse($notifications as $notification)
            <div class="p-4 flex items-start justify-between gap-4 {{ $notification->is_read ? '' : 'bg-amber-50' }}">
                <div>
                    <p class="font-semibold text-sm">{{ $notification->title }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                    <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
                @unless($notification->is_read)
                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-xs text-blue-600 hover:underline whitespace-nowrap">
                            Tandai dibaca
                        </button>
                    </form>
                @endunless
            </div>
        @empty
            <div class="p-6 text-center text-gray-500 text-sm">Belum ada notifikasi.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $notifications->links() }}</div>
