#!/bin/bash

# Start the Flask backend
cd backend
python app.py &

# Start the PHP frontend server
cd ../frontend
php -S localhost:8000
