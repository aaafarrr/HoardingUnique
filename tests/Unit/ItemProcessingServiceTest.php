<?php

use App\Services\ItemProcessingService;

beforeEach(function () {
    $this->service = new ItemProcessingService();
});

test('rgbToHex converts colors correctly', function () {
    $hex = $this->service->rgbToHex(255, 165, 0);
    expect($hex)->toBe('#ffa500');

    $hex2 = $this->service->rgbToHex(0, 0, 255);
    expect($hex2)->toBe('#0000ff');
});

test('rgbToHsl converts red color correctly', function () {
    // Red HSL: Hue 0, Sat 100%, Lightness 50%
    $hsl = $this->service->rgbToHsl(255, 0, 0);
    expect($hsl)->toBe([0, 100, 50]);
});

test('rgbToHsl converts gold color correctly', function () {
    // Gold: rgb(250, 204, 21)
    $hsl = $this->service->rgbToHsl(250, 204, 21);
    expect($hsl[0])->toBeGreaterThanOrEqual(40)->toBeLessThanOrEqual(60);
    expect($hsl[1])->toBeGreaterThanOrEqual(80);
});

test('determineTierAndPoints assigns SSS+ to gold', function () {
    // Gold (H=48, S=85, L=50) should be SSS+
    $result = $this->service->determineTierAndPoints([48, 85, 50]);
    expect($result['tier'])->toBe('SSS+');
    expect($result['points'])->toBeGreaterThanOrEqual(1100)->toBeLessThanOrEqual(1500);
});

test('determineTierAndPoints assigns D to dark color', function () {
    // Very dark color (L=5) should be D
    $result = $this->service->determineTierAndPoints([48, 85, 5]);
    expect($result['tier'])->toBe('D');
    expect($result['points'])->toBeGreaterThanOrEqual(11)->toBeLessThanOrEqual(29);
});

test('getHammingDistance calculates distance correctly', function () {
    // Identical hashes
    $dist1 = $this->service->getHammingDistance('ffffffffffffffff', 'ffffffffffffffff');
    expect($dist1)->toBe(0);

    // Difference of 1 bit in hex: 'f' (1111) vs 'e' (1110)
    $dist2 = $this->service->getHammingDistance('ffffffffffffffff', 'fffffffffffffffe');
    expect($dist2)->toBe(1);

    // Difference of all bits
    $dist3 = $this->service->getHammingDistance('ffffffffffffffff', '0000000000000000');
    expect($dist3)->toBe(64);
});

test('image dominant color and hashing works with real gd resources', function () {
    // Create a temporary yellow image
    $img = imagecreatetruecolor(50, 50);
    $yellow = imagecolorallocate($img, 255, 255, 0);
    imagefill($img, 0, 0, $yellow);
    
    $tempPath = tempnam(sys_get_temp_dir(), 'test_img') . '.jpg';
    imagejpeg($img, $tempPath);
    imagedestroy($img);

    // Extract dominant color
    $rgb = $this->service->getDominantColor($tempPath);
    expect($rgb)->toBe([255, 255, 0]);

    // Calculate aHash (should be stable for single color image)
    $hash = $this->service->getAverageHash($tempPath);
    expect(strlen($hash))->toBe(16);

    // Clean up
    @unlink($tempPath);
});
