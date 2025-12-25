<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class WasabiStorageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Use fake storage for testing
        Storage::fake('wasabi');
    }
    
    /** @test */
    public function it_can_store_wifi_scan_data()
    {
        $data = json_encode([
            'networks' => [
                ['name' => 'TestWiFi', 'signal' => 80]
            ]
        ]);
        
        Storage::disk('wasabi')->put('scans/test.json', $data);
        
        Storage::disk('wasabi')->assertExists('scans/test.json');
    }
    
    /** @test */
    public function it_can_retrieve_stored_data()
    {
        $data = json_encode(['network' => 'TestWiFi', 'signal' => 80]);
        Storage::disk('wasabi')->put('scans/test.json', $data);
        
        $retrieved = Storage::disk('wasabi')->get('scans/test.json');
        
        $this->assertEquals($data, $retrieved);
    }
    
    /** @test */
    public function it_can_delete_files()
    {
        Storage::disk('wasabi')->put('scans/test.json', 'test data');
        
        Storage::disk('wasabi')->delete('scans/test.json');
        
        Storage::disk('wasabi')->assertMissing('scans/test.json');
    }
    
    /** @test */
    public function it_can_list_files_in_directory()
    {
        Storage::disk('wasabi')->put('scans/scan1.json', 'data1');
        Storage::disk('wasabi')->put('scans/scan2.json', 'data2');
        Storage::disk('wasabi')->put('scans/scan3.json', 'data3');
        
        $files = Storage::disk('wasabi')->files('scans');
        
        $this->assertCount(3, $files);
        $this->assertContains('scans/scan1.json', $files);
        $this->assertContains('scans/scan2.json', $files);
        $this->assertContains('scans/scan3.json', $files);
    }
    
    /** @test */
    public function it_can_check_file_existence()
    {
        Storage::disk('wasabi')->put('test.json', 'data');
        
        $this->assertTrue(Storage::disk('wasabi')->exists('test.json'));
        $this->assertFalse(Storage::disk('wasabi')->exists('nonexistent.json'));
    }
    
    /** @test */
    public function it_stores_json_data_correctly()
    {
        $scanData = [
            'timestamp' => '2024-12-25T00:00:00Z',
            'networks' => [
                ['name' => 'HomeNetwork', 'signalStrength' => 85],
                ['name' => 'GuestWiFi', 'signalStrength' => 60]
            ],
            'total_count' => 2
        ];
        
        Storage::disk('wasabi')->put(
            'wifi-scans/test-scan.json',
            json_encode($scanData, JSON_PRETTY_PRINT)
        );
        
        $retrieved = json_decode(
            Storage::disk('wasabi')->get('wifi-scans/test-scan.json'),
            true
        );
        
        $this->assertEquals($scanData, $retrieved);
        $this->assertEquals(2, $retrieved['total_count']);
    }
}
