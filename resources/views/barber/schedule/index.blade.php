@extends('layouts.barber')

@section('title', 'Jadwal Saya - Barber Woi')

@section('content')
    <div class="mx-auto max-w-5xl space-y-8">
        @if(session('success')) <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div> @endif
        @if($errors->any()) <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div> @endif

        <section class="rounded-2xl border border-[#e4e4e7]/70 bg-white p-4 shadow-sm sm:p-6">
            <h1 class="mb-5 font-['Plus_Jakarta_Sans'] text-xl font-semibold text-[#18181b] sm:mb-6">Tambah Jadwal</h1>
            <form action="{{ route('barber.schedule.store') }}" method="POST" class="grid gap-4 sm:grid-cols-2 lg:flex lg:flex-wrap lg:items-end lg:gap-6">@csrf
                <label class="w-full lg:min-w-[220px] lg:flex-1"><span class="mb-2 block text-sm text-[#71717a]">Tanggal</span><input type="date" name="date" required min="{{ now()->toDateString() }}" class="w-full rounded-lg border-[#e4e4e7] px-4 py-2.5 text-sm focus:border-[#f6d268] focus:ring-[#f6d268]"></label>
                <label class="w-full lg:w-40"><span class="mb-2 block text-sm text-[#71717a]">Jam Mulai</span><input type="time" name="start_time" required class="w-full rounded-lg border-[#e4e4e7] px-4 py-2.5 text-center text-sm focus:border-[#f6d268] focus:ring-[#f6d268]"></label>
                <label class="w-full lg:w-40"><span class="mb-2 block text-sm text-[#71717a]">Jam Selesai</span><input type="time" name="end_time" required class="w-full rounded-lg border-[#e4e4e7] px-4 py-2.5 text-center text-sm focus:border-[#f6d268] focus:ring-[#f6d268]"></label>
                <button type="submit" class="h-[42px] w-full rounded-lg bg-[#c19d4c] px-6 text-sm font-medium text-white transition-colors hover:bg-[#a88231] sm:col-span-2 lg:w-auto">+ Tambah Jadwal</button>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-[#e4e4e7]/70 bg-white shadow-sm">
            <div class="divide-y divide-[#e4e4e7] md:hidden">
                @forelse($schedules as $schedule)
                    <article class="space-y-4 p-4">
                        <div class="flex items-start justify-between gap-3"><div><h2 class="font-medium text-[#18181b]">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('l, d M Y') }}</h2><p class="mt-1 text-sm text-[#71717a]">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} WIB</p></div><span @class(['shrink-0 inline-flex rounded-full px-2.5 py-1 text-xs font-medium', 'bg-[#dcfce7] text-[#166534]' => $schedule->status === 'tersedia', 'bg-[#fce7f3] text-[#991b1b]' => $schedule->status === 'penuh', 'bg-[#f1f5f9] text-[#475569]' => $schedule->status === 'libur'])>{{ ucfirst($schedule->status) }}</span></div>
                        <div class="flex items-center justify-between border-y border-[#e4e4e7] py-3 text-sm"><span class="text-[#71717a]">Booking</span><span class="font-medium text-[#18181b]">{{ $schedule->bookings_count }} booking</span></div>
                        <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm">@if($schedule->status !== 'libur')<form action="{{ route('barber.schedule.close', $schedule) }}" method="POST">@csrf @method('PATCH')<button onclick="return confirm('Tutup jadwal ini?')" class="font-medium text-[#c19d4c] hover:text-[#a88231]">Tutup Jadwal</button></form>@endif @if($schedule->bookings_count === 0)<form action="{{ route('barber.schedule.destroy', $schedule) }}" method="POST">@csrf @method('DELETE')<button onclick="return confirm('Hapus jadwal ini?')" class="font-medium text-[#991b1b] hover:text-red-900">Hapus</button></form>@endif</div>
                    </article>
                @empty <p class="px-4 py-14 text-center text-sm text-[#71717a]">Belum ada jadwal. Tambahkan jadwal kerja Anda di atas.</p> @endforelse
            </div>
            <div class="hidden overflow-x-auto md:block"><table class="w-full min-w-[720px] border-collapse text-left text-sm"><thead><tr class="border-b border-[#e4e4e7] bg-gray-50 text-[#71717a]"><th class="w-1/4 px-8 py-4 font-semibold">Tanggal</th><th class="w-1/4 px-8 py-4 font-semibold">Jam Kerja</th><th class="w-1/6 px-8 py-4 font-semibold">Status</th><th class="w-1/6 px-8 py-4 font-semibold">Booking</th><th class="w-1/6 px-8 py-4 font-semibold">Aksi</th></tr></thead><tbody class="divide-y divide-[#e4e4e7]">
            @forelse($schedules as $schedule)
                <tr class="transition-colors hover:bg-gray-50"><td class="px-8 py-4"><div class="font-medium text-[#18181b]">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('l, d') }}</div><div class="text-[#71717a]">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('M Y') }}</div></td><td class="px-8 py-4"><div>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} –</div><div>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</div></td><td class="px-8 py-4"><span @class(['inline-flex rounded-full px-2.5 py-1 text-xs font-medium', 'bg-[#dcfce7] text-[#166534]' => $schedule->status === 'tersedia', 'bg-[#fce7f3] text-[#991b1b]' => $schedule->status === 'penuh', 'bg-[#f1f5f9] text-[#475569]' => $schedule->status === 'libur'])>{{ ucfirst($schedule->status) }}</span></td><td class="px-8 py-4 text-[#71717a]">{{ $schedule->bookings_count }} booking</td><td class="px-8 py-4"><div class="flex items-center gap-3 text-sm">@if($schedule->status !== 'libur')<form action="{{ route('barber.schedule.close', $schedule) }}" method="POST">@csrf @method('PATCH')<button onclick="return confirm('Tutup jadwal ini?')" class="font-medium text-[#c19d4c] hover:text-[#a88231]">Tutup</button></form>@endif @if($schedule->bookings_count === 0)<form action="{{ route('barber.schedule.destroy', $schedule) }}" method="POST">@csrf @method('DELETE')<button onclick="return confirm('Hapus jadwal ini?')" class="font-medium text-[#991b1b] hover:text-red-900">Hapus</button></form>@endif</div></td></tr>
            @empty <tr><td colspan="5" class="py-14 text-center text-sm text-[#71717a]">Belum ada jadwal. Tambahkan jadwal kerja Anda di atas.</td></tr> @endforelse
            </tbody></table></div>
        </section>
    </div>
@endsection
