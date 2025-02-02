# WiFiDistanceCatcher

WiFiDistanceCatcher is a web application that calculates the approximate distance of a Wi-Fi network based on its RSSI (Received Signal Strength Indicator) value. It also displays a list of available Wi-Fi networks along with their signal strength. The project consists of a Flask backend and an AngularJS frontend.

## Features
- **Distance Calculation**: Estimates the distance of a Wi-Fi network using the Log-Distance Path Loss Model.
- **Signal Strength Categorization**: Classifies signal strength as Weak, Medium, or Strong.
- **Wi-Fi Network List**: Displays a list of available Wi-Fi networks with their signal strength.
- **Responsive Design**: Built with Bootstrap for a clean and responsive user interface.
- **Micro-Animations**: Uses GSAP (GreenSock Animation Platform) for smooth animations.

## Technologies Used

### Frontend:
- AngularJS
- Bootstrap
- GSAP (for animations)

### Backend:
- Flask (Python)

### Other Tools:
- `nmcli` (Linux/MacOS) or `netsh` (Windows) for Wi-Fi network scanning.

## Prerequisites
Before running the project, ensure you have the following installed:

- **Python 3.x**: [Download Python](https://www.python.org/downloads/)
- **pip**: Python package manager (usually comes with Python).
- **Node.js** (optional): For serving the frontend using a local server.

### Wi-Fi Scanning Tools:
- **Linux/MacOS**: `nmcli` (part of NetworkManager).
- **Windows**: `netsh` (built-in).
- **macOS**: `airport` (requires enabling).

## Setup Instructions

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/WiFiDistanceCatcher.git
cd WiFiDistanceCatcher
```

### 2. Set Up the Backend
Navigate to the backend folder:
```bash
cd backend
```
Install the required Python packages:
```bash
pip install -r requirements.txt
```
Start the Flask backend:
```bash
python app.py
```
The backend will run at [http://127.0.0.1:5000](http://127.0.0.1:5000).

### 3. Set Up the Frontend
Navigate to the frontend folder:
```bash
cd ../frontend
```
Serve the frontend using a local server. For example, use Python's built-in HTTP server:
```bash
python -m http.server 8000
```
The frontend will be accessible at [http://127.0.0.1:8000](http://127.0.0.1:8000).

## Usage
Open the application in your browser:
[http://localhost:8000](http://localhost:8000)

### Calculate Distance:
1. Enter the RSSI value (e.g., -70) in the input field.
2. Click **Calculate** to see the estimated distance and signal strength.

### View Available Wi-Fi Networks:
- The second card displays a list of available Wi-Fi networks along with their signal strength.

## Project Structure
```
WiFiDistanceCatcher/
├── backend/             # Backend files (Flask)
│   ├── app.py          # Flask API for Wi-Fi network scanning
│   ├── requirements.txt # Python dependencies
├── frontend/            # Frontend files (AngularJS)
│   ├── index.html      # Main HTML file with AngularJS and Bootstrap
│   ├── calculate_distance.php # PHP script for distance calculation
├── README.md            # Project documentation
└── start.sh             # Script to start the backend and frontend
```

## API Endpoints

### Backend API ([http://127.0.0.1:5000](http://127.0.0.1:5000))

#### GET `/wifi-networks`
Fetches a list of available Wi-Fi networks.

#### Example Response:
```json
[
  {"name": "HomeWiFi", "signalStrength": 85},
  {"name": "GuestWiFi", "signalStrength": 60},
  {"name": "NeighborWiFi", "signalStrength": 45}
]
```

## Troubleshooting

### 1. Backend Not Running
Ensure Flask is installed:
```bash
pip install flask
```
Check for errors in the terminal where the backend is running.

### 2. Frontend Not Loading
Ensure the frontend server is running (e.g., `python -m http.server 8000`).
Open the browser console (F12) and check for errors.

### 3. Wi-Fi Networks Not Displayed
Ensure the correct Wi-Fi scanning tool is installed for your OS:
- **Linux/MacOS**: `nmcli`
- **Windows**: `netsh`
- **macOS**: `airport`

If the tool is unavailable, use mock data in `backend/app.py`.

## Contributing
Contributions are welcome! Follow these steps:

1. Fork the repository.
2. Create a new branch:
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. Commit your changes:
   ```bash
   git commit -m "Add your feature"
   ```
4. Push to the branch:
   ```bash
   git push origin feature/your-feature-name
   ```
5. Open a pull request.

## License
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Acknowledgments
- **Bootstrap**: For the responsive design.
- **GSAP**: For smooth animations.
- **Flask**: For the lightweight backend.

Enjoy using WiFiDistanceCatcher! If you have any questions or issues, feel free to open an issue on GitHub.

