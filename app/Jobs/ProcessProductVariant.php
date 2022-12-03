<?php

namespace App\Jobs;

use App\Models\Mapp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Facades\Cafe24ApiService;
use App\Models\Mall;

class ProcessProductVariant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $params;
    protected $options;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params, $options)
    {
        $this->params = $params;
        $this->options = $options;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sleep(20);
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();
        // Update variant
        $variants = Cafe24ApiService::get(config('aos.mall_id'), $mall->access_token, 'products/' . $this->params['product_no'] . '/variants');
        Log::channel('queue')->info('queue start...' . $this->params['product_no']);
        foreach ($variants->variants as $key => $variant) {
            foreach ($this->options as $beeOption) {
                if ($variant->options[0]->value == @$beeOption['name']) {
                    $dataOption = [
                        'shop_no' => 1,
                        'request' => [
                            "custom_variant_code" => @$beeOption['value'][0]['barcode'],
                            "additional_amount" => @$beeOption['value'][0]['supply_price'] - $this->params['supply_price'],
                            "quantity" => @$beeOption['value'][0]['quantity'],
                        ],
                    ];
                    $resultOption = Cafe24ApiService::put(config('aos.mall_id'), $mall->access_token, 'products/' . $this->params['product_no'] . '/variants/' . $variant->variant_code, $dataOption);
                    Log::channel('queue')->info('Queues Put Options: ' . jEncode($resultOption));
                }
            }
        }
    }
}
