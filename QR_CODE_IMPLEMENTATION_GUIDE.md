# QR Code Implementation Guide

## Overview
This guide explains how to use the QR code components and services in the NSRC AMS project.

## What's Been Added

### 1. QRCodeService (`app/Services/QRCodeService.php`)
A service class for generating QR codes with multiple methods:

- `generate()` - Generate QR code from any data
- `generateViaApi()` - Generate QR code using external API (fallback)
- `generateSvg()` - Generate SVG QR code
- `generateTotpQrCode()` - Generate TOTP QR code for 2FA
- `generateTotpQrCodeUrl()` - Generate TOTP QR code URL

### 2. QR Code Component (`resources/views/components/qr-code.blade.php`)
A reusable Blade component for displaying QR codes.

### 3. TOTP QR Code Component (`resources/views/components/totp-qr-code.blade.php`)
A specialized component for displaying TOTP QR codes with instructions.

---

## Usage Examples

### Basic QR Code

```blade
<x-qr-code 
    data="https://example.com"
    size="200"
    errorCorrection="M"
/>
```

### QR Code with Title and Download

```blade
<x-qr-code 
    data="https://example.com"
    size="250"
    title="Scan to Visit Website"
    downloadable="true"
/>
```

### TOTP QR Code (2FA Setup)

```blade
<x-totp-qr-code 
    email="admin@gmail.com"
    secret="HIMBPXHTQHYXJ56G"
    issuer="NSRC AMS"
    size="250"
    title="Scan with Authenticator App"
    instructions="true"
/>
```

### Using the Service Directly

```php
use App\Services\QRCodeService;

// Generate QR code from any data
$qrUrl = QRCodeService::generateViaApi('https://example.com', 200, 'M');

// Generate TOTP QR code
$totpUrl = QRCodeService::generateTotpQrCodeUrl(
    'admin@gmail.com',
    'HIMBPXHTQHYXJ56G',
    'NSRC AMS',
    250
);

// In a view
echo "<img src='{$totpUrl}' alt='TOTP QR Code'>";
```

---

## Component Props

### qr-code Component

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `data` | string | '' | Data to encode in QR code |
| `size` | int | 200 | Size of QR code in pixels |
| `errorCorrection` | string | 'M' | Error correction level (L, M, Q, H) |
| `title` | string | null | Optional title above QR code |
| `downloadable` | bool | false | Show download button |

### totp-qr-code Component

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `email` | string | '' | User email address |
| `secret` | string | '' | TOTP secret key |
| `issuer` | string | 'NSRC AMS' | Application name |
| `size` | int | 250 | Size of QR code in pixels |
| `title` | string | 'Scan with Authenticator App' | Component title |
| `instructions` | bool | true | Show setup instructions |

---

## Error Correction Levels

| Level | Capacity | Use Case |
|-------|----------|----------|
| L | ~7% | Small QR codes, low damage risk |
| M | ~15% | Standard use (recommended) |
| Q | ~25% | Moderate damage risk |
| H | ~30% | High damage risk, outdoor use |

---

## Real-World Examples

### Example 1: 2FA Setup Page

```blade
<div class="max-w-md mx-auto">
    <h1>Enable Two-Factor Authentication</h1>
    
    <x-totp-qr-code 
        email="{{ auth()->user()->email }}"
        secret="{{ $totpSecret }}"
        issuer="NSRC AMS"
        size="250"
    />
    
    <form method="POST" action="{{ route('2fa.verify') }}">
        @csrf
        <div class="mt-6">
            <label for="code">Enter 6-digit code:</label>
            <input 
                type="text" 
                id="code" 
                name="code" 
                maxlength="6" 
                placeholder="000000"
                required
            >
        </div>
        <button type="submit">Verify & Enable 2FA</button>
    </form>
</div>
```

### Example 2: Event Registration QR Code

```blade
<div class="event-card">
    <h2>{{ $event->name }}</h2>
    
    <x-qr-code 
        data="{{ route('events.register', $event) }}"
        size="200"
        title="Scan to Register"
        downloadable="true"
    />
    
    <p>Scan to register for this event</p>
</div>
```

### Example 3: WiFi QR Code

```php
// In controller
$wifiData = "WIFI:T:WPA;S:NetworkName;P:Password;;";
$qrUrl = QRCodeService::generateViaApi($wifiData, 200);
```

```blade
<x-qr-code 
    data="WIFI:T:WPA;S:NetworkName;P:Password;;"
    size="200"
    title="Connect to WiFi"
/>
```

### Example 4: Contact Information QR Code

```php
// vCard format
$vcard = "BEGIN:VCARD
VERSION:3.0
FN:John Doe
TEL:+1234567890
EMAIL:john@example.com
ORG:NSRC
END:VCARD";

$qrUrl = QRCodeService::generateViaApi($vcard, 200);
```

---

## Supported QR Code Data Formats

### URLs
```
https://example.com
```

### Email
```
mailto:user@example.com
```

### Phone
```
tel:+1234567890
```

### SMS
```
smsto:+1234567890:Hello
```

### WiFi
```
WIFI:T:WPA;S:NetworkName;P:Password;;
```

### vCard (Contact)
```
BEGIN:VCARD
VERSION:3.0
FN:John Doe
TEL:+1234567890
EMAIL:john@example.com
END:VCARD
```

### TOTP (2FA)
```
otpauth://totp/NSRC%20AMS:admin%40gmail.com?secret=HIMBPXHTQHYXJ56G&issuer=NSRC%20AMS&algorithm=SHA1&digits=6&period=30
```

### Calendar Event
```
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Example//Example//EN
BEGIN:VEVENT
DTSTART:20260518T100000Z
DTEND:20260518T110000Z
SUMMARY:Meeting
END:VEVENT
END:VCALENDAR
```

---

## Integration with 2FA

### Step 1: Generate Secret
```php
use PragmaRX\Google2FA\Google2FA;

$google2fa = new Google2FA();
$secret = $google2fa->generateSecretKey();
```

### Step 2: Display QR Code
```blade
<x-totp-qr-code 
    email="{{ auth()->user()->email }}"
    secret="{{ $secret }}"
/>
```

### Step 3: Verify Code
```php
$google2fa = new Google2FA();
$valid = $google2fa->verifyKey($secret, $code);
```

---

## Styling & Customization

### Custom Styling
```blade
<x-qr-code 
    data="https://example.com"
    class="my-custom-class"
/>
```

### CSS Classes Available
- `.qr-code-wrapper` - Outer wrapper
- `.qr-code-container` - Main container
- `.qr-code-header` - Header section
- `.qr-code-title` - Title text
- `.qr-code-image-wrapper` - Image wrapper
- `.qr-code-image` - Image element
- `.qr-code-actions` - Actions section
- `.qr-code-download-btn` - Download button

### TOTP CSS Classes
- `.totp-qr-code-wrapper` - Outer wrapper
- `.totp-qr-code-container` - Main container
- `.totp-qr-header` - Header section
- `.totp-qr-title` - Title text
- `.totp-qr-image-wrapper` - Image wrapper
- `.totp-qr-image` - Image element
- `.totp-qr-instructions` - Instructions section
- `.totp-qr-manual` - Manual entry section
- `.totp-qr-secret-box` - Secret key box
- `.totp-qr-account-info` - Account info section

---

## Performance Considerations

### API-Based Generation
- Uses QR Server API (free, no authentication)
- Fast generation
- No server-side processing
- Suitable for most use cases

### Library-Based Generation
- Uses endroid/qr-code library
- More control over output
- Can generate SVG or PNG
- Better for high-volume generation

### Caching
```php
// Cache QR code URL
$qrUrl = Cache::remember("qr_code_{$data}", 3600, function () use ($data) {
    return QRCodeService::generateViaApi($data);
});
```

---

## Security Considerations

### TOTP Secrets
- Never expose secrets in URLs
- Store secrets securely in database
- Use strong random generation
- Implement rate limiting on verification

### QR Code Data
- Validate data before encoding
- Sanitize user input
- Use HTTPS for URLs
- Consider data sensitivity

### Best Practices
```php
// Good: Secure TOTP setup
$secret = app('pragmarx.google2fa')->generateSecretKey();
$user->update(['totp_secret' => encrypt($secret)]);

// Bad: Exposing secret in URL
$url = "https://example.com?secret={$secret}"; // Don't do this!
```

---

## Troubleshooting

### QR Code Not Displaying
1. Check internet connection (API-based generation needs internet)
2. Verify data is not too long
3. Check browser console for errors
4. Try different error correction level

### QR Code Too Small
- Increase `size` parameter
- Recommended minimum: 200px
- For printing: 300px or larger

### QR Code Not Scanning
- Increase error correction level (L → M → Q → H)
- Ensure sufficient contrast
- Check for glare or reflections
- Try different scanner app

### TOTP Not Working
- Verify secret is correct
- Check time synchronization on device
- Ensure algorithm is SHA1
- Verify digits is 6
- Check period is 30 seconds

---

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 11+
- Edge 79+
- Mobile browsers (iOS Safari, Chrome Android)

---

## Dependencies

### Required
- Laravel 12+
- PHP 8.2+

### Optional
- `endroid/qr-code` - For advanced QR code generation
- `pragmarx/google2fa` - For TOTP functionality

---

## API Reference

### QRCodeService::generate()
```php
public static function generate(
    string $data,
    int $size = 200,
    string $errorCorrection = 'M'
): string
```

### QRCodeService::generateViaApi()
```php
public static function generateViaApi(
    string $data,
    int $size = 200,
    string $errorCorrection = 'M'
): string
```

### QRCodeService::generateTotpQrCodeUrl()
```php
public static function generateTotpQrCodeUrl(
    string $email,
    string $secret,
    string $issuer = 'NSRC AMS',
    int $size = 200
): string
```

---

## Examples in Context

### Complete 2FA Setup Flow

```blade
<!-- Step 1: Display QR Code -->
<x-totp-qr-code 
    email="{{ auth()->user()->email }}"
    secret="{{ session('totp_secret') }}"
/>

<!-- Step 2: Verify Code -->
<form method="POST" action="{{ route('2fa.verify') }}">
    @csrf
    <input type="text" name="code" maxlength="6" placeholder="000000">
    <button type="submit">Verify</button>
</form>
```

```php
// Controller
public function verify(Request $request)
{
    $google2fa = new Google2FA();
    $secret = session('totp_secret');
    
    if ($google2fa->verifyKey($secret, $request->code)) {
        auth()->user()->update([
            'totp_secret' => encrypt($secret),
            'totp_enabled' => true,
        ]);
        
        return redirect()->with('success', '2FA enabled!');
    }
    
    return back()->with('error', 'Invalid code');
}
```

---

## Next Steps

1. Review the components in `resources/views/components/`
2. Check the service in `app/Services/QRCodeService.php`
3. Integrate into your 2FA setup flow
4. Test QR code generation
5. Customize styling as needed

---

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review the examples
3. Check browser console for errors
4. Verify data format is correct
5. Test with different QR code scanners

---

**Last Updated:** May 18, 2026
**Version:** 1.0
**Status:** Ready for Use
