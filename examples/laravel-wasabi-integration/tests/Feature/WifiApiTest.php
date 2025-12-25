<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WifiApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Use fake storage for testing
        Storage::fake('wasabi');
    }
    
    /** @test */
    public function it_can_get_wifi_networks()
    {
        $response = $this->getJson('/api/wifi/networks');
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['name', 'signalStrength']
                 ]);
    }
    
    /** @test */
    public function it_stores_wifi_scan_to_wasabi()
    {
        $response = $this->getJson('/api/wifi/networks');
        
        $response->assertStatus(200);
        
        // Verify file was created in Wasabi
        $files = Storage::disk('wasabi')->files('wifi-scans');
        $this->assertNotEmpty($files);
    }
    
    /** @test */
    public function it_can_calculate_distance()
    {
        $response = $this->postJson('/api/wifi/calculate-distance', [
            'rssi' => -70
        ]);
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'distance',
                     'signalStrength',
                     'rssi'
                 ]);
        
        $data = $response->json();
        $this->assertEquals(-70, $data['rssi']);
        $this->assertEquals('Medium', $data['signalStrength']);
    }
    
    /** @test */
    public function it_validates_rssi_input()
    {
        // Test missing RSSI
        $response = $this->postJson('/api/wifi/calculate-distance', []);
        $response->assertStatus(422);
        
        // Test invalid RSSI (too high)
        $response = $this->postJson('/api/wifi/calculate-distance', [
            'rssi' => 10
        ]);
        $response->assertStatus(422);
        
        // Test invalid RSSI (too low)
        $response = $this->postJson('/api/wifi/calculate-distance', [
            'rssi' => -150
        ]);
        $response->assertStatus(422);
    }
    
    /** @test */
    public function it_stores_calculation_to_wasabi()
    {
        $response = $this->postJson('/api/wifi/calculate-distance', [
            'rssi' => -70
        ]);
        
        $response->assertStatus(200);
        
        // Verify calculation was stored
        $files = Storage::disk('wasabi')->files('calculations');
        $this->assertNotEmpty($files);
    }
    
    /** @test */
    public function it_can_retrieve_scan_history()
    {
        // Create some test scans
        $scanData1 = [
            'timestamp' => '2024-12-25T00:00:00Z',
            'networks' => [['name' => 'Network1', 'signalStrength' => 80]],
            'total_count' => 1
        ];
        
        $scanData2 = [
            'timestamp' => '2024-12-25T01:00:00Z',
            'networks' => [['name' => 'Network2', 'signalStrength' => 70]],
            'total_count' => 1
        ];
        
        Storage::disk('wasabi')->put('wifi-scans/scan1.json', json_encode($scanData1));
        Storage::disk('wasabi')->put('wifi-scans/scan2.json', json_encode($scanData2));
        
        $response = $this->getJson('/api/wifi/history');
        
        $response->assertStatus(200)
                 ->assertJsonCount(2);
    }
    
    /** @test */
    public function it_can_limit_scan_history_results()
    {
        // Create 5 test scans
        for ($i = 1; $i <= 5; $i++) {
            $scanData = [
                'timestamp' => '2024-12-25T0' . $i . ':00:00Z',
                'networks' => [],
                'total_count' => 0
            ];
            Storage::disk('wasabi')->put("wifi-scans/scan{$i}.json", json_encode($scanData));
        }
        
        $response = $this->getJson('/api/wifi/history?limit=3');
        
        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }
    
    /** @test */
    public function it_categorizes_signal_strength_correctly()
    {
        // Test Strong signal
        $response = $this->postJson('/api/wifi/calculate-distance', ['rssi' => -45]);
        $this->assertEquals('Strong', $response->json('signalStrength'));
        
        // Test Medium signal
        $response = $this->postJson('/api/wifi/calculate-distance', ['rssi' => -65]);
        $this->assertEquals('Medium', $response->json('signalStrength'));
        
        // Test Weak signal
        $response = $this->postJson('/api/wifi/calculate-distance', ['rssi' => -85]);
        $this->assertEquals('Weak', $response->json('signalStrength'));
    }
}
