from flask import Flask, jsonify
from flask_cors import CORS
import subprocess  # Add this line

app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

@app.route('/wifi-networks', methods=['GET'])
def wifi_networks():
    try:
        # Run system command to scan Wi-Fi networks (Linux/MacOS)
        result = subprocess.run(["nmcli", "-t", "-f", "SSID,SIGNAL", "dev", "wifi"], capture_output=True, text=True)
        networks = []

        # Parse the output
        for line in result.stdout.splitlines():
            ssid, signal = line.split(":")
            networks.append({
                "name": ssid,
                "signalStrength": int(signal)  # Signal strength in percentage
            })

        return jsonify(networks)
    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)