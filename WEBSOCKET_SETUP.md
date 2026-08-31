# WEBSOCKET SETUP - LARAVEL REVERB

## Apa yang Sudah Disetup?

✅ Event Broadcasting: `app/Events/NotificationSent.php`
✅ WebSocket Listener: `resources/js/echo.js`
✅ Frontend Integration: Updated `resources/js/app.js`
✅ Broadcasting Channels: `routes/channels.php`
✅ Environment Config: Added to `.env`
✅ Layout Update: Added user-id meta tag
✅ Service Update: NotificationService broadcast on send

---

## CARA SETUP (Langkah-Langkah)

### 1. Install NPM Dependencies
```bash
npm install laravel-echo pusher-js
npm run build
```

### 2. Publish Laravel Reverb (jika perlu)
```bash
php artisan vendor:publish --provider="Laravel\Reverb\ReverbServiceProvider"
```

### 3. Update .env dengan Key yang Valid
```env
BROADCAST_CONNECTION=reverb
REVERB_APP_KEY=barber-woi-key
REVERB_APP_SECRET=your-secret-key
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY=barber-woi-key
VITE_REVERB_HOST=127.0.0.1
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http
```

### 4. Generate Reverb Keys
```bash
php artisan reverb:install
```

### 5. Jalankan Laravel Reverb Server (Terminal 1)
```bash
php artisan reverb:start
```

### 6. Jalankan Development Server (Terminal 2)
```bash
php artisan serve
```

### 7. Build Frontend Assets (Terminal 3)
```bash
npm run dev
```

---

## TESTING WEBSOCKET

1. Open 2 browser tabs
2. Login as barber di tab 1
3. Login as customer di tab 2
4. Barber accept booking → Customer langsung dapat notifi (instant!)

---

## PRODUCTION SETUP

### Option 1: Reverb Cloud (Recommended)
- Deploy Laravel Reverb ke server
- Update .env dengan production URL

### Option 2: Pusher (Third-party)
```bash
composer require pusher/pusher-php-server
npm install --save pusher-js

# Di .env:
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=xxx
PUSHER_APP_KEY=xxx
PUSHER_APP_SECRET=xxx
```

### Option 3: Redis + Socket.IO (Advanced)
- Setup Redis server
- Deploy Socket.IO dengan Laravel Echo
- Lebih scalable untuk traffic tinggi

---

## TROUBLESHOOTING

### Issue: "Connection refused on port 8080"
→ Reverb server belum jalan. Jalankan: `php artisan reverb:start`

### Issue: "CORS error"
→ Update REVERB_HOST di .env ke domain yang benar

### Issue: "Event tidak ter-broadcast"
→ Check: BROADCAST_CONNECTION=reverb di .env
→ Check: Channel authorization di routes/channels.php

---

## PERFORMANCE BENEFIT

Sebelum (Polling):
- 15-30 detik delay untuk notifikasi
- Request ke server setiap interval (boros)
- 100 user online = 200+ request/menit

Sesudah (WebSocket):
- 0 delay - instant notification
- Hanya request saat ada event (efisien)
- 100 user online = 0 request saat idle, hanya saat ada event

Hemat bandwidth: ~80% untuk sistem ini!

