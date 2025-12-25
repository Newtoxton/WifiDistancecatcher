<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;

class WifiScanService
{
    /**
     * Scan available Wi-Fi networks
     * 
     * @return array
     */
    public function scanNetworks()
    {
        try {
            // Attempt to scan using nmcli (Linux/MacOS)
            $result = Process::run('nmcli -t -f SSID,SIGNAL dev wifi');
            
            if (!$result->successful()) {
                return $this->getMockData();
            }
            
            return $this->parseNmcliOutput($result->output());
            
        } catch (\Exception $e) {
            // Fall back to mock data if scanning fails
            return $this->getMockData();
        }
    }
    
    /**
     * Parse nmcli command output
     * 
     * @param string $output
     * @return array
     */
    protected function parseNmcliOutput($output)
    {
        $networks = [];
        $lines = explode("\n", trim($output));
        
        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }
            
            $parts = explode(':', $line);
            
            if (count($parts) >= 2) {
                $ssid = $parts[0];
                $signal = (int) $parts[1];
                
                // Skip empty SSIDs
                if (!empty($ssid)) {
                    $networks[] = [
                        'name' => $ssid,
                        'signalStrength' => $signal
                    ];
                }
            }
        }
        
        return $networks;
    }
    
    /**
     * Get mock data for development/testing
     * 
     * @return array
     */
    protected function getMockData()
    {
        return [
            [
                'name' => 'HomeNetwork',
                'signalStrength' => 85
            ],
            [
                'name' => 'GuestWiFi',
                'signalStrength' => 60
            ],
            [
                'name' => 'OfficeNetwork',
                'signalStrength' => 45
            ],
            [
                'name' => 'PublicWiFi',
                'signalStrength' => 30
            ],
            [
                'name' => 'NeighborNetwork',
                'signalStrength' => 25
            ]
        ];
    }
}
