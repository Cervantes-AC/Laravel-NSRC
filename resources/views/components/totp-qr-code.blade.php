@props([
    'email' => '',
    'secret' => '',
    'issuer' => 'NSRC AMS',
    'size' => 250,
    'title' => 'Scan with Authenticator App',
    'instructions' => true,
])

@php
    use App\Services\QRCodeService;
    
    // Generate TOTP QR code URL
    $qrUrl = QRCodeService::generateTotpQrCodeUrl($email, $secret, $issuer, $size);
@endphp

<div {{ $attributes->merge(['class' => 'totp-qr-code-wrapper']) }}>
    <div class="totp-qr-code-container">
        {{-- Title --}}
        <div class="totp-qr-header">
            <h3 class="totp-qr-title">{{ $title }}</h3>
        </div>

        {{-- QR Code --}}
        <div class="totp-qr-image-wrapper">
            <img 
                src="{{ $qrUrl }}" 
                alt="TOTP QR Code for {{ $email }}" 
                class="totp-qr-image"
                width="{{ $size }}"
                height="{{ $size }}"
            />
        </div>

        {{-- Instructions --}}
        @if($instructions)
            <div class="totp-qr-instructions">
                <p class="totp-qr-instruction-title">How to set up:</p>
                <ol class="totp-qr-instruction-list">
                    <li>Download an authenticator app (Google Authenticator, Microsoft Authenticator, Authy, etc.)</li>
                    <li>Open the app and select "Add Account"</li>
                    <li>Choose "Scan QR Code"</li>
                    <li>Scan this QR code with your phone camera</li>
                    <li>Enter the 6-digit code from your authenticator app to verify</li>
                </ol>
            </div>
        @endif

        {{-- Manual Entry Option --}}
        <div class="totp-qr-manual">
            <p class="totp-qr-manual-title">Can't scan?</p>
            <p class="totp-qr-manual-text">Enter this code manually in your authenticator app:</p>
            <div class="totp-qr-secret-box">
                <code class="totp-qr-secret">{{ $secret }}</code>
                <button 
                    type="button" 
                    class="totp-qr-copy-btn"
                    onclick="navigator.clipboard.writeText('{{ $secret }}'); this.textContent = 'Copied!'; setTimeout(() => this.textContent = 'Copy', 2000);"
                    title="Copy to clipboard"
                >
                    Copy
                </button>
            </div>
        </div>

        {{-- Account Info --}}
        <div class="totp-qr-account-info">
            <div class="totp-qr-info-item">
                <span class="totp-qr-info-label">Account:</span>
                <span class="totp-qr-info-value">{{ $email }}</span>
            </div>
            <div class="totp-qr-info-item">
                <span class="totp-qr-info-label">Issuer:</span>
                <span class="totp-qr-info-value">{{ $issuer }}</span>
            </div>
        </div>
    </div>
</div>

<style>
    .totp-qr-code-wrapper {
        display: inline-block;
        width: 100%;
        max-width: 500px;
    }

    .totp-qr-code-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 2rem;
        background: white;
        border-radius: 1rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .totp-qr-header {
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .totp-qr-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .totp-qr-image-wrapper {
        padding: 1.5rem;
        background: #f9fafb;
        border-radius: 0.75rem;
        border: 2px solid #f3f4f6;
        margin-bottom: 1.5rem;
    }

    .totp-qr-image {
        display: block;
        image-rendering: pixelated;
        image-rendering: crisp-edges;
    }

    .totp-qr-instructions {
        width: 100%;
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: #eff6ff;
        border-left: 4px solid #3b82f6;
        border-radius: 0.5rem;
    }

    .totp-qr-instruction-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1e40af;
        margin: 0 0 0.75rem 0;
    }

    .totp-qr-instruction-list {
        margin: 0;
        padding-left: 1.25rem;
        font-size: 0.875rem;
        color: #1e40af;
        line-height: 1.6;
    }

    .totp-qr-instruction-list li {
        margin-bottom: 0.5rem;
    }

    .totp-qr-manual {
        width: 100%;
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        border-radius: 0.5rem;
    }

    .totp-qr-manual-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #92400e;
        margin: 0 0 0.5rem 0;
    }

    .totp-qr-manual-text {
        font-size: 0.875rem;
        color: #92400e;
        margin: 0 0 0.75rem 0;
    }

    .totp-qr-secret-box {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        background: white;
        border: 1px solid #fcd34d;
        border-radius: 0.5rem;
        font-family: 'Courier New', monospace;
    }

    .totp-qr-secret {
        flex: 1;
        font-size: 0.875rem;
        font-weight: 600;
        color: #1f2937;
        letter-spacing: 0.1em;
        word-break: break-all;
    }

    .totp-qr-copy-btn {
        padding: 0.5rem 0.75rem;
        background: #f59e0b;
        color: white;
        border: none;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
        white-space: nowrap;
    }

    .totp-qr-copy-btn:hover {
        background: #d97706;
    }

    .totp-qr-account-info {
        width: 100%;
        padding: 1rem;
        background: #f3f4f6;
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }

    .totp-qr-info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .totp-qr-info-item:last-child {
        margin-bottom: 0;
    }

    .totp-qr-info-label {
        font-weight: 600;
        color: #6b7280;
    }

    .totp-qr-info-value {
        color: #1f2937;
        word-break: break-all;
    }

    /* Responsive */
    @media (max-width: 640px) {
        .totp-qr-code-container {
            padding: 1.5rem;
        }

        .totp-qr-image-wrapper {
            padding: 1rem;
        }

        .totp-qr-title {
            font-size: 1.125rem;
        }
    }
</style>
