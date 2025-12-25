# Laravel + Wasabi Integration Example

This directory contains a complete working example of integrating Wasabi storage with Laravel in the context of the WiFiDistanceCatcher application.

## Quick Start

### 1. Install Laravel

```bash
composer create-project laravel/laravel wifi-distance-laravel
cd wifi-distance-laravel
```

### 2. Install Required Packages

```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

### 3. Configure Environment

Copy the `.env.example` file in this directory to your Laravel project's `.env` file and update with your Wasabi credentials.

### 4. Copy Configuration Files

```bash
# Copy the filesystem configuration
cp config/filesystems.php config/filesystems.php.backup
cp examples/laravel-wasabi-integration/config/filesystems.php config/filesystems.php

# Copy the services
cp -r examples/laravel-wasabi-integration/app/Services app/
cp -r examples/laravel-wasabi-integration/app/Http/Controllers/* app/Http/Controllers/

# Copy the routes
cat examples/laravel-wasabi-integration/routes/api.php >> routes/api.php
```

### 5. Test the Integration

```bash
php artisan tinker
```

```php
// Test basic storage
Storage::disk('wasabi')->put('test.txt', 'Hello Wasabi!');
Storage::disk('wasabi')->get('test.txt'); // Should return: "Hello Wasabi!"

// Test Wi-Fi scan storage
$data = ['networks' => [['name' => 'TestWiFi', 'signal' => 80]]];
Storage::disk('wasabi')->put('wifi-scans/test-scan.json', json_encode($data));
```

### 6. Run the Application

```bash
php artisan serve
```

Test the API endpoints:

```bash
# Get Wi-Fi networks
curl http://localhost:8000/api/wifi/networks

# Calculate distance
curl -X POST http://localhost:8000/api/wifi/calculate-distance \
  -H "Content-Type: application/json" \
  -d '{"rssi": -70}'

# Get scan history
curl http://localhost:8000/api/wifi/history
```

## File Structure

```
examples/laravel-wasabi-integration/
├── README.md                           # This file
├── .env.example                        # Environment configuration template
├── config/
│   └── filesystems.php                 # Wasabi disk configuration
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── WifiScanController.php  # Main API controller
│   └── Services/
│       ├── WifiScanService.php         # Wi-Fi scanning logic
│       └── DistanceCalculatorService.php # Distance calculation
├── routes/
│   └── api.php                         # API routes
└── tests/
    ├── Unit/
    │   └── WasabiStorageTest.php       # Unit tests
    └── Feature/
        └── WifiApiTest.php             # Feature tests
```

## Features Demonstrated

1. **Basic Wasabi Storage Operations**
   - File upload
   - File retrieval
   - File listing
   - File deletion

2. **WiFiDistanceCatcher Integration**
   - Wi-Fi network scanning
   - RSSI-based distance calculation
   - Automatic storage of scan results
   - Historical data retrieval

3. **Best Practices**
   - Service layer architecture
   - Dependency injection
   - Error handling
   - Testing with Storage facade mocking

## API Endpoints

### GET /api/wifi/networks
Scans available Wi-Fi networks and stores results to Wasabi.

**Response:**
```json
[
  {
    "name": "HomeNetwork",
    "signalStrength": 85
  },
  {
    "name": "GuestWiFi",
    "signalStrength": 60
  }
]
```

### POST /api/wifi/calculate-distance
Calculates distance from RSSI value and stores result to Wasabi.

**Request:**
```json
{
  "rssi": -70
}
```

**Response:**
```json
{
  "distance": "17.78 meters",
  "signalStrength": "Medium",
  "rssi": -70
}
```

### GET /api/wifi/history
Retrieves historical scan data from Wasabi.

**Query Parameters:**
- `limit` (optional): Number of records to return (default: 10)

**Response:**
```json
[
  {
    "timestamp": "2024-12-25T00:00:00.000000Z",
    "networks": [...],
    "total_count": 5
  }
]
```

## Testing

Run the included tests:

```bash
# Unit tests
php artisan test --filter WasabiStorageTest

# Feature tests
php artisan test --filter WifiApiTest

# All tests
php artisan test
```

## Troubleshooting

### Connection Issues

Test Wasabi connectivity:
```php
php artisan tinker
>>> Storage::disk('wasabi')->put('test.txt', 'test');
>>> Storage::disk('wasabi')->exists('test.txt');
```

### Credentials Issues

Verify your `.env` configuration:
```bash
php artisan config:clear
php artisan config:cache
```

### Permission Issues

Ensure your Wasabi bucket has proper permissions:
- Read/Write access for your access key
- CORS configured if accessing from frontend

## Next Steps

1. **Add Authentication**: Implement user authentication to track per-user scans
2. **Add Frontend**: Build a Vue.js or React frontend
3. **Add Caching**: Cache frequently accessed data
4. **Add Queues**: Process large scans asynchronously
5. **Add Monitoring**: Implement logging and monitoring

## Resources

- [Main Assessment Document](../../WASABI_LARAVEL_INTEGRATION_ASSESSMENT.md)
- [Laravel Filesystem Documentation](https://laravel.com/docs/10.x/filesystem)
- [Wasabi Documentation](https://wasabi.com/help/)
