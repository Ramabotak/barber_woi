<a href="{{ route('customer.booking.show', $booking) }}"
   class="block bg-white rounded-xl shadow p-4 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
        <div>
            <p class="font-semibold text-sm">{{ $booking->service->service_name }}</p>
            <p class="text-xs text-gray-500">
                {{ $booking->barber->user->name }} &middot; {{ $booking->booking_code }}
            </p>
            <p class="text-xs text-gray-400 mt-1">
                {{ $booking->schedule?->date?->format('d M Y') }}{{ $booking->slot_time ? ', ' . $booking->slot_time->format('H:i') : '' }}
                &middot; No. Antrean {{ $booking->queue_number }}
            </p>
        </div>
        <span class="px-3 py-1 text-xs rounded-full whitespace-nowrap
            @if($booking->status == 'completed') bg-green-100 text-green-700
            @elseif($booking->status == 'cancelled') bg-red-100 text-red-700
            @elseif($booking->status == 'serving') bg-blue-100 text-blue-700
            @elseif($booking->status == 'late') bg-red-50 text-red-600
            @elseif($booking->status == 'paid') bg-emerald-100 text-emerald-700
            @else bg-amber-100 text-amber-800 @endif">
            {{ ucfirst($booking->status) }}
        </span>
    </div>
</a>
