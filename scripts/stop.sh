#!/bin/bash

# Stop Script for Suvaya Bakery System
# This script stops both backend and frontend servers

echo "=========================================="
echo "Stopping Suvaya Bakery System"
echo "=========================================="
echo ""

# Stop using PIDs if available
if [ -f "logs/backend.pid" ]; then
    BACKEND_PID=$(cat logs/backend.pid)
    if ps -p $BACKEND_PID > /dev/null; then
        kill $BACKEND_PID
        echo "Stopped backend (PID: $BACKEND_PID)"
    fi
    rm logs/backend.pid
fi

if [ -f "logs/frontend.pid" ]; then
    FRONTEND_PID=$(cat logs/frontend.pid)
    if ps -p $FRONTEND_PID > /dev/null; then
        kill $FRONTEND_PID
        echo "Stopped frontend (PID: $FRONTEND_PID)"
    fi
    rm logs/frontend.pid
fi

# Fallback: kill by port
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null ; then
    lsof -ti:8000 | xargs kill -9
    echo "Stopped process on port 8000"
fi

if lsof -Pi :5173 -sTCP:LISTEN -t >/dev/null ; then
    lsof -ti:5173 | xargs kill -9
    echo "Stopped process on port 5173"
fi

echo ""
echo "All servers stopped"
echo ""
