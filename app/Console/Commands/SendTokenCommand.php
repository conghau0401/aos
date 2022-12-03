<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Mall;
use App\Facades\Cafe24Service;

class SendTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'renewToken';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renew token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::channel('token')->info('Crontab start...');
        $malls = Mall::where('is_deleted', 0)->get()->keyBy('mall_id');
        foreach ($malls as $key => $mall) {
            $appSetting = [
                "refresh_token" => $mall->refresh_token,
                "client_id" => config('cafe24.client_id'),
                "client_secret" => config('cafe24.client_secret'),
                "mall_id" => $mall->mall_id,
            ];

            // get new token
            $cafe24NewToken = Cafe24Service::refreshToken($appSetting);
            Log::channel('token')->info('Crontab renew token mall '. $mall->mall_id . ': ' . json_encode($cafe24NewToken));

            // update mall data (access_token, refresh_token, refresh_token_expires_at)
            Cafe24Service::updateMallToken($cafe24NewToken['mall_id'], $cafe24NewToken['access_token'], $cafe24NewToken['refresh_token'], $cafe24NewToken['refresh_token_expires_at']);
        }
    }
}
