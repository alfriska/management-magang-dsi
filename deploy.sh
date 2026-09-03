#!/bin/bash
# ===========================================
# InternHub Deployment Script
# ===========================================
# Usage: ./deploy.sh
# Run this script on your server after git pull

set -e

echo "🚀 Starting InternHub Deployment..."
echo "=================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Step 1: Enable maintenance mode
echo -e "${YELLOW}[1/8] Enabling maintenance mode...${NC}"
php artisan down --render="errors::503" || true

# Step 2: Pull latest changes
echo -e "${YELLOW}[2/8] Pulling latest changes from GitHub...${NC}"
git pull origin main

# Step 3: Install/update Composer dependencies
echo -e "${YELLOW}[3/8] Installing Composer dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction

# Step 4: Run database migrations
echo -e "${YELLOW}[4/8] Running database migrations...${NC}"
php artisan migrate --force

# Step 5: Clear and rebuild caches
echo -e "${YELLOW}[5/8] Optimizing application...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Step 6: Build frontend assets (if node is available)
if command -v npm &> /dev/null; then
    echo -e "${YELLOW}[6/8] Building frontend assets...${NC}"
    npm ci --production
    npm run build
else
    echo -e "${YELLOW}[6/8] Skipping npm build (npm not available)...${NC}"
fi

# Step 7: Create storage link if not exists
echo -e "${YELLOW}[7/8] Ensuring storage link...${NC}"
php artisan storage:link || true

# Step 8: Disable maintenance mode
echo -e "${YELLOW}[8/8] Disabling maintenance mode...${NC}"
php artisan up

echo ""
echo -e "${GREEN}=================================="
echo "✅ Deployment completed successfully!"
echo "==================================${NC}"
echo ""
echo "📊 Quick health check:"
php artisan about --only=environment

