#!/bin/bash

# Development script for Hütte9
# Starts Webpack Encore Dev Server and Symfony Development Server in parallel

echo "🎭 Hütte9 - Development Environment"
echo "===================================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Always install Node dependencies
echo -e "${YELLOW}📦 Installing Node dependencies...${NC}"
yarn install
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Yarn install failed!${NC}"
    exit 1
fi

# Always install PHP dependencies
echo -e "${YELLOW}📦 Installing PHP dependencies...${NC}"
composer install
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Composer install failed!${NC}"
    exit 1
fi

# Clear cache
echo -e "${YELLOW}🧹 Clearing cache...${NC}"
php bin/console cache:clear

# Build assets
echo -e "${YELLOW}🔨 Building assets...${NC}"
yarn encore dev
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Asset build failed!${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}✅ Setup complete!${NC}"
echo ""
echo "Starting development servers..."
echo ""
echo -e "${GREEN}📦 Webpack Dev Server:${NC} http://localhost:8080"
echo -e "${GREEN}🚀 Symfony Server:${NC}     http://localhost:8000"
echo ""
echo -e "${YELLOW}Press Ctrl+C to stop all servers${NC}"
echo ""

# Function to stop all processes
cleanup() {
    echo ""
    echo -e "${YELLOW}🛑 Stopping servers...${NC}"
    kill $WEBPACK_PID $SYMFONY_PID 2>/dev/null
    exit 0
}

# Trap for Ctrl+C
trap cleanup SIGINT SIGTERM

# Start Webpack Dev Server in background
yarn encore dev --watch &
WEBPACK_PID=$!

# Wait a bit so Webpack can start
sleep 2

# Start Symfony Server in background
symfony server:start --no-tls --port=8000 &
SYMFONY_PID=$!

# Wait for both processes
wait
