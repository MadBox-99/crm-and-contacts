# Chat Rendszer Használati Útmutató

## 🚀 Gyors Indítás

### 1. Frontend Build
Először build-eljük a frontend asset-eket (Echo support szükséges):

```bash
npm run build
# vagy development módban:
npm run dev
```

### 2. Reverb Server Indítása
Új terminál ablakban indítsuk el a Laravel Reverb WebSocket szervert:

```bash
php artisan reverb:start
```

Vagy használhatod a `composer run dev` parancsot, ami automatikusan elindítja az összes szolgáltatást:
```bash
composer run dev
```

Ez egyszerre indítja:
- PHP artisan serve (HTTP szerver)
- Queue listener
- Pail (log viewer)
- Vite dev server (hot reload)
- Laravel Reverb (WebSocket)

### 3. Laravel Development Server (ha külön kell)
Ha nem használod a `composer run dev`-et:

```bash
php artisan serve
```

### 4. Demo Oldal Megtekintése
Nyisd meg a böngészőben:

```
http://localhost:8000/chat-demo
```

---

## 📱 Vendég Oldali Használat

### Chat Widget Beágyazása

Bármilyen Blade view-ban használható:

```blade
{{-- Vendég ID-val --}}
<livewire:chat.chat-widget :customerId="$customer->id" />

{{-- Vagy session-ből --}}
<livewire:chat.chat-widget :customerId="session('customer_id')" />
```

### Funkciók:
- ✅ Floating chat button (jobb alsó sarok)
- ✅ Unread badge (piros értesítés)
- ✅ Minimize / Maximize
- ✅ Real-time üzenetek
- ✅ Gépelés jelzés
- ✅ Olvasási nyugták (kék pipa)
- ✅ Auto-scroll legújabb üzenethez
- ✅ Enter küldés, Shift+Enter új sor

---

## 👨‍💼 Admin Oldali Használat

### 1. Filament Admin Panel
```
http://localhost:8000/admin
```

### 2. Chat Sessions Menü
Navigálj: **Chat Sessions** → Lásd az aktív beszélgetéseket

### 3. Session Megnyitása
Kattints egy session-re → Relation Manager: **Messages**

### 4. Üzenet Küldése
- Create new message
- Sender type: **User**
- Írj üzenetet
- Save

### Funkciók:
- ✅ Aktív session-ök listázása
- ✅ Unread count látható
- ✅ Session assign adminhoz
- ✅ Session transfer másik adminhoz
- ✅ Session close/rate
- ✅ Full message history
- ✅ Customer információk

---

## 🔧 Környezeti Változók

Ellenőrizd a `.env` fájlt:

```env
# Broadcasting
BROADCAST_CONNECTION=reverb

# Reverb Configuration
REVERB_APP_ID=173928
REVERB_APP_KEY=9mla768euaie9aqsuz0n
REVERB_APP_SECRET=tabpxqmr9xs5ymz545ro
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite (frontend)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

---

## 🧪 Tesztelés

### 1. Két Böngészőben Teszt

**Böngésző 1 - Vendég:**
1. Nyisd meg: `http://localhost:8000/chat-demo`
2. Kattints a chat gombra
3. Küldj egy üzenetet

**Böngésző 2 - Admin:**
1. Nyisd meg: `http://localhost:8000/admin`
2. Jelentkezz be
3. Menj: Chat Sessions
4. Nyisd meg az aktív session-t
5. Válaszolj az üzenetre

**Elvárt eredmény:**
- ✅ Üzenetek azonnal megjelennek mindkét oldalon
- ✅ Gépelés jelzés látható
- ✅ Olvasási nyugták frissülnek

### 2. Queue Tesztelés
```bash
# Queue listener indítása
php artisan queue:work

# Vagy sync driver használata development-ben
QUEUE_CONNECTION=sync
```

---

## 🐛 Hibaelhárítás

### Probléma: Üzenetek nem jelennek meg real-time

**Megoldás 1 - Reverb fut?**
```bash
# Ellenőrizd:
ps aux | grep reverb

# Ha nem fut, indítsd el:
php artisan reverb:start
```

**Megoldás 2 - Frontend asset build**
```bash
npm run build
# vagy
npm run dev
```

**Megoldás 3 - Browser cache törlés**
- Hard reload: Ctrl+Shift+R (Win) vagy Cmd+Shift+R (Mac)

**Megoldás 4 - Ellenőrizd a browser console-t**
- F12 → Console tab
- Keress Echo connection hibákat

### Probléma: "419 Page Expired"

**Megoldás:**
```bash
php artisan config:clear
php artisan cache:clear
```

### Probléma: Channels nem működnek

**Ellenőrizd:**
1. `routes/channels.php` létezik
2. `bootstrap/app.php` tartalmazza: `channels: __DIR__.'/../routes/channels.php'`
3. Admin user be van jelentkezve

---

## 📊 Monitorozás

### Reverb Logs
```bash
php artisan reverb:start --debug
```

### Laravel Logs
```bash
php artisan pail

# Vagy tail paranccsal:
tail -f storage/logs/laravel.log
```

### Queue Monitoring
```bash
php artisan queue:work --verbose
```

---

## 🎯 Következő Lépések

1. **Filament Widget létrehozása** - Dashboard statisztikák
2. **Online/Offline Middleware** - Auto status tracking
3. **Canned Responses** - Gyors válaszok
4. **File Upload** - Képek, dokumentumok küldése
5. **Bot Integration** - Auto-reply funkció
6. **Email Notifications** - Offline értesítések
7. **Mobile App** - React Native / Flutter

---

## 📚 Hasznos Linkek

- [Laravel Reverb Docs](https://laravel.com/docs/11.x/reverb)
- [Laravel Echo Docs](https://laravel.com/docs/11.x/broadcasting#client-side-installation)
- [Livewire Docs](https://livewire.laravel.com/docs)
- [Filament Docs](https://filamentphp.com/docs)
- [PlantUML Diagrams](diagrams/chat-flow-diagram.puml)

---

## 💡 Pro Tippek

1. **Development módban használj `npm run dev`-et** - Hot reload
2. **Production-ban mindig `npm run build`** - Optimalizált bundle
3. **Reverb Scaling** - Redis használata több server esetén
4. **Rate Limiting** - Védd az API-t spam ellen
5. **Session Timeout** - Inaktív session-ök automatikus lezárása

---

## 🆘 Segítségkérés

Ha problémába ütközöl:
1. Ellenőrizd a Laravel log-okat: `storage/logs/laravel.log`
2. Ellenőrizd a browser console-t (F12)
3. Ellenőrizd a Reverb server output-ját
4. Nézd meg a PlantUML diagramokat a flow megértéséhez

Jó chatezést! 🚀💬
