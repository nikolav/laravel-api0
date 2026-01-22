## 🚀 API-Focused Laravel Application

A lightweight, production-ready Laravel setup optimized for building **RESTful APIs** and backend services.  
This project is designed to run behind **Nginx**, use **Docker**, and expose **API-only endpoints** with HTTPS via **Let’s Encrypt**.

---

## ✨ Features

- Laravel **API-only** architecture
- Nginx reverse proxy
- Dockerized runtime (PHP-FPM + Nginx)
- SQLite (default) + Redis
- HTTPS with Let’s Encrypt (Certbot)
- Hardened Nginx defaults
- Ready for production deployment

---

## 📦 Quick Setup (Local / Development)

```bash
# 1. Clone repository
git clone [repository-url]
cd [project-name]

# 2. Install PHP dependencies
composer install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. SQLite database (default)
touch database/database.sqlite
php artisan migrate
```

---

## 🔐 Optional: API Authentication (Sanctum)

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

---

## 🐳 Docker (Recommended for Production)

### Build & run

```bash
docker compose up -d --build
```

### Verify containers

```bash
docker compose ps
```

### Test API

```bash
curl http://127.0.0.1:9000/api/health
```

---

## 🌐 Domain & DNS Setup

### 1️⃣ DNS records (at your domain provider)

Point your **apex domain** to the server IP:

| Type | Host | Value     |
|-----:|------|-----------|
| A    | @    | SERVER_IP |

> No CNAME needed for apex.
> Nameservers should already point to DNS provider.
> subdomains:* - CNAME * domain.com

---

## 🔒 HTTPS with Let’s Encrypt (Certbot)

### Requirements

- Domain resolves to your server
- Port **80** and **443** open
- Nginx installed on the host

---

### 1️⃣ Install Certbot (Ubuntu)

```bash
sudo apt update
sudo apt install -y certbot python3-certbot-nginx
```

---

### 2️⃣ Ensure Nginx is serving `domain.com` on HTTP

Before running certbot, make sure your HTTP vhost has the correct `server_name`:

```nginx
server {
  listen 80;
  server_name domain.com;
  
  ...
}
```

Reload:

```bash
sudo nginx -t && sudo systemctl reload nginx
```

---

### 3️⃣ Request certificate (apex only)

```bash
sudo certbot --nginx -d domain.com
```

When prompted, choose **Redirect HTTP → HTTPS: Yes**.

---

### 4️⃣ Final HTTPS Nginx config (API-only)

```nginx
server {
  listen 443 ssl http2;
  server_name domain.com;

  ssl_certificate     /etc/letsencrypt/live/domain.com/fullchain.pem;
  ssl_certificate_key /etc/letsencrypt/live/domain.com/privkey.pem;

  ...
}
```

Reload Nginx:

```bash
sudo nginx -t && sudo systemctl reload nginx
```

---

### 5️⃣ Verify HTTPS

```bash
curl -I https://domain.com/api/health
```

Expected response includes:

```http
HTTP/1.1 200 OK
Strict-Transport-Security: max-age=15552000
```

---

## 🔁 Auto-Renew Certificates

Certbot installs a systemd timer automatically. Verify:

```bash
systemctl list-timers | grep certbot
```

Manual test:

```bash
sudo certbot renew --dry-run
```

---

## 🧪 Production Health Checks

```bash
# API
curl https://domain.com/api/health

# Laravel
docker exec -it laravel-api php artisan about

# Redis (example)
docker exec -it laravel-api php artisan tinker --execute="use Illuminate\\Support\\Facades\\Redis; Redis::set('ping','pong'); echo Redis::get('ping');"
```

---

## 🔐 Security Notes

- HSTS enabled **only on HTTPS**
- API-only exposure (`/api/*`)
- No PHP exposed directly
- HTTPoxy protection enabled
- No directory indexing

