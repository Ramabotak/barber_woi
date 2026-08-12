import './bootstrap';

import Alpine from 'alpinejs';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);
window.Chart = Chart;

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.store('notif', {
        unreadCount: 0,
        toasts: [],
        started: false,
        lastSeenId: parseInt(localStorage.getItem('notif_last_seen_id') || '0', 10),

        init() {
            if (this.started) return;
            this.started = true;

            this.fetchLatest(true);
            setInterval(() => this.fetchLatest(false), 15000);
        },

        fetchLatest(isFirstLoad) {
            fetch('/notifications/latest', { headers: { Accept: 'application/json' } })
                .then((res) => res.json())
                .then((data) => {
                    this.unreadCount = data.unread_count;

                    if (!isFirstLoad) {
                        data.notifications
                            .filter((n) => n.id > this.lastSeenId)
                            .reverse()
                            .forEach((n) => this.pushToast(n));
                    }

                    if (data.notifications.length > 0) {
                        const maxId = Math.max(...data.notifications.map((n) => n.id));
                        if (maxId > this.lastSeenId) {
                            this.lastSeenId = maxId;
                            localStorage.setItem('notif_last_seen_id', String(maxId));
                        }
                    }
                })
                .catch(() => {});
        },

        pushToast(notif) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, title: notif.title, message: notif.message });
            setTimeout(() => {
                this.toasts = this.toasts.filter((t) => t.id !== id);
            }, 6000);
        },

        dismissToast(id) {
            this.toasts = this.toasts.filter((t) => t.id !== id);
        },
    });
});

Alpine.start();
