<?php

namespace App\Services\Api;

use App\Facades\Cafe24ApiService;
use App\Jobs\ProcessProductVariant;
use App\Models\MappOption;
use App\Models\Mapp;
use Illuminate\Support\Facades\Log;

class ProductService extends AppService
{
    /**
     * Save a product
     *
     * @param Request $request
     * @param integer $productNo // cafe24_id
     * @return void
     */
    public function saveProduct($request, $productNo = 0)
    {
        $params = $request->all();
        $params = $params['request'];

        // handle option
        $paramOptions = @$params['options'];
        $options = [];
        $optionsUpdated = [];
        if (is_array($paramOptions) && !empty($paramOptions)) {
            $listName = [];
            foreach ($paramOptions as $value) {
                array_push($listName, $value['name']);
                array_push($optionsUpdated, [
                    'option_text' => $value['name'],
                ]);
            }
            $options = [
                [
                    "name" => "Package",
                    "value" => $listName,
                ]
            ];
        } else {
            $paramOptions = [];
        }
        // get manufacturer code
        $manufacturerCode = @$params['manufacturer_code'];
        $manufacturers = Cafe24ApiService::get(config('aos.mall_id'), $request->bearerToken(), 'manufacturers', ['manufacturer_name' => $manufacturerCode]);
        if (empty($manufacturers->manufacturers) && !empty($manufacturerCode)) {
            $data = [
                "shop_no" => 1,
                "request" => [
                    "manufacturer_name" => $manufacturerCode,
                    "president_name" => "Sample User"
                ]
            ];
            $manufacturer = Cafe24ApiService::post(config('aos.mall_id'), $request->bearerToken(), 'manufacturers', $data);
            Log::channel('api')->info('API create manufacturer:' . jEncode($manufacturer));
            $manufacturerCode = $manufacturer->manufacturer->manufacturer_code;
        } else {
            $manufacturerCode = $manufacturers->manufacturers[0]->manufacturer_code;
        }

        // get supplier code
        $supplierCode = @$params['supplier_code'];
        $suppliers = Cafe24ApiService::get(config('aos.mall_id'), $request->bearerToken(), 'suppliers', ['supplier_name' => $supplierCode]);
        if (empty($suppliers->suppliers) && !empty($supplierCode)) {
            $data = [
                "shop_no" => 1,
                "request" => [
                    "supplier_name" => $supplierCode,
                    "use_supplier" => "T"
                ]
            ];
            $supplier = Cafe24ApiService::post(config('aos.mall_id'), $request->bearerToken(), 'suppliers', $data);
            Log::channel('api')->info('API create supplier:' . jEncode($supplier));
            $supplierCode = $supplier->supplier->supplier_code;
        } else {
            $supplierCode = $suppliers->suppliers[0]->supplier_code;
        }

        // prepare data
        $dataProduct = [
            'shop_no' => 1,
            'request' => [
                'custom_product_code' => @$params['custom_product_code'],
                'internal_product_name' => @$params['internal_product_name'],
                'product_material' => @$params['product_type'],
                'tax_type' => config('aos.tax.' . str()->snake(@$params['tax_type'])) ?? 'A',
                'tax_amount' => (int)@$params['tax_amount'],
                'maximum_quantity' => @$params['maximum_quantity'],
                'made_in_code' => @$params['made_in_code'],
                'release_date' => @$params['release_date'],
                'selling' => @$params['selling'],
                'display' => @$params['display'],
                'additional_image' => @$params['additional_image'],
                'description' => @$params['description'],
                'product_name' => @$params['product_name'],
                'supply_product_name' => @$params['supply_product_name'],
                'product_tag' => @$params['product_tag'],
                'summary_description' => @$params['summary_description'],
                'add_category_no' => @$params['add_category_no'],
                'supply_price' => @$params['supply_price'] == null ? 0 : $params['supply_price'],
                'has_option' => @$params['has_option'],
                'options' => $options,
                'price' => @$params['supply_price'] == null ? 0 : $params['supply_price'],
                'manufacturer_code' => $manufacturerCode,
                'supplier_code' => $supplierCode,
                'additional_information' => [
                    [
                        'key' => 'custom_option1',
                        'value' => @$params['marketable_size'],
                    ],
                    [
                        'key' => 'custom_option2',
                        'value' => @$params['return_info'],
                    ],
                ],
                'minimum_quantity' => 1,
            ],
        ];

        if (empty($paramOptions)) {
            unset($dataProduct['request']['options']);
        }

        // Create or updated product
        if ($productNo != 0) {
            unset($dataProduct['request']['has_option']);
            unset($dataProduct['request']['options']);
            $product = Cafe24ApiService::put(config('aos.mall_id'), $request->bearerToken(), 'products/' . $productNo, $dataProduct);
            if (isset($product->error)) {
                return $product;
            }
            Log::channel('api')->info("API updated product: " . jEncode($product));

            // delete product option
            $result = Cafe24ApiService::delete(config('aos.mall_id'), $request->bearerToken(), 'products/' . $productNo . '/options');
            Log::channel('api')->info("Delete old options product: " . jEncode($result));
            if (!empty($paramOptions)) {
                $dataOption = [
                    'shop_no' => 1,
                    'request' => [
                        'has_option' => 'T',
                        'option_type' => 'T',
                        'option_list_type' => 'S',
                        'options' => [
                            [
                                'option_name' => 'Package',
                                'option_value' => $optionsUpdated,
                            ],
                        ]
                    ]
                ];
                $result = Cafe24ApiService::post(config('aos.mall_id'), $request->bearerToken(), 'products/' . $productNo . '/options', $dataOption);
                Log::channel('api')->info("Create new options product: " . jEncode($result));
            }

            // update variants (add to queues)
            $params['product_no'] = $productNo;
            if (!empty($paramOptions)) {
                ProcessProductVariant::dispatch($params, $paramOptions);
            }
        } else {
            $product = Cafe24ApiService::post(config('aos.mall_id'), $request->bearerToken(), 'products', $dataProduct);
            Log::channel('api')->info("API Created product: " . jEncode($product));
            if (isset($product->error)) {
                return $product;
            }
            $productNo = $product->product->product_no;
            // create product in app
            $mapp = Mapp::create([
                'doosoun_id' => @$params['product_id'],
                'cafe24_id' => $product->product->product_no,
                'shop_no' => 1,
                'mall_id' => config('aos.mall_id'),
                'type' => 'product',
            ]);

            Log::channel('api')->info('Insert product table app: ' . jEncode($mapp));

            // Update variant
            $variants = Cafe24ApiService::get(config('aos.mall_id'), $request->bearerToken(), 'products/' . $product->product->product_no . '/variants');
            foreach ($variants->variants as $key => $variant) {
                if (!empty($paramOptions)) {
                    foreach ($paramOptions as $beeOption) {
                        if ($variant->options[0]->value == @$beeOption['name']) {
                            $dataOption = [
                                'shop_no' => 1,
                                'request' => [
                                    "custom_variant_code" => @$beeOption['value'][0]['barcode'],
                                    "additional_amount" => @$beeOption['value'][0]['supply_price'] - $params['supply_price'],
                                    "quantity" => @$beeOption['value'][0]['quantity'],
                                ],
                            ];
                            $resultOption = Cafe24ApiService::put(config('aos.mall_id'), $request->bearerToken(), 'products/' . $product->product->product_no . '/variants/' . $variant->variant_code, $dataOption);
                            Log::channel('api')->info('Put Options: ' . jEncode($resultOption));
                        }
                    }
                }
            }
        }

        // update or create meta
        $mapp = Mapp::where(['type' => 'product', 'cafe24_id' => $productNo])->first();
        if (!empty($mapp)) {
            if (!empty(@$params['options'])) {
                MappOption::updateOrCreate([
                    'map_id' => $mapp->id,
                    'meta_key' => 'option',
                ], [
                    'meta_value' => jEncode(@$params['options']),
                ]);
            } else {
                MappOption::where([
                    'map_id' => $mapp->id,
                    'meta_key' => 'option',
                ])->delete();
            }
            if (!empty(@$params['marketable_size'])) {
                MappOption::updateOrCreate([
                    'map_id' => $mapp->id,
                    'meta_key' => 'marketable_size',
                ], [
                    'meta_value' => @$params['marketable_size'],
                ]);
            } else {
                MappOption::where([
                    'map_id' => $mapp->id,
                    'meta_key' => 'marketable_size',
                ])->delete();
            }
            Log::channel('api')->info('Update product table app: ');
        }

        // Upload product image
        if (!empty(@$params['detail_image'])) {
            $b64image = base64_encode(@file_get_contents(@$params['detail_image']));
            $dataImage = [
                "shop_no" => 1,
                "request" => [
                    "image_upload_type" => "A",
                    "detail_image" => $b64image,
                ]
            ];
            $image = Cafe24ApiService::post(config('aos.mall_id'), $request->bearerToken(), 'products/' . $product->product->product_no . '/images', $dataImage);
            Log::channel('api')->info("API Upload image: " . jEncode($image));
        }

        // response data
        $productResponse = $params;
        $productResponse['product_no'] = $product->product->product_no;
        return $productResponse;
    }
}
