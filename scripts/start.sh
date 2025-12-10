#!/bin/bash

# Start Script for Suvaya Bakery System
# This script starts both backend and frontend servers

echo "=========================================="
echo "Starting Suvaya Bakery System"
echo "=========================================="
echo ""

# Check if processes are already running
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null ; then
    echo "WARNING: Port 8000 is already in use"
    read -p "Kill existing process? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        lsof -ti:8000 | xargs kill -9
        echo "Killed process on port 8000"
    else
        echo "Skipping backend start"
    fi
fi

if lsof -Pi :5173 -sTCP:LISTEN -t >/dev/null ; then
    echo "WARNING: Port 5173 is already in use"
    read -p "Kill existing process? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        lsof -ti:5173 | xargs kill -9
        echo "Killed process on port 5173"
    else
        echo "Skipping frontend start"
    fi
fi

echo ""
echo "Starting servers..."
echo ""

# Create logs directory
mkdir -p logs

# Start backend
echo "Starting PHP backend on http://localhost:8000"
cd backend
php -S localhost:8000 > ../logs/backend.log 2>&1 &
BACKEND_PID=$!
cd ..
echo "Backend PID: $BACKEND_PID"

# Wait a moment for backend to start
sleep 2

# Start frontend
echo "Starting Vue frontend on http://localhost:5173"
cd frontend
npm run dev > ../logs/frontend.log 2>&1 &
FRONTEND_PID=$!
cd ..
echo "Frontend PID: $FRONTEND_PID"

# Save PIDs
echo $BACKEND_PID > logs/backend.pid
echo $FRONTEND_PID > logs/frontend.pid

echo ""
echo "=========================================="
echo "Servers Started Successfully!"
echo "=========================================="
echo ""
echo "Frontend: http://localhost:5173"
echo "Backend:  http://localhost:8000"
echo "Admin:    http://localhost:5173/admin"
echo ""
echo "Admin credentials:"
echo "  Email:    admin@suvaya.com"
echo "  Password: admin123"
echo ""
echo "To stop servers: ./scripts/stop.sh"
echo "View logs: tail -f logs/backend.log logs/frontend.log"
echo ""
