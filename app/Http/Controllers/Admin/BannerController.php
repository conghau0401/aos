<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Facades\Cafe24ApiService;
use App\Models\Banner;
use App\Models\BannerImage;
use Illuminate\Support\Facades\Log;

class BannerController extends Controller
{
    /**
     * Banner list
     * @param Request $request
     */
    public function index(Request $request)
    {
        $banners = Banner::paginate(20);
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Banner create
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Banner save
     * @param Request $request
     */
    public function store(Request $request)
    {
        $banner = Banner::create([
            'name' => $request->name,
        ]);
        if (is_array($request->images)) {
            foreach ($request->images as $key => $image) {
                BannerImage::create([
                    'banner_id' => $banner->id,
                    'image' => $image,
                    'name' => $request->name_image[$key] ?? '',
                    'url' => $request->url[$key] ?? '',
                ]);
            }
        }
        Log::info('Created banner');
        return redirect(route('banners.index'));
    }

    /**
     * Banner detail
     * @param int $id
     */
    public function show($id)
    {
        $bannerImages = BannerImage::where('banner_id', $id)->get();
        $banner = Banner::where('id', $id)->first();
        return view('admin.banners.detail', compact('banner', 'bannerImages'));
    }

    /**
     * Banner update
     * @param int $id
     */
    public function update($id, Request $request)
    {
        Banner::where('id', $id)->update([
            'name' => $request->name,
        ]);
        // create new image
        if (is_array($request->images)) {
            foreach ($request->images as $key => $image) {
                BannerImage::create([
                    'banner_id' => $id,
                    'image' => $image,
                    'name' => $request->name_image[$key] ?? '',
                    'url' => $request->url[$key] ?? '',
                ]);
            }
        }
        // update name, url old image
        if (is_array($request->old_ids)) {
            foreach ($request->old_ids as $key => $idImage) {
                BannerImage::where('id', $idImage)->update([
                    'name' => $request->old_name_image[$key] ?? '',
                    'url' => $request->old_url[$key] ?? '',
                ]);
            }
        }
        return redirect(route('banners.index'));
    }

    /**
     * Delete a banner
     * @param int $id
     */
    public function destroy($id)
    {
        Banner::where('id', $id)->delete();
        BannerImage::where('banner_id', $id)->delete();
        return redirect(route('banners.index'));
    }
}
