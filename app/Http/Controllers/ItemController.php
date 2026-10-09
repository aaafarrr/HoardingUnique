<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Requests\UploadItemRequest;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Repost;
use App\Models\UniqueItem;
use App\Services\ItemProcessingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function __construct(
        protected ItemProcessingService $itemService
    ) {}

    /**
     * Display a listing of unique items.
     */
    public function index(Request $request)
    {
        $query = UniqueItem::with(['user', 'likes', 'comments', 'reposts'])
            ->withCount(['likes', 'comments', 'reposts']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tier')) {
            $query->where('tier', $request->input('tier'));
        }

        $items = $query->latest()->paginate(12)->withQueryString();

        return view('items.index', compact('items'));
    }

    /**
     * Show the form for creating a new unique item.
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created unique item in storage.
     */
    public function store(UploadItemRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = Auth::user();

        // 1. Process base64 camera image
        $base64Data = $request->input('image_base64');
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $data = substr($base64Data, strpos($base64Data, ',') + 1);
            $type = strtolower($type[1]);

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                throw new \InvalidArgumentException('Format gambar tidak valid.');
            }

            $data = base64_decode($data);
            if ($data === false) {
                throw new \InvalidArgumentException('Gagal mendecode data base64 gambar.');
            }
        } else {
            throw new \InvalidArgumentException('Format Data URI gambar tidak valid.');
        }

        // Save data to a temporary file for GD color and hash processing
        $tempPath = tempnam(sys_get_temp_dir(), 'capture_') . '.' . $type;
        file_put_contents($tempPath, $data);

        try {
            // 2. Perform Image Hashing & Deduplication check
            $hash = $this->itemService->getAverageHash($tempPath);
            $this->itemService->checkDeduplication($hash);

            // 3. Extract Dominant Color & Calculate Tier/Points
            $rgb = $this->itemService->getDominantColor($tempPath);
            $hexColor = $this->itemService->rgbToHex($rgb[0], $rgb[1], $rgb[2]);
            $hsl = $this->itemService->rgbToHsl($rgb[0], $rgb[1], $rgb[2]);
            $tierAndPoints = $this->itemService->determineTierAndPoints($hsl);

            // 4. Store image file persistently in storage/app/public/items
            $imagePath = 'items/' . Str::random(40) . '.' . $type;
            Storage::disk('public')->put($imagePath, $data);
        } finally {
            @unlink($tempPath);
        }

        // 5. DB Transaction to insert item and update user points
        $item = DB::transaction(function () use ($validated, $user, $imagePath, $hexColor, $tierAndPoints, $hash) {
            $createdItem = UniqueItem::create([
                'user_id' => $user->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'image_path' => $imagePath,
                'dominant_color' => $hexColor,
                'tier' => $tierAndPoints['tier'],
                'points' => $tierAndPoints['points'],
                'image_hash' => $hash,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'location_name' => $validated['location_name'] ?? null,
            ]);

            // Add points to user's total
            $user->increment('points', $tierAndPoints['points']);

            return $createdItem;
        });

        return redirect()->route('items.show', $item)->with('success', sprintf(
            'Barang unik berhasil diunggah! Tier: %s (+%d Poin)',
            $item->tier,
            $item->points
        ));
    }

    /**
     * Display the specified unique item.
     */
    public function show(UniqueItem $item)
    {
        $item->load(['user', 'comments.user', 'likes', 'reposts'])
            ->loadCount(['likes', 'comments', 'reposts']);

        $isLiked = Auth::check() && $item->likes->contains('user_id', Auth::id());
        $isReposted = Auth::check() && $item->reposts->contains('user_id', Auth::id());

        return view('items.show', compact('item', 'isLiked', 'isReposted'));
    }

    /**
     * Toggle like/unlike on a unique item.
     */
    public function toggleLike(UniqueItem $item): RedirectResponse
    {
        $user = Auth::user();
        $like = Like::where('user_id', $user->id)
            ->where('unique_item_id', $item->id)
            ->first();

        if ($like) {
            $like->delete();
            $message = 'Menyukai barang unik dibatalkan.';
        } else {
            Like::create([
                'user_id' => $user->id,
                'unique_item_id' => $item->id,
            ]);
            $message = 'Anda menyukai barang unik ini!';
        }

        return back()->with('success', $message);
    }

    /**
     * Toggle repost/un-repost on a unique item.
     */
    public function toggleRepost(UniqueItem $item): RedirectResponse
    {
        $user = Auth::user();
        $repost = Repost::where('user_id', $user->id)
            ->where('unique_item_id', $item->id)
            ->first();

        if ($repost) {
            $repost->delete();
            $message = 'Repost dibatalkan.';
        } else {
            Repost::create([
                'user_id' => $user->id,
                'unique_item_id' => $item->id,
            ]);
            $message = 'Barang unik berhasil direpost ke profil Anda!';
        }

        return back()->with('success', $message);
    }

    /**
     * Store a comment on a unique item.
     */
    public function storeComment(CommentRequest $request, UniqueItem $item): RedirectResponse
    {
        Comment::create([
            'user_id' => Auth::id(),
            'unique_item_id' => $item->id,
            'content' => $request->input('content'),
        ]);

        return back()->with('success', 'Komentar Anda berhasil ditambahkan!');
    }
}
