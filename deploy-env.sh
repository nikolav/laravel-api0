#!/usr/bin/env bash
set -Eeuo pipefail

trap 'echo "❌ Failed at line $LINENO. Command: $BASH_COMMAND" >&2' ERR

# Must run as root
if [[ "${EUID:-$(id -u)}" -ne 0 ]]; then
  echo "Run as root (sudo ./setup.sh)" >&2
  exit 1
fi

export DEBIAN_FRONTEND=noninteractive

# ---------- Update packages ----------
apt-get update
apt-get upgrade -y

# ---------- Base deps ----------
apt-get install -y --no-install-recommends \
  ca-certificates curl gnupg lsb-release \
  git ufw

# ---------- Git config (root only) ----------
git config --global user.name "nikolav"
git config --global user.email "admin@nikolav.rs"

# ---------- Install Docker from official repo ----------
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg \
  | gpg --dearmor -o /etc/apt/keyrings/docker.gpg
chmod a+r /etc/apt/keyrings/docker.gpg

ARCH="$(dpkg --print-architecture)"
CODENAME="$(. /etc/os-release && echo "$VERSION_CODENAME")"

echo "deb [arch=${ARCH} signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu ${CODENAME} stable" \
  > /etc/apt/sources.list.d/docker.list

apt-get update
apt-get install -y --no-install-recommends \
  docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

systemctl enable --now docker

# Add a real user (not root) to docker group
TARGET_USER="${SUDO_USER:-}"
if [[ -n "$TARGET_USER" ]]; then
  usermod -aG docker "$TARGET_USER"
  echo "ℹ️ Added $TARGET_USER to docker group (log out/in to apply)."
else
  echo "ℹ️ Not adding to docker group (no SUDO_USER detected)."
fi

# ---------- Install Nginx ----------
apt-get install -y --no-install-recommends nginx
systemctl enable --now nginx

# # Create a reverse proxy site → localhost:9000 (Laravel container)
# SITE_NAME="laravel-api"
# SITE_CONF="/etc/nginx/sites-available/${SITE_NAME}.conf"

# cat > "$SITE_CONF" <<'NGINX'
# server {
#   listen 80;
#   server_name _;

#   location / {
#     proxy_pass http://127.0.0.1:9000;
#     proxy_http_version 1.1;

#     proxy_set_header Host $host;
#     proxy_set_header X-Real-IP $remote_addr;
#     proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
#     proxy_set_header X-Forwarded-Proto $scheme;

#     # For websockets/upgrade (harmless if unused)
#     proxy_set_header Upgrade $http_upgrade;
#     proxy_set_header Connection "upgrade";
#   }
# }
# NGINX

# # Enable site
# ln -sf "$SITE_CONF" "/etc/nginx/sites-enabled/${SITE_NAME}.conf"

# # Disable default site if present
# if [[ -f /etc/nginx/sites-enabled/default ]]; then
#   rm -f /etc/nginx/sites-enabled/default
# fi

# nginx -t
# systemctl reload nginx

# ---------- Firewall ----------
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

echo -e "\n=== Setup complete ==="
echo "Git: $(git --version)"
echo "Docker: $(docker --version)"
echo "Docker Compose: $(docker compose version)"
echo "Nginx: $(nginx -v 2>&1)"
echo "UFW: $(ufw status | head -n 1)"
