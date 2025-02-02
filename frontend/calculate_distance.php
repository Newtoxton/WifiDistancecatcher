<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$rssi = $data['rssi'];

function calculateDistance($rssi, $txPower = -59, $n = 2) {
    if ($rssi >= $txPower) {
        return 1; // Minimum distance is 1 meter
    }
    $distance = pow(10, ($txPower - $rssi) / (10 * $n));
    return $distance;
}

function formatDistance($distance) {
    if ($distance >= 1000) {
        // Convert to kilometers
        return round($distance / 1000, 2) . ' km';
    } else {
        // Keep in meters
        return round($distance, 2) . ' meters';
    }
}

function getSignalStrength($rssi) {
    if ($rssi >= -50) {
        return 'Strong';
    } elseif ($rssi >= -70) {
        return 'Medium';
    } else {
        return 'Weak';
    }
}

$distance = calculateDistance($rssi);
$formattedDistance = formatDistance($distance); // Ensure unit is included
$signalStrength = getSignalStrength($rssi);

echo json_encode([
    'distance' => $formattedDistance, // Includes unit (e.g., "1.5 km" or "500 meters")
    'signalStrength' => $signalStrength
]);
?>