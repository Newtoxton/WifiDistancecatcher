<!DOCTYPE html>
<html ng-app="wifiApp">
<head>
    <title>Wi-Fi Signal Strength</title>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            opacity: 0;
            transform: translateY(20px);
        }
        .fade-in {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.5s ease-out, transform 0.5s ease-out;
        }
    </style>
</head>
<body ng-controller="wifiController" ng-init="init()">
    <div class="container mt-5">
        <div class="row">
            <!-- Card 1: Calculate Distance and Signal Strength -->
            <div class="col-md-6">
                <div class="card" id="card1">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title">Calculate Distance and Signal Strength</h5>
                    </div>
                    <div class="card-body">
                        <label for="rssi" class="form-label">Enter RSSI (dBm):</label>
                        <input type="number" id="rssi" ng-model="rssi" class="form-control" placeholder="e.g., -70">
                        <button ng-click="calculateDistance()" class="btn btn-primary mt-3">Calculate</button>
                        <div class="mt-3">
                            <p><strong>Estimated Distance:</strong> <span id="distance">{{ distance }}</span></p>
                            <p><strong>Signal Strength:</strong> <span id="signalStrength">{{ signalStrength }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Display Current Networks -->
            <div class="col-md-6">
                <div class="card" id="card2">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title">Current Wi-Fi Networks</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Network Name</th>
                                    <th>Signal Strength</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="network in networks">
                                    <td>{{ network.name }}</td>
                                    <td>{{ network.signalStrength }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var app = angular.module('wifiApp', []);
        app.controller('wifiController', function($scope, $http) {
            $scope.rssi = null;
            $scope.distance = null;
            $scope.signalStrength = null;
            $scope.networks = [];

            // Initialize animations and fetch Wi-Fi networks
            $scope.init = function() {
                // Animate cards on load
                gsap.to("#card1", { opacity: 1, y: 0, duration: 0.8, delay: 0.2 });
                gsap.to("#card2", { opacity: 1, y: 0, duration: 0.8, delay: 0.4 });

                // Fetch Wi-Fi networks
                $http.get('http://localhost:5000/wifi-networks')
                    .then(function(response) {
                        $scope.networks = response.data;
                    }, function(error) {
                        console.error('Error fetching Wi-Fi networks:', error);
                    });
            };

            // Function to calculate distance and signal strength
            $scope.calculateDistance = function() {
                $http({
                    method: 'POST',
                    url: 'calculate_distance.php',
                    data: { rssi: $scope.rssi }
                }).then(function(response) {
                    $scope.distance = response.data.distance;
                    $scope.signalStrength = response.data.signalStrength;

                    // Animate distance and signal strength text
                    gsap.fromTo("#distance", { opacity: 0, y: 10 }, { opacity: 1, y: 0, duration: 0.5 });
                    gsap.fromTo("#signalStrength", { opacity: 0, y: 10 }, { opacity: 1, y: 0, duration: 0.5 });
                }, function(error) {
                    console.error('Error:', error);
                });
            };
        });
    </script>
</body>
</html>