@props([
    'data' => '',
    'size' => 200,
    'errorCorrection' => 'M',
    'title' => null,
    'downloadable' => false,
])

@php
    use App\Services\QRCodeService;
    
    // Generate QR code URL
    $qrUrl = QRCodeService::generateViaApi($data, $size, $errorCorrection);
@endphp

<div {{ $attributes->merge(['class' => 'qr-code-wrapper']) }}>
    <div class="qr-code-container">
        @if($title)
            <div class="qr-code-header">
                <h3 class="qr-code-title">{{ $title }}</h3>
            </div>
        @endif
        
        <div class="qr-code-image-wrapper">
            <img 
                src="{{ $qrUrl }}" 
                alt="QR Code{{ $title ? ': ' . $title : '' }}" 
                class="qr-code-image"
                width="{{ $size }}"
                height="{{ $size }}"
            />
        </div>
        
        @if($downloadable)
            <div class="qr-code-actions">
                <a href="{{ $qrUrl }}" download="qr-code.png" class="qr-code-download-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download
                </a>
            </div>
        @endif
    </div>
</div>

<style>
    .qr-code-wrapper {
        display: inline-block;
    }
    
    .qr-code-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .qr-code-header {
        margin-bottom: 1rem;
        text-align: center;
    }
    
    .qr-code-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }
    
    .qr-code-image-wrapper {
        padding: 1rem;
        background: white;
        border-radius: 0.5rem;
        border: 2px solid #f3f4f6;
    }
    
    .qr-code-image {
        display: block;
        image-rendering: pixelated;
        image-rendering: crisp-edges;
    }
    
    .qr-code-actions {
        margin-top: 1rem;
        display: flex;
        gap: 0.5rem;
    }
    
    .qr-code-download-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: #3b82f6;
        color: white;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: background-color 0.2s;
    }
    
    .qr-code-download-btn:hover {
        background: #2563eb;
    }
</style>
