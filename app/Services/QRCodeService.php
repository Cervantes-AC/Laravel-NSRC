<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;

class QRCodeService
{
    /**
     * Generate a QR code from data
     *
     * @param string $data The data to encode
     * @param int $size The size of the QR code (default 200)
     * @param string $errorCorrection Error correction level (L, M, Q, H)
     * @return string Base64 encoded PNG image
     */
    public static function generate(string $data, int $size = 200, string $errorCorrection = 'M'): string
    {
        try {
            $qrCode = QrCode::create($data)
                ->setSize($size)
                ->setMargin(10)
                ->setErrorCorrectionLevel(ErrorCorrectionLevel::from($errorCorrection));

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            return base64_encode($result->getString());
        } catch (\Exception $e) {
            // Fallback to API-based QR code
            return self::generateViaApi($data, $size, $errorCorrection);
        }
    }

    /**
     * Generate QR code via external API (fallback)
     *
     * @param string $data The data to encode
     * @param int $size The size of the QR code
     * @param string $errorCorrection Error correction level
     * @return string URL to QR code image
     */
    public static function generateViaApi(string $data, int $size = 200, string $errorCorrection = 'M'): string
    {
        $encodedData = urlencode($data);
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encodedData}&ecc={$errorCorrection}";
    }

    /**
     * Generate SVG QR code
     *
     * @param string $data The data to encode
     * @param int $size The size of the QR code
     * @return string SVG string
     */
    public static function generateSvg(string $data, int $size = 200): string
    {
        try {
            $qrCode = QrCode::create($data)
                ->setSize($size)
                ->setMargin(10)
                ->setErrorCorrectionLevel(ErrorCorrectionLevel::Medium);

            $writer = new \Endroid\QrCode\Writer\SvgWriter();
            $result = $writer->write($qrCode);

            return $result->getString();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Generate TOTP QR code for 2FA setup
     *
     * @param string $email User email
     * @param string $secret TOTP secret
     * @param string $issuer Application name
     * @param int $size QR code size
     * @return string Base64 encoded image or API URL
     */
    public static function generateTotpQrCode(string $email, string $secret, string $issuer = 'NSRC AMS', int $size = 200): string
    {
        $otpAuthUrl = sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
            urlencode($issuer),
            urlencode($email),
            $secret,
            urlencode($issuer)
        );

        return self::generate($otpAuthUrl, $size);
    }

    /**
     * Generate TOTP QR code URL (for API-based generation)
     *
     * @param string $email User email
     * @param string $secret TOTP secret
     * @param string $issuer Application name
     * @param int $size QR code size
     * @return string URL to QR code image
     */
    public static function generateTotpQrCodeUrl(string $email, string $secret, string $issuer = 'NSRC AMS', int $size = 200): string
    {
        $otpAuthUrl = sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
            urlencode($issuer),
            urlencode($email),
            $secret,
            urlencode($issuer)
        );

        return self::generateViaApi($otpAuthUrl, $size);
    }
}
