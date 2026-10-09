<?php

use App\Models\UniqueItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Fake the public storage disk
    Storage::fake('public');
});

test('guest can access home page successfully', function () {
    $response = $this->get('/');
    $response->assertOk();
});

test('guest is redirected to login when trying to access upload page', function () {
    $response = $this->get('/items/create');
    $response->assertRedirect('/login');
});

test('user can switch application locale', function () {
    $response = $this->get('/lang/id');
    $response->assertRedirect();
    expect(session('locale'))->toBe('id');

    $response2 = $this->get('/lang/en');
    $response2->assertRedirect();
    expect(session('locale'))->toBe('en');
});

test('user can register manually', function () {
    $response = $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
});

test('user can upload a unique item and receives points', function () {
    $user = User::factory()->create(['points' => 0]);
    $this->actingAs($user);

    // Create a fake gold colored image to guarantee SSS+ tier
    $file = UploadedFile::fake()->image('artifact.jpg', 100, 100);
    $img = imagecreatefromjpeg($file->getRealPath());
    $gold = imagecolorallocate($img, 250, 204, 21);
    imagefill($img, 0, 0, $gold);
    imagejpeg($img, $file->getRealPath());
    imagedestroy($img);

    $base64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($file->getRealPath()));

    $response = $this->post('/items', [
        'title' => 'Golden Chalice',
        'description' => 'A beautiful ancient golden chalice.',
        'image_base64' => $base64,
        'latitude' => -6.2088,
        'longitude' => 106.8456,
        'location_name' => 'Jakarta, Indonesia',
    ]);

    $item = UniqueItem::first();
    expect($item)->not->toBeNull();
    expect($item->tier)->toBe('SSS+');
    expect($item->points)->toBeGreaterThanOrEqual(1100);
    
    // Redirect to show page
    $response->assertRedirect("/items/{$item->uuid}");

    // Points are added to user
    $user->refresh();
    expect($user->points)->toBe($item->points);
});

test('uploading same image twice results in duplicate detection error', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create a fake image
    $file1 = UploadedFile::fake()->image('gold1.jpg', 50, 50);
    $img = imagecreatefromjpeg($file1->getRealPath());
    $color = imagecolorallocate($img, 255, 0, 0);
    imagefill($img, 0, 0, $color);
    imagejpeg($img, $file1->getRealPath());
    imagedestroy($img);

    $base64_1 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($file1->getRealPath()));

    // First upload
    $this->post('/items', [
        'title' => 'First Upload',
        'image_base64' => $base64_1,
    ])->assertRedirect();

    // Second upload (should trigger duplicate exception since hash is identical)
    $response = $this->post('/items', [
        'title' => 'Second Upload (Duplicate)',
        'image_base64' => $base64_1,
    ]);

    $response->assertSessionHasErrors(['image']);
    expect(UniqueItem::count())->toBe(1);
});

test('user can like and comment on items', function () {
    $user = User::factory()->create();
    $itemOwner = User::factory()->create();
    
    $item = UniqueItem::create([
        'user_id' => $itemOwner->id,
        'title' => 'Cool Crystal',
        'image_path' => 'items/test.jpg',
        'dominant_color' => '#ffffff',
        'tier' => 'A',
        'points' => 200,
        'image_hash' => '1234567890abcdef',
    ]);

    $this->actingAs($user);

    // 1. Toggle like
    $this->post("/items/{$item->uuid}/like")->assertRedirect();
    $this->assertDatabaseHas('likes', [
        'user_id' => $user->id,
        'unique_item_id' => $item->id
    ]);

    // 2. Add Comment
    $this->post("/items/{$item->uuid}/comment", [
        'content' => 'Wow, this is truly a unique finding!',
    ])->assertRedirect();

    $this->assertDatabaseHas('comments', [
        'user_id' => $user->id,
        'unique_item_id' => $item->id,
        'content' => 'Wow, this is truly a unique finding!'
    ]);
});

test('user can repost an item', function () {
    $user = User::factory()->create();
    $itemOwner = User::factory()->create();
    
    $item = UniqueItem::create([
        'user_id' => $itemOwner->id,
        'title' => 'Unique Leaf',
        'image_path' => 'items/leaf.jpg',
        'dominant_color' => '#00ff00',
        'tier' => 'B',
        'points' => 120,
        'image_hash' => 'abcdef1234567890',
    ]);

    $this->actingAs($user);

    $this->post("/items/{$item->uuid}/repost")->assertRedirect();
    $this->assertDatabaseHas('reposts', [
        'user_id' => $user->id,
        'unique_item_id' => $item->id
    ]);
});

test('public routes bind models using UUID instead of ID', function () {
    $user = User::factory()->create();
    
    $item = UniqueItem::create([
        'user_id' => $user->id,
        'title' => 'Ancient Ring',
        'image_path' => 'items/ring.jpg',
        'dominant_color' => '#ffd700',
        'tier' => 'SS',
        'points' => 800,
        'image_hash' => 'ffff1234567890ab',
    ]);

    // Access via UUID (success)
    $this->get("/items/{$item->uuid}")->assertStatus(200);
    $this->get("/profile/{$user->uuid}")->assertStatus(200);

    // Access via sequential integer ID (fails with 404)
    $this->get("/items/{$item->id}")->assertStatus(404);
    $this->get("/profile/{$user->id}")->assertStatus(404);
});

test('user can search globally for items and other users', function () {
    $user1 = User::factory()->create(['name' => 'Aditya Explorer']);
    $user2 = User::factory()->create(['name' => 'Budi Hunter']);
    
    UniqueItem::create([
        'user_id' => $user1->id,
        'title' => 'Batu Obsidian Langka',
        'image_path' => 'items/obsidian.jpg',
        'dominant_color' => '#111111',
        'tier' => 'A',
        'points' => 300,
        'image_hash' => 'aaabbbccc1112223',
    ]);

    // Search query matches user and item
    $response = $this->get('/search?q=Obsidian');
    $response->assertStatus(200);
    $response->assertSee('Batu Obsidian Langka');

    $response = $this->get('/search?q=Aditya');
    $response->assertStatus(200);
    $response->assertSee('Aditya Explorer');
});
