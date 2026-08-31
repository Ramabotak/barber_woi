import './bootstrap';
import Echo from './echo'; // Rename echo.js.example to echo.js after npm install laravel-echo pusher-js

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Chart = Chart;
window.Alpine = Alpine;

document.addEventListener('DOMContentLoaded', () => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    document.querySelectorAll('main > *').forEach((element, index) => {
        element.classList.add('page-enter');
        element.style.animationDelay = `${Math.min(index * 75, 450)}ms`;
    });

    document.querySelectorAll('main article, main .group, main [class*="rounded-xl"], main [class*="rounded-2xl"]').forEach((element) => {
        element.classList.add('motion-card');
    });
});

document.addEventListener('alpine:init', () => {
    // Get current user ID from meta tag (set in layout)
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    Alpine.store('notif', {
        unreadCount: 0,
        toasts: [],
        started: false,
        lastSeenId: parseInt(localStorage.getItem('notif_last_seen_id') || '0', 10),

        init() {
            if (this.started) return;
            this.started = true;

            this.fetchLatest(true);
            
             if (userId) {
                 this.setupWebSocket(userId);
             }
            setInterval(() => this.fetchLatest(false), 30000); // Poll every 30s (reduced load)
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

        setupWebSocket(userId) {
            Echo.channel(`user.${userId}`)
                .listen('.notification.sent', (e) => {
                    console.log('Real-time notification received:', e);
                    
                    // Update unread count
                    this.unreadCount++;
                    
                    // Show toast notification
                    this.pushToast({
                        id: e.id,
                        title: e.title,
                        message: e.message
                    });
                    
                    // Update last seen ID
                    if (e.id > this.lastSeenId) {
                        this.lastSeenId = e.id;
                        localStorage.setItem('notif_last_seen_id', String(e.id));
                    }
                });
        },

        dismissToast(id) {
            this.toasts = this.toasts.filter((t) => t.id !== id);
        },
    });
});

Alpine.start();
