<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Facades\Cafe24ApiService;
use App\Models\Mall;
use App\Models\Mapp;
use App\Models\MappOption;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Category list
     * @param Request $request
     */
    public function index(Request $request)
    {
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();
        $result = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'categories', ['limit' => 100]);
        $ids = [];
        $cateResult = [];
        foreach ($result->categories as $key => $value) {
            array_push($ids, $value->category_no);
            array_push($cateResult, $value);
        }

        // add meta key - value to results
        $metaResult = Mapp::where('type', 'category')->whereIn('cafe24_id', $ids)->leftJoin('mapp_options', 'mapps.id', '=', 'mapp_options.map_id')->get();
        foreach ($cateResult as &$cate) {
            foreach ($metaResult as $meta) {
                if ($cate->category_no == $meta->cafe24_id) {
                    $cate->{$meta->meta_key} = $meta->meta_value;
                }
            }
        }

        $categories = buildTree($cateResult);

        return view('admin.categories.index', compact('categories', 'mall'));
    }

    /**
     * Category detail
     * @param int $id
     */
    public function show($id)
    {
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();
        $category = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'categories/' . $id);
        $category = $category->category;

        $categories = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'categories', ['limit' => 100]);
        $categories2 = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'categories', ['limit' => 100, 'offset' => 100]);
        $categories = array_merge($categories2->categories, $categories->categories);
        $categories = buildTree($categories);
        $options = buildOptions($categories, '=', [$category->parent_category_no]);

        // mapping meta
        $mappingMeta = $this->getMetaValue($id, 'category');

        return view('admin.categories.detail', compact('options', 'category', 'mappingMeta', 'mall'));
    }

    /**
     * Category create
     */
    public function create()
    {
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();

        $categories = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'categories', ['limit' => 100]);
        $categories2 = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'categories', ['limit' => 100, 'offset' => 100]);
        $categories = array_merge($categories2->categories, $categories->categories);
        $categories = buildTree($categories);
        $options = buildOptions($categories, '=');

        return view('admin.categories.create', compact('options', 'mall'));
    }

    /**
     * Category create
     * @param Request $request
     */
    public function store(Request $request)
    {
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();
        $data = [
            'shop_no' => 1,
            'request' => [
                'category_name' => $request->category_name,
                'use_display' => !empty($request->use_display) ? 'T' : 'F',
                'parent_category_no' => $request->parent_category_no,
            ],
        ];
        $result = Cafe24ApiService::post($mall->mall_id, $mall->access_token, 'categories', $data);
        Log::info('Created category: ' . json_encode($result));
        if (isset($result->error)) {
            return $result;
        }

        // create mapping in app
        $mapp = Mapp::create([
            'doosoun_id' => 0,
            'cafe24_id' => $result->category->category_no,
            'shop_no' => 1,
            'mall_id' => config('aos.mall_id'),
            'type' => 'category',
        ]);
        Log::info('Insert to mapping table app: ' . json_encode($mapp));

        MappOption::updateOrCreate([
            'map_id' => $mapp->id,
            'meta_key' => 'category_description',
        ], [
            'meta_value' => $request->category_description,
        ]);
        return redirect(route('category.index'));
    }

    /**
     * Category update
     * @param int $id
     */
    public function update($id, Request $request)
    {
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();
        $data = [
            'shop_no' => 1,
            'request' => [
                'category_name' => $request->category_name,
                'use_display' => !empty($request->use_display) ? 'T' : 'F',
            ],
        ];
        $result = Cafe24ApiService::put($mall->mall_id, $mall->access_token, 'categories/' . $id, $data);
        if (isset($result->error)) {
            return $result;
        }

        // update parent category
        if ($request->parent_category_no != $result->category->parent_category_no) {
            $data = [
                "requests" => [
                    [
                        "category_depth" => $request->category_depth + 1,
                        "parent_category_no" => $request->parent_category_no,
                        "category_no" => $id,
                        "display_order" => $result->category->display_order,
                    ]
                ]
            ];
            // Cafe24ApiService::put($mall->mall_id, $mall->access_token, 'categories/tree', $data);
        }
        Log::info('Updated category: ' . json_encode($result));

        // update description to app
        $mapp = Mapp::where(['type' => 'category', 'cafe24_id' => $id])->first();
        if (!empty($mapp)) {
            MappOption::updateOrCreate([
                'map_id' => $mapp->id,
                'meta_key' => 'category_description',
            ], [
                'meta_value' => $request->category_description,
            ]);
        }
        return redirect(route('category.index'));
    }

    /**
     * Delete a category
     * @param int $id
     */
    public function destroy($id)
    {
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();
        $result = Cafe24ApiService::delete($mall->mall_id, $mall->access_token, 'categories/' . $id);
        Log::info('Deleted category: ' . json_encode($result));
        return redirect(route('category.index'));
    }
}
