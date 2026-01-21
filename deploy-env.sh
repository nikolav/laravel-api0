#!/bin/bash

set -e  # Exit on any error

# Update packages
apt-get update && apt-get upgrade -y

# Install git with minimal config
apt-get install -y git
git config --global user.name "nikolav"
git config --global user.email "admin@nikolav.rs"

# Install Docker from official repository
apt-get install -y ca-certificates curl gnupg
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg
chmod a+r /etc/apt/keyrings/docker.gpg
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null

apt-get update
apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Add current user to docker group (avoids needing sudo)
usermod -aG docker $USER

# Configure firewall (minimal ports)
ufw allow OpenSSH
ufw allow http
ufw allow https
ufw --force enable  # Enable with default deny policy

echo -e "\n=== Setup complete ==="
echo "Git: $(git --version)"
echo "Docker: $(docker --version)"
echo "Docker Compose: $(docker compose version)"
