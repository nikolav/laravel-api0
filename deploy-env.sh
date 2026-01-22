#!/usr/bin/env bash
set -Eeuo pipefail

trap 'echo "❌ Failed at line $LINENO. Command: $BASH_COMMAND" >&2' ERR

ENV_FILE=".env"

export DEBIAN_FRONTEND=noninteractive

# Must run as root
if [[ "${EUID:-$(id -u)}" -ne 0 ]]; then
  echo "Run as root (sudo ./setup.sh)" >&2
  exit 1
fi

if [[ ! -f "$ENV_FILE" ]]; then
  echo "ERROR: .env file not found in current directory"
  exit 1
fi

# read NGINX_INTERNAL_AUTH_TOKEN from env
NGINX_INTERNAL_AUTH_TOKEN="$(
  grep -E '^NGINX_INTERNAL_AUTH_TOKEN=' "$ENV_FILE" \
  | tail -n1 \
  | cut -d= -f2- \
  | sed 's/^["'\'']//; s/["'\'']$//'
)"

if [[ -z "$NGINX_INTERNAL_AUTH_TOKEN" ]]; then
  echo "ERROR: NGINX_INTERNAL_AUTH_TOKEN is not set or empty in .env"
  exit 1
fi

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

# auto-load custom global config
#   (nginx.conf -> http { include /etc/nginx/conf.d/*.conf; })
tee /etc/nginx/conf.d/00-internal-auth-token.conf > /dev/null <<EOF
map \$host \$internal_auth_token {
    default "${NGINX_INTERNAL_AUTH_TOKEN}";
}
EOF

# chown root:root /etc/nginx/conf.d/00-internal-auth-token.conf
# chmod 600 /etc/nginx/conf.d/00-internal-auth-token.conf

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
