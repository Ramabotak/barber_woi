{{-- Toast notifikasi pop-up. Muncul otomatis saat ada notifikasi baru (dipoll tiap 15 detik). --}}
<div x-data x-init="$store.notif.init()"
     class="fixed top-4 right-4 z-[9999] w-80 max-w-[calc(100vw-2rem)] space-y-2 pointer-events-none">
    <template x-for="toast in $store.notif.toasts" :key="toast.id">
        <div x-show="true"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="bg-white border-l-4 border-brand-gold shadow-xl rounded-lg p-4 pointer-events-auto cursor-pointer"
             @click="$store.notif.dismissToast(toast.id); window.location.href = '{{ route('notifications.index') }}'">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="text-sm font-semibold text-brand-navy" x-text="toast.title"></p>
                    <p class="text-xs text-gray-500 mt-1" x-text="toast.message"></p>
                </div>
                <button type="button" @click.stop="$store.notif.dismissToast(toast.id)"
                        class="text-gray-300 hover:text-gray-500 text-lg leading-none flex-shrink-0">&times;</button>
            </div>
        </div>
    </template>
</div>
