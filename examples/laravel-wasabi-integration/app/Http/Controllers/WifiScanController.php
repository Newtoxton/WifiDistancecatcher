<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\WifiScanService;
use App\Services\DistanceCalculatorService;

class WifiScanController extends Controller
{
    protected $wifiScanService;
    protected $distanceCalculator;
    
    public function __construct(
        WifiScanService $wifiScanService,
        DistanceCalculatorService $distanceCalculator
    ) {
        $this->wifiScanService = $wifiScanService;
        $this->distanceCalculator = $distanceCalculator;
    }
    
    /**
     * Get available Wi-Fi networks
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNetworks()
    {
        $networks = $this->wifiScanService->scanNetworks();
        
        // Store scan results in Wasabi
        $this->storeScanToWasabi($networks);
        
        return response()->json($networks);
    }
    
    /**
     * Calculate distance from RSSI
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateDistance(Request $request)
    {
        $request->validate([
            'rssi' => 'required|numeric|min:-120|max:0'
        ]);
        
        $result = $this->distanceCalculator->calculate($request->rssi);
        
        // Store calculation in Wasabi
        $this->storeCalculationToWasabi($request->rssi, $result);
        
        return response()->json($result);
    }
    
    /**
     * Get scan history from Wasabi
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getScanHistory(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        try {
            $files = Storage::disk('wasabi')->files('wifi-scans');
            
            // Get latest files
            $latestFiles = array_slice(array_reverse($files), 0, $limit);
            
            $scans = [];
            foreach ($latestFiles as $file) {
                $content = Storage::disk('wasabi')->get($file);
                $scans[] = json_decode($content, true);
            }
            
            return response()->json($scans);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve scan history',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store scan results to Wasabi
     * 
     * @param array $networks
     * @return void
     */
    protected function storeScanToWasabi($networks)
    {
        try {
            $data = [
                'timestamp' => now()->toIso8601String(),
                'networks' => $networks,
                'total_count' => count($networks)
            ];
            
            $filename = 'wifi-scans/' . now()->format('Y-m-d-His') . '.json';
            Storage::disk('wasabi')->put($filename, json_encode($data, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            // Log error but don't fail the request
            Log::error('Failed to store scan to Wasabi: ' . $e->getMessage());
        }
    }
    
    /**
     * Store calculation to Wasabi
     * 
     * @param float $rssi
     * @param array $result
     * @return void
     */
    protected function storeCalculationToWasabi($rssi, $result)
    {
        try {
            $data = [
                'timestamp' => now()->toIso8601String(),
                'rssi' => $rssi,
                'distance' => $result['distance'],
                'signal_strength' => $result['signalStrength']
            ];
            
            $filename = 'calculations/' . now()->format('Y-m-d-His') . '.json';
            Storage::disk('wasabi')->put($filename, json_encode($data, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            // Log error but don't fail the request
            Log::error('Failed to store calculation to Wasabi: ' . $e->getMessage());
        }
    }
}
