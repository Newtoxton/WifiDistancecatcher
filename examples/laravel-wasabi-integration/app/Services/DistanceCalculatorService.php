<?php

namespace App\Services;

class DistanceCalculatorService
{
    /**
     * Calculate distance from RSSI value
     * Using Log-Distance Path Loss Model
     * 
     * @param float $rssi RSSI value in dBm
     * @param float $txPower Transmitter power in dBm (default: -59)
     * @param float $n Path loss exponent (default: 2)
     * @return array
     */
    public function calculate($rssi, $txPower = -59, $n = 2)
    {
        $distance = $this->calculateDistance($rssi, $txPower, $n);
        
        return [
            'distance' => $this->formatDistance($distance),
            'signalStrength' => $this->getSignalStrength($rssi),
            'rssi' => $rssi
        ];
    }
    
    /**
     * Calculate raw distance in meters
     * 
     * @param float $rssi
     * @param float $txPower
     * @param float $n
     * @return float
     */
    protected function calculateDistance($rssi, $txPower, $n)
    {
        if ($rssi >= $txPower) {
            return 1; // Minimum distance is 1 meter
        }
        
        // Log-Distance Path Loss Model
        // distance = 10 ^ ((txPower - rssi) / (10 * n))
        return pow(10, ($txPower - $rssi) / (10 * $n));
    }
    
    /**
     * Format distance with appropriate unit
     * 
     * @param float $distance Distance in meters
     * @return string
     */
    protected function formatDistance($distance)
    {
        if ($distance >= 1000) {
            // Convert to kilometers
            return round($distance / 1000, 2) . ' km';
        }
        
        // Keep in meters
        return round($distance, 2) . ' meters';
    }
    
    /**
     * Categorize signal strength
     * 
     * @param float $rssi
     * @return string
     */
    protected function getSignalStrength($rssi)
    {
        if ($rssi >= -50) {
            return 'Strong';
        } elseif ($rssi >= -70) {
            return 'Medium';
        }
        
        return 'Weak';
    }
}
