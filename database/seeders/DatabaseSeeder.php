<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UniqueItem;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Repost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a dummy image in storage
        if (!Storage::disk('public')->exists('items/dummy.jpg')) {
            Storage::disk('public')->makeDirectory('items');
            $img = imagecreatetruecolor(400, 400);
            $color = imagecolorallocate($img, 99, 102, 241); // indigo-500
            imagefill($img, 0, 0, $color);
            ob_start();
            imagejpeg($img, null, 90);
            $data = ob_get_clean();
            Storage::disk('public')->put('items/dummy.jpg', $data);
            imagedestroy($img);
        }

        // Wrap database writes in a transaction to make it extremely fast in SQLite/MySQL
        DB::transaction(function () {
            // 2. Create the default test user
            $testUser = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'bio' => 'Seorang petualang sejati yang mencari barang terunik di muka bumi.',
                'points' => 0,
            ]);

            // 3. Create 25 other randomized users
            $users = User::factory(25)->create([
                'password' => Hash::make('password'),
            ])->concat([$testUser]);

            // Define a list of unique objects for item titles
            $uniqueObjects = [
                'Cangkir Antik Dinasti Ming', 'Batu Fosil Daun Purba', 'Keris Pusaka Singosari', 'Koin Emas VOC', 
                'Kamera Roll Jadul', 'Buku Catatan Kuno', 'Kacamata Retak Klasik', 'Kerang Spiral Raksasa', 
                'Meteorit Hitam Berkilau', 'Fosil Gigi Hiu Megalodon', 'Kompas Navigasi Kapal Submarine', 
                'Piring Keramik Lukis Tangan', 'Teropong Bintang Vintage', 'Kunci Gembok Benteng Kuno', 
                'Botol Ramuan Kaca Hijau', 'Topeng Kayu Suku Pedalaman', 'Patung Perunggu Mini', 
                'Cincin Perak Bermata Kecubung', 'Pedang Karatan Abad Ke-15', 'Peta Harta Karun Lusuh', 
                'Kalung Manik-Manik Etnik', 'Mainan Timah Mekanik', 'Jam Saku Emas Berukir', 
                'Lonceng Kuil Kuningan', 'Alat Tulis Bulu Angsa', 'Cap Stempel Kerajaan', 
                'Kotak Musik Kayu Cendana', 'Guci Keramik Retak Seribu', 'Lampu Minyak Tembaga', 
                'Biola Tua Berwarna Gelap', 'Trompet Kuningan Penyok', 'Pahat Batu Prasejarah', 
                'Gading Gajah Ukir Kuno', 'Kereta Kuda Kayu Pajangan', 'Set Catur Marmer', 
                'Kompas Saku Pramuka Tua', 'Terompet Tanduk Hewan', 'Lukisan Miniatur Kanvas', 
                'Koin Perak Romawi', 'Sendok Perak Ukir Naga', 'Gelas Kaca Kristal Tebal', 
                'Pena Bulu Tinta Hitam', 'Dompet Kulit Buaya Tua', 'Sabuk Logam Bertatahkan Batu', 
                'Sepatu Bot Kulit Kusam', 'Gantungan Kunci Tulang', 'Kerang Mutiara Raksasa', 
                'Bunga Kering Dalam Kaca', 'Daun Emas Pembatas Buku', 'Cermin Kuningan Kusam', 
                'Toples Kaca Kedap Udara', 'Kancing Baju Tulang Kerbau', 'Sisir Tanduk Sapi', 
                'Pipa Rokok Kayu Langka', 'Asbak Batu Gunung Api', 'Gelang Serat Kayu Purba', 
                'Bandul Kalung Fosil Kayu', 'Miniatur Candi Batu', 'Wayang Kulit Antik', 
                'Kerajinan Logam Tempaan', 'Sumbu Lentera Kuno', 'Timbangan Obat Kuningan', 
                'Gelas Takar Tembaga', 'Sendok Kayu Ulin Tua', 'Uang Kertas Kuno', 
                'Perangko Pertama RI', 'Kartu Pos Klasik', 'Piring Seng Motif Kembang', 
                'Teko Seng Biru Lurik', 'Mangkok Jago Legendaris', 'Caping Bambu Petani', 
                'Keranjang Anyaman Rotan', 'Tikar Pandan Tenun Tangan', 'Kendi Tanah Liat Klasik', 
                'Cobek Batu Kali Asli', 'Lesung Kayu Penumbuk Padi', 'Kipas Cendana Harum', 
                'Payung Kertas Lukis', 'Kelom Geulis Kayu', 'Batik Tulis Motif Langka', 
                'Keris Kecil Jimat', 'Tombak Pusaka Karatan', 'Tameng Kayu Motif Suku', 
                'Suling Bambu Tradisional', 'Kendang Kulit Kerbau', 'Gong Perunggu Kecil', 
                'Angklung Bambu Hitam', 'Kecapi Kayu Berukir', 'Rebab Gesek Kuno', 
                'Gamelan Mini Kuningan', 'Topeng Barong Bali', 'Patung Garuda Kayu', 
                'Ukiran Jepara Klasik', 'Naskah Lontar Bali', 'Kropak Penyimpan Lontar', 
                'Wadah Bedak Kuningan', 'Korek Api Kayu Jadul', 'Setrika Arang Ayam Jago', 
                'Lampu Petromaks Tembaga', 'Radio Tabung Kayu Kuno', 'Gramofon Piringan Hitam', 
                'Telepon Putar Klasik', 'Mesin Ketik Manual Besi', 'Kamera Polaroid Vintage', 
                'Jam Dinding Kayu Kuno', 'Kacamata Bulat Klasik', 'Pena Tinta Emas', 
                'Tempat Tinta Batu', 'Buku Harian Kulit Kuno', 'Peta Dunia Gulungan', 
                'Globe Bola Dunia Vintage', 'Kompas Bidik Logam', 'Kaca Pembesar Gagang Tanduk', 
                'Pisau Lipat Multi Guna', 'Korek Api Minyak Zippo', 'Dompet Koin Rajutan', 
                'Sabuk Kulit Gesper Kuningan', 'Tas Kulit Kurir Sepeda', 'Klip Kertas Raksasa', 
                'Stempel Lilin Segel', 'Lilin Segel Merah', 'Pita Mesin Ketik Tua', 
                'Kertas Buram Kuno', 'Amplop Cokelat Bersegel', 'Perangko Luar Negeri Kuno', 
                'Uang Logam Bolong Tengah', 'Koin Emas Spanyol', 'Cincin Emas Bermata Merah', 
                'Kalung Perak Liontin Fosil', 'Gelang Perunggu Tebal', 'Anting Emas Kuno', 
                'Mahkota Logam Tiruan', 'Bros Dada Manik-Manik', 'Jepit Rambut Perak', 
                'Tusuk Konde Kuningan', 'Kancing Manset Vintage', 'Jepitan Dasi Emas', 
                'Rantai Jam Saku', 'Kotak Perhiasan Beludru', 'Kunci Pintu Besi Tempa', 
                'Engsel Pintu Kastil', 'Gagang Pintu Perunggu', 'Lonceng Pintu Besi', 
                'Ketukan Pintu Bentuk Singa', 'Paku Besi Kuno Raksasa', 'Rantai Jangkar Karatan', 
                'Katrol Kayu Kapal Layar', 'Kemudi Ship Kayu Raksasa', 'Pelampung Gabus Klasik', 
                'Dayung Kayu Ukir', 'Jaring Ikan Tali Serat', 'Tempat Ikan Anyaman', 
                'Kail Pancing Besi Tempa', 'Lentera Kapal Kuningan', 'Peta Navigasi Laut Kuno', 
                'Buku Log Kapal Kapten', 'Teropong Kapal Tembaga', 'Kompas Kemudi Perunggu', 
                'Jam Pasir Kayu Klasik', 'Kronometer Kapal Vintage', 'Sekstan Navigasi Bintang', 
                'Buku Astronomi Kuno', 'Peta Rasi Bintang Malam', 'Astrolab Perunggu Klasik', 
                'Bola Langit Logam', 'Teropong Bintang Kuningan', 'Cermin Pemantul Teleskop', 
                'Lensa Kaca Tebal', 'Tripod Kayu Teropong', 'Kotak Kayu Penyimpan Lensa', 
                'Buku Catatan Astronom', 'Peta Bulan Gambar Tangan', 'Fosil Tulang Dinosaurus', 
                'Fosil Daun Pakis Purba', 'Fosil Ikan Dalam Batu', 'Fosil Serangga Dalam Amber', 
                'Fosil Cangkang Kerang', 'Fosil Kayu Menjadi Batu', 'Batu Akik Gambar Unik', 
                'Batu Giok Hijau Ukir', 'Batu Kecubung Ungu Kristal', 'Batu Merah Delima Asli', 
                'Batu Safir Biru Alami', 'Batu Zamrud Khatulistiwa', 'Batu Opal Kalimaya', 
                'Batu Pirus Persia Kuno', 'Batu Obsidian Hitam Legam', 'Batu Belerang Kuning Alami', 
                'Batu Apung Light', 'Batu Kuarsa Putih Kristal', 'Batu Badar Besi Magnet', 
                'Batu Meteorit Kasar', 'Butiran Pasir Emas', 'Serpihan Logam Perak', 
                'Tembaga Batangan Kasar', 'Timah Solder Kuno', 'Kuningan Lembaran Tua', 
                'Perunggu Batangan Klasik', 'Besi Tempa Kasar', 'Baja Damaskus Lipat'
            ];

            $tiers = [
                'SSS+' => [1100, 1500],
                'SSS' => [900, 1099],
                'SS+' => [750, 899],
                'SS' => [600, 749],
                'S+' => [480, 599],
                'S' => [380, 479],
                'A+' => [280, 379],
                'A' => [200, 279],
                'B+' => [150, 199],
                'B' => [100, 149],
                'C+' => [80, 99],
                'C' => [60, 79],
                'D+' => [40, 59],
                'D' => [10, 39]
            ];

            // 4. Create 500 unique items distributed globally
            $items = [];
            for ($i = 0; $i < 500; $i++) {
                $randomUser = $users->random();
                $tierKey = array_rand($tiers);
                [$minPts, $maxPts] = $tiers[$tierKey];
                $pts = rand($minPts, $maxPts);

                // Determine coordinates region
                // 220 items in Indonesia (to ensure rich local testing)
                // 80 items densely clustered in Paris, France (densest fallback cluster)
                // 100 items in the USA
                // 100 items in Europe/Asia/other parts
                if ($i < 220) {
                    // Indonesia: Java/Bali region
                    $lat = -6.0 - (rand(0, 250) / 100); // -6.0 to -8.5
                    $lng = 106.0 + (rand(0, 950) / 100); // 106.0 to 115.5
                    $cities = ['Jakarta', 'Bandung', 'Yogyakarta', 'Surabaya', 'Denpasar, Bali', 'Semarang', 'Malang'];
                    $locName = $cities[array_rand($cities)] . ', Indonesia';
                } elseif ($i < 300) {
                    // Dense cluster in Paris, France (approx 48.8566, 2.3522)
                    $lat = 48.8566 + (rand(-50, 50) / 1000); // within ~5 km of Paris
                    $lng = 2.3522 + (rand(-50, 50) / 1000);
                    $locName = 'Paris, France';
                } elseif ($i < 400) {
                    // USA
                    $lat = 34.0 + (rand(0, 800) / 100); // 34.0 to 42.0
                    $lng = -118.0 + (rand(0, 4400) / 100); // -118.0 to -74.0
                    $cities = ['New York', 'Los Angeles', 'Chicago', 'San Francisco', 'Seattle', 'Boston', 'Miami'];
                    $locName = $cities[array_rand($cities)] . ', USA';
                } else {
                    // Rest of the World (London, Tokyo, Sydney, Cairo, etc.)
                    $destinations = [
                        ['lat' => 51.5074, 'lng' => -0.1278, 'name' => 'London, UK'],
                        ['lat' => 35.6762, 'lng' => 139.6503, 'name' => 'Tokyo, Japan'],
                        ['lat' => -33.8688, 'lng' => 151.2093, 'name' => 'Sydney, Australia'],
                        ['lat' => 30.0444, 'lng' => 31.2357, 'name' => 'Cairo, Egypt'],
                        ['lat' => 1.3521, 'lng' => 103.8198, 'name' => 'Singapore'],
                    ];
                    $dest = $destinations[array_rand($destinations)];
                    $lat = $dest['lat'] + (rand(-50, 50) / 1000);
                    $lng = $dest['lng'] + (rand(-50, 50) / 1000);
                    $locName = $dest['name'];
                }

                // Assign title and description
                $title = $uniqueObjects[$i % count($uniqueObjects)];
                if ($i >= count($uniqueObjects)) {
                    $title .= ' Ke-' . (floor($i / count($uniqueObjects)) + 1);
                }

                $items[] = UniqueItem::create([
                    'user_id' => $randomUser->id,
                    'title' => $title,
                    'description' => 'Ini adalah deskripsi barang unik ' . strtolower($title) . ' yang ditemukan secara tidak sengaja di daerah setempat.',
                    'image_path' => 'items/dummy.jpg',
                    'dominant_color' => '#' . str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
                    'tier' => $tierKey,
                    'points' => $pts,
                    'image_hash' => Str::random(16),
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'location_name' => $locName,
                    'created_at' => now()->subMinutes(rand(1, 10000)),
                ]);
            }

            // 5. Create random social interactions (Likes, Comments, Reposts)
            $commentsData = [
                'Luar biasa penemuannya!', 'Sangat langka dan unik sekali barang ini.', 'Saya belum pernah melihat yang seperti ini sebelumnya.',
                'Wow, ini bernilai tinggi di pasar kolektor!', 'Penemuan yang mengubah sejarah!', 'Di mana Anda menemukan barang ini?',
                'Apakah ini dijual? Saya tertarik membelinya.', 'Hebat sekali petualangannya!', 'Terima kasih telah berbagi temuan indah ini.',
                'Ini benar-benar luar biasa.', 'Salut untuk penjelajahannya!', 'Menarik sekali sejarah di balik barang ini.'
            ];

            foreach ($users as $user) {
                // Like 30 to 70 random items
                $likeItems = collect($items)->random(rand(30, 70));
                foreach ($likeItems as $item) {
                    Like::create([
                        'user_id' => $user->id,
                        'unique_item_id' => $item->id,
                    ]);
                }

                // Comment on 10 to 20 random items
                $commentItems = collect($items)->random(rand(10, 20));
                foreach ($commentItems as $item) {
                    Comment::create([
                        'user_id' => $user->id,
                        'unique_item_id' => $item->id,
                        'content' => $commentsData[array_rand($commentsData)],
                    ]);
                }

                // Repost 5 to 12 random items
                $repostItems = collect($items)->random(rand(5, 12));
                foreach ($repostItems as $item) {
                    Repost::create([
                        'user_id' => $user->id,
                        'unique_item_id' => $item->id,
                    ]);
                }
            }

            // 6. Calculate total points for each user based on their findings
            foreach ($users as $user) {
                $userPoints = UniqueItem::where('user_id', $user->id)->sum('points');
                $user->update([
                    'points' => $userPoints,
                    'bio' => 'Petualang barang unik aktif. Telah mempublikasikan banyak barang terlangka.',
                    'login_count' => rand(5, 100),
                    'last_login_at' => now()->subHours(rand(0, 168)),
                ]);
            }
        });
    }
}
