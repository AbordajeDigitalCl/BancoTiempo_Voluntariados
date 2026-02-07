<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdvertisementController extends Controller
{
    public function index(Request $request)
    {
        $createdAds = session('created_ads', []);

        return Inertia::render('Advertisement', [
            'createdAds' => $createdAds,
        ]);
    }

    public function my(Request $request)
    {
        $createdAds = session('created_ads', []);

        return Inertia::render('MyAdvertisements', [
            'createdAds' => $createdAds,
        ]);
    }

    public function show(Request $request, $id)
    {
        $createdAds = session('created_ads', []);

        $ad = null;
        foreach ($createdAds as $item) {
            if ((string)($item['id'] ?? '') === (string)$id) {
                $ad = $item;
                break;
            }
        }

        if (!$ad) {
            abort(404);
        }

        return Inertia::render('AdvertisementShow', [
            'ad' => $ad,
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('CreateAdvertisement');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:2000'],
            'hours' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'in:Servicio,Producto'],
            'mode' => ['required', 'in:Oferta,Demanda'],
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'max:4096'],
        ]);

        $imageUrls = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('ads', 'public');
                $imageUrls[] = Storage::url($path);
            }
        }

        $createdAds = session('created_ads', []);

        $newAd = [
            'id' => now()->timestamp . rand(10, 99),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'hours' => (int)$validated['hours'],
            'type' => $validated['type'],
            'mode' => $validated['mode'],
            'status' => 'active',
            'author' => $request->user()->name,
            'imageUrl' => $imageUrls[0] ?? null,
            'images' => $imageUrls,
        ];

        $createdAds[] = $newAd;
        session(['created_ads' => $createdAds]);

        return redirect()->route('myadvertisements');
    }

    public function status(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:active,paused,finished'],
        ]);

        $createdAds = session('created_ads', []);
        $updated = [];

        foreach ($createdAds as $ad) {
            if ((string)($ad['id'] ?? '') === (string)$id) {
                if (($ad['author'] ?? null) === $request->user()->name) {
                    $ad['status'] = $request->input('status');
                }
            }
            $updated[] = $ad;
        }

        session(['created_ads' => $updated]);

        return back();
    }

    public function destroy(Request $request, $id)
    {
        $createdAds = session('created_ads', []);

        $createdAds = array_values(array_filter($createdAds, function ($ad) use ($id, $request) {

            if (($ad['author'] ?? null) !== $request->user()->name) return true;
            return (string)($ad['id'] ?? '') !== (string)$id;
        }));

        session(['created_ads' => $createdAds]);

        return back();
    }
}
