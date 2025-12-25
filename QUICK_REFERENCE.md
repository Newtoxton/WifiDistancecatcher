# Wasabi + Laravel Integration - Quick Reference Guide

## 1-Minute Setup Checklist

### Prerequisites
- [ ] Laravel 8.x or higher installed
- [ ] Composer installed
- [ ] Wasabi account created
- [ ] Wasabi bucket created

### Installation (5 minutes)

```bash
# 1. Install AWS S3 package
composer require league/flysystem-aws-s3-v3 "^3.0"

# 2. Add to .env
WASABI_ACCESS_KEY_ID=your_access_key
WASABI_SECRET_ACCESS_KEY=your_secret_key
WASABI_DEFAULT_REGION=us-east-1
WASABI_BUCKET=your_bucket_name
WASABI_ENDPOINT=https://s3.us-east-1.wasabisys.com
FILESYSTEM_DISK=wasabi

# 3. Add to config/filesystems.php
'wasabi' => [
    'driver' => 's3',
    'key' => env('WASABI_ACCESS_KEY_ID'),
    'secret' => env('WASABI_SECRET_ACCESS_KEY'),
    'region' => env('WASABI_DEFAULT_REGION'),
    'bucket' => env('WASABI_BUCKET'),
    'endpoint' => env('WASABI_ENDPOINT'),
    'use_path_style_endpoint' => false,
],

# 4. Test connection
php artisan tinker
>>> Storage::disk('wasabi')->put('test.txt', 'It works!');
>>> Storage::disk('wasabi')->get('test.txt');
```

✅ **Done!** You can now use Wasabi in your Laravel app.

---

## Common Operations Cheat Sheet

### Upload File
```php
use Illuminate\Support\Facades\Storage;

// Simple upload
Storage::disk('wasabi')->put('path/file.txt', $contents);

// Upload from request
$path = $request->file('file')->store('uploads', 'wasabi');

// Upload with custom name
$path = $request->file('file')->storeAs('uploads', 'custom-name.txt', 'wasabi');
```

### Download/Retrieve File
```php
// Get contents
$contents = Storage::disk('wasabi')->get('path/file.txt');

// Download file
return Storage::disk('wasabi')->download('path/file.txt');

// Download with custom name
return Storage::disk('wasabi')->download('path/file.txt', 'custom-name.txt');
```

### Check File Existence
```php
if (Storage::disk('wasabi')->exists('path/file.txt')) {
    // File exists
}

if (Storage::disk('wasabi')->missing('path/file.txt')) {
    // File doesn't exist
}
```

### Delete File
```php
// Delete single file
Storage::disk('wasabi')->delete('path/file.txt');

// Delete multiple files
Storage::disk('wasabi')->delete(['file1.txt', 'file2.txt']);

// Delete directory
Storage::disk('wasabi')->deleteDirectory('path/to/directory');
```

### List Files
```php
// List files in directory
$files = Storage::disk('wasabi')->files('path/to/directory');

// List all files recursively
$allFiles = Storage::disk('wasabi')->allFiles('path/to/directory');

// List directories
$directories = Storage::disk('wasabi')->directories('path/to/directory');
```

### Get File Info
```php
// Get file size
$size = Storage::disk('wasabi')->size('path/file.txt');

// Get last modified time
$lastModified = Storage::disk('wasabi')->lastModified('path/file.txt');

// Get MIME type
$mimeType = Storage::disk('wasabi')->mimeType('path/file.txt');
```

### Generate URLs
```php
// Get public URL
$url = Storage::disk('wasabi')->url('path/file.txt');

// Get temporary URL (expires in 5 minutes)
$url = Storage::disk('wasabi')->temporaryUrl(
    'path/file.txt',
    now()->addMinutes(5)
);

// Temporary URL with custom name
$url = Storage::disk('wasabi')->temporaryUrl(
    'path/file.txt',
    now()->addMinutes(5),
    ['ResponseContentDisposition' => 'attachment; filename="download.txt"']
);
```

### Copy & Move Files
```php
// Copy file
Storage::disk('wasabi')->copy('old/path.txt', 'new/path.txt');

// Move file
Storage::disk('wasabi')->move('old/path.txt', 'new/path.txt');
```

### Streaming (for large files)
```php
// Write stream
$resource = fopen('local/large-file.txt', 'r');
Storage::disk('wasabi')->writeStream('path/large-file.txt', $resource);

// Read stream
$resource = Storage::disk('wasabi')->readStream('path/large-file.txt');
```

---

## Wasabi Endpoints by Region

| Region | Endpoint |
|--------|----------|
| US East 1 (N. Virginia) | https://s3.us-east-1.wasabisys.com |
| US East 2 (N. Virginia) | https://s3.us-east-2.wasabisys.com |
| US West 1 (Oregon) | https://s3.us-west-1.wasabisys.com |
| EU Central 1 (Amsterdam) | https://s3.eu-central-1.wasabisys.com |
| AP Northeast 1 (Tokyo) | https://s3.ap-northeast-1.wasabisys.com |
| AP Northeast 2 (Osaka) | https://s3.ap-northeast-2.wasabisys.com |

---

## WiFiDistanceCatcher Specific Examples

### Store Wi-Fi Scan
```php
$networks = [
    ['name' => 'HomeWiFi', 'signalStrength' => 85],
    ['name' => 'GuestWiFi', 'signalStrength' => 60]
];

$data = [
    'timestamp' => now()->toIso8601String(),
    'networks' => $networks,
    'total_count' => count($networks)
];

Storage::disk('wasabi')->put(
    'wifi-scans/' . now()->format('Y-m-d-His') . '.json',
    json_encode($data, JSON_PRETTY_PRINT)
);
```

### Store Distance Calculation
```php
$calculation = [
    'timestamp' => now()->toIso8601String(),
    'rssi' => -70,
    'distance' => '17.78 meters',
    'signal_strength' => 'Medium'
];

Storage::disk('wasabi')->put(
    'calculations/' . now()->format('Y-m-d-His') . '.json',
    json_encode($calculation, JSON_PRETTY_PRINT)
);
```

### Retrieve Scan History
```php
$files = Storage::disk('wasabi')->files('wifi-scans');
$latestFiles = array_slice(array_reverse($files), 0, 10);

$scans = [];
foreach ($latestFiles as $file) {
    $content = Storage::disk('wasabi')->get($file);
    $scans[] = json_decode($content, true);
}
```

---

## Error Handling

```php
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\Log;

try {
    Storage::disk('wasabi')->put('file.txt', $data);
} catch (S3Exception $e) {
    Log::error('Wasabi error: ' . $e->getMessage());
    
    // Fallback to local storage
    Storage::disk('local')->put('file.txt', $data);
}
```

---

## Testing

```php
use Illuminate\Support\Facades\Storage;

// In your test's setUp method
protected function setUp(): void
{
    parent::setUp();
    Storage::fake('wasabi');
}

// In your test
public function test_file_upload()
{
    Storage::disk('wasabi')->put('test.txt', 'content');
    
    Storage::disk('wasabi')->assertExists('test.txt');
    Storage::disk('wasabi')->assertMissing('nonexistent.txt');
}
```

---

## Performance Tips

1. **Cache file existence checks**
```php
Cache::remember('file-exists-' . $filename, 300, function() use ($filename) {
    return Storage::disk('wasabi')->exists($filename);
});
```

2. **Use streaming for large files**
```php
Storage::disk('wasabi')->writeStream('large.csv', $stream);
```

3. **Batch operations**
```php
$files = ['file1.txt', 'file2.txt', 'file3.txt'];
Storage::disk('wasabi')->delete($files); // One API call
```

4. **Use queued jobs for uploads**
```php
dispatch(function () use ($file, $path) {
    Storage::disk('wasabi')->put($path, file_get_contents($file));
})->afterCommit();
```

---

## Security Best Practices

1. **Never commit credentials**
```bash
# Add to .gitignore
.env
.env.backup
```

2. **Use temporary URLs for sensitive files**
```php
$url = Storage::disk('wasabi')->temporaryUrl('private.pdf', now()->addMinutes(5));
```

3. **Validate uploads**
```php
$request->validate([
    'file' => 'required|file|max:10240|mimes:json,csv,txt'
]);
```

4. **Encrypt sensitive data**
```php
$encrypted = encrypt($sensitiveData);
Storage::disk('wasabi')->put('encrypted.json', $encrypted);
```

---

## Troubleshooting

### "Class 'League\Flysystem\AwsS3V3\AwsS3V3Adapter' not found"
```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

### "InvalidAccessKeyId: The AWS Access Key Id you provided does not exist"
- Check `.env` file has correct credentials
- Run `php artisan config:clear`

### "SignatureDoesNotMatch"
- Check secret key is correct
- Verify no extra spaces in `.env`

### "NoSuchBucket"
- Verify bucket name in `.env`
- Check bucket exists in Wasabi console

### Connection timeout
- Check firewall settings
- Verify endpoint matches your region
- Test with: `curl https://s3.us-east-1.wasabisys.com`

---

## Environment Variables Quick Reference

```env
# Required
WASABI_ACCESS_KEY_ID=your_access_key_here
WASABI_SECRET_ACCESS_KEY=your_secret_key_here
WASABI_BUCKET=your_bucket_name

# Optional (with defaults)
WASABI_DEFAULT_REGION=us-east-1
WASABI_ENDPOINT=https://s3.us-east-1.wasabisys.com
FILESYSTEM_DISK=wasabi
```

---

## Summary

| Operation | Code | Time |
|-----------|------|------|
| Setup | Copy config, add env vars | 5 min |
| Upload file | `Storage::disk('wasabi')->put()` | 1 line |
| Get file | `Storage::disk('wasabi')->get()` | 1 line |
| Delete file | `Storage::disk('wasabi')->delete()` | 1 line |
| Test locally | `Storage::fake('wasabi')` | 1 line |

**Difficulty: ⭐ (1/5)** - Extremely easy!

---

**See also:**
- [Full Assessment Document](../WASABI_LARAVEL_INTEGRATION_ASSESSMENT.md)
- [Complete Integration Example](../examples/laravel-wasabi-integration/)
- [Laravel Storage Documentation](https://laravel.com/docs/10.x/filesystem)
