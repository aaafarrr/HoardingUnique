<?php

namespace App\Services;

use App\Models\UniqueItem;
use Illuminate\Validation\ValidationException;

class ItemProcessingService
{
    /**
     * Extract dominant color from image.
     */
    public function getDominantColor(string $path): array
    {
        $info = @getimagesize($path);
        if (!$info) {
            throw new \InvalidArgumentException('File is not a valid image.');
        }

        $type = $info[2];
        $img = null;

        switch ($type) {
            case IMAGETYPE_JPEG:
                $img = @imagecreatefromjpeg($path);
                break;
            case IMAGETYPE_PNG:
                $img = @imagecreatefrompng($path);
                break;
            case IMAGETYPE_GIF:
                $img = @imagecreatefromgif($path);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    $img = @imagecreatefromwebp($path);
                }
                break;
        }

        if (!$img) {
            throw new \InvalidArgumentException('Failed to read image. Make sure GD library is enabled.');
        }

        $width = imagesx($img);
        $height = imagesy($img);

        // Resize to 1x1 to average all colors
        $resized = imagecreatetruecolor(1, 1);
        imagecopyresampled($resized, $img, 0, 0, 0, 0, 1, 1, $width, $height);

        $color = imagecolorat($resized, 0, 0);
        $r = ($color >> 16) & 0xFF;
        $g = ($color >> 8) & 0xFF;
        $b = $color & 0xFF;

        imagedestroy($img);
        imagedestroy($resized);

        return [$r, $g, $b];
    }

    /**
     * Convert RGB to HEX string.
     */
    public function rgbToHex(int $r, int $g, int $b): string
    {
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Convert RGB to HSL.
     */
    public function rgbToHsl(int $r, int $g, int $b): array
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);

        $h = 0;
        $s = 0;
        $l = ($max + $min) / 2;

        if ($max !== $min) {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

            switch ($max) {
                case $r:
                    $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
                    break;
                case $g:
                    $h = ($b - $r) / $d + 2;
                    break;
                case $b:
                    $h = ($r - $g) / $d + 4;
                    break;
            }

            $h /= 6;
        }

        return [
            (int) round($h * 360),
            (int) round($s * 100),
            (int) round($l * 100)
        ];
    }

    /**
     * Determine Tier and calculate Points based on HSL + RNG.
     */
    public function determineTierAndPoints(array $hsl): array
    {
        [$h, $s, $l] = $hsl;

        // Base tiers mapping
        if ($l < 15 || ($l > 85 && $s < 10)) {
            // Extremely dark or extremely light white/gray -> D
            $tier = 'D';
        } elseif ($s < 15) {
            // Low saturation -> Grayish -> D+
            $tier = 'D+';
        } elseif ($h >= 40 && $h <= 60 && $s >= 65 && $l >= 40) {
            // Gold / Bright Yellow -> SSS+ / SSS
            $tier = $s >= 80 ? 'SSS+' : 'SSS';
        } elseif ($h >= 250 && $h <= 310 && $s >= 60 && $l >= 35) {
            // Purple / Violet -> SS+ / SS
            $tier = $s >= 75 ? 'SS+' : 'SS';
        } elseif (($h >= 340 || $h <= 20) && $s >= 60 && $l >= 35) {
            // Red / Crimson -> S+ / S
            $tier = $s >= 75 ? 'S+' : 'S';
        } elseif ($h >= 170 && $h <= 240 && $s >= 50) {
            // Blue / Cyan -> A+ / A
            $tier = $s >= 70 ? 'A+' : 'A';
        } elseif ($h >= 75 && $h <= 160 && $s >= 40) {
            // Green -> B+ / B
            $tier = $s >= 60 ? 'B+' : 'B';
        } elseif ($h >= 21 && $h <= 39 && $s >= 40) {
            // Orange / Brown -> C+ / C
            $tier = $s >= 60 ? 'C+' : 'C';
        } else {
            // fallback
            $tier = 'D+';
        }

        // Tier to points mapping with RNG
        switch ($tier) {
            case 'SSS+':
                $base = 1000;
                $rng = rand(100, 500);
                break;
            case 'SSS':
                $base = 800;
                $rng = rand(50, 199);
                break;
            case 'SS+':
                $base = 650;
                $rng = rand(50, 149);
                break;
            case 'SS':
                $base = 500;
                $rng = rand(50, 149);
                break;
            case 'S+':
                $base = 400;
                $rng = rand(30, 99);
                break;
            case 'S':
                $base = 300;
                $rng = rand(30, 99);
                break;
            case 'A+':
                $base = 250;
                $rng = rand(20, 49);
                break;
            case 'A':
                $base = 200;
                $rng = rand(20, 49);
                break;
            case 'B+':
                $base = 150;
                $rng = rand(10, 49);
                break;
            case 'B':
                $base = 120;
                $rng = rand(10, 29);
                break;
            case 'C+':
                $base = 90;
                $rng = rand(5, 29);
                break;
            case 'C':
                $base = 60;
                $rng = rand(5, 29);
                break;
            case 'D+':
                $base = 30;
                $rng = rand(1, 29);
                break;
            case 'D':
            default:
                $base = 10;
                $rng = rand(1, 19);
                break;
        }

        return [
            'tier' => $tier,
            'points' => $base + $rng,
        ];
    }

    /**
     * Generate 8x8 Average Hash (aHash) for image.
     */
    public function getAverageHash(string $path): string
    {
        $info = @getimagesize($path);
        if (!$info) {
            throw new \InvalidArgumentException('File is not a valid image.');
        }

        $type = $info[2];
        $img = null;

        switch ($type) {
            case IMAGETYPE_JPEG:
                $img = @imagecreatefromjpeg($path);
                break;
            case IMAGETYPE_PNG:
                $img = @imagecreatefrompng($path);
                break;
            case IMAGETYPE_GIF:
                $img = @imagecreatefromgif($path);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    $img = @imagecreatefromwebp($path);
                }
                break;
        }

        if (!$img) {
            throw new \InvalidArgumentException('Failed to read image. Make sure GD library is enabled.');
        }

        $width = imagesx($img);
        $height = imagesy($img);

        // Resize to 8x8
        $resized = imagecreatetruecolor(8, 8);
        imagecopyresampled($resized, $img, 0, 0, 0, 0, 8, 8, $width, $height);

        $grays = [];
        $sum = 0;

        for ($y = 0; $y < 8; $y++) {
            for ($x = 0; $x < 8; $x++) {
                $color = imagecolorat($resized, $x, $y);
                $r = ($color >> 16) & 0xFF;
                $g = ($color >> 8) & 0xFF;
                $b = $color & 0xFF;

                // Luminosity formula
                $gray = (int) round(0.299 * $r + 0.587 * $g + 0.114 * $b);
                $grays[] = $gray;
                $sum += $gray;
            }
        }

        $average = $sum / 64;
        $hashBinary = '';

        foreach ($grays as $gray) {
            $hashBinary .= ($gray >= $average) ? '1' : '0';
        }

        // Convert binary string (64 characters) to hex (16 characters)
        $hex = '';
        for ($i = 0; $i < 64; $i += 4) {
            $nibble = substr($hashBinary, $i, 4);
            $hex .= dechex(bindec($nibble));
        }

        imagedestroy($img);
        imagedestroy($resized);

        return str_pad($hex, 16, '0', STR_PAD_LEFT);
    }

    /**
     * Compute Hamming distance between two hex hashes.
     */
    public function getHammingDistance(string $hash1, string $hash2): int
    {
        if (strlen($hash1) !== 16 || strlen($hash2) !== 16) {
            return 64;
        }

        $distance = 0;
        for ($i = 0; $i < 16; $i++) {
            $char1 = hexdec($hash1[$i]);
            $char2 = hexdec($hash2[$i]);

            $xor = $char1 ^ $char2;

            while ($xor > 0) {
                if ($xor & 1) {
                    $distance++;
                }
                $xor >>= 1;
            }
        }

        return $distance;
    }

    /**
     * Check if a duplicate item exists in database.
     * Throws ValidationException if a match (Hamming distance <= 8) is found.
     */
    public function checkDeduplication(string $newHash): void
    {
        $existingItems = UniqueItem::select('id', 'title', 'image_hash', 'user_id')->get();

        foreach ($existingItems as $item) {
            $distance = $this->getHammingDistance($newHash, $item->image_hash);
            if ($distance <= 8) {
                throw ValidationException::withMessages([
                    'image' => "Barang unik ini terdeteksi sebagai duplikat dari barang \"" . $item->title . "\" yang sudah diunggah sebelumnya (Tingkat kemiripan tinggi).",
                ]);
            }
        }
    }
}
