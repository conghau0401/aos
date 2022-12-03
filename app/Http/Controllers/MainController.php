<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MainController extends Controller
{
    /**
     * show popup address
     *
     * @return void
     */
    public function popup()
    {
        return view('juso_popup_utf8');
    }

    /**
     * Get payment result function
     *
     * @param Request $request
     * @return void
     */
    public function paymentResult(Request $request)
    {
        $authResultCode = $request->AuthResultCode;        // authentication result code 0000:success
        $authResultMsg = $request->AuthResultMsg;        // authentication result message
        $nextAppURL = $request->NextAppURL;                // authorization request URL
        $txTid = $request->TxTid;                        // transaction ID
        $authToken = $request->AuthToken;                // authentication TOKEN
        $payMethod = $request->PayMethod;                // payment method
        $mid = $request->MID;                            // merchant id
        $moid = $request->Moid;                            // order number
        $amt = $request->Amt;                            // Amount of payment
        $reqReserved = $request->ReqReserved;            // mall custom field
        $netCancelURL = $request->NetCancelURL;            // netCancelURL

        if ($authResultCode === "0000") {
            /*
            ****************************************************************************************
            * <Hash encryption> (do not modify)
            ****************************************************************************************
            */
            $ediDate = date("YmdHis");
            $merchantKey = config('aos.payment.merchant_key'); // 상점키
            $signData = bin2hex(hash('sha256', $authToken . $mid . $amt . $ediDate . $merchantKey, true));

            try {
                // create order
                $customFields = json_decode(rawurldecode($reqReserved), true);
                Log::channel('payment')->info('Custom fields: ' . jEncode($customFields) . ' Request data:' . jEncode($request->all()));
                $response = Http::withHeaders(['Authorization' => 'Bearer ' . $customFields['token']])->post(env('APP_URL') . 'api/order/create', $customFields);
                $resultOrder = json_decode($response->getBody()->getContents());
                Log::channel('payment')->info('Result create order: ' . jEncode($resultOrder));
                // throw exception if error
                if (!isset($resultOrder->success) || $resultOrder->success == false) {
                    throw new \Exception("Can't create order!");
                }

                $data = array(
                    'TID' => $txTid,
                    'AuthToken' => $authToken,
                    'MID' => $mid,
                    'Amt' => $amt,
                    'EdiDate' => $ediDate,
                    'SignData' => $signData,
                    'CharSet' => 'utf-8'
                );
                /*
                ****************************************************************************************
                * <authorization request>
                * authorization through server to server communication.
                ****************************************************************************************
                */
                $response = $this->requestPost($data, $nextAppURL);
                Log::channel('payment')->info('Payment result: ' . jEncode($response));
                $params = [
                    'ResultCode' => @$response['ResultCode'],
                    'ResultMsg' => @$response['ResultMsg'],
                    'ResultMsg' => @$response['ResultMsg'],
                    'BuyerName' => @$response['BuyerName'],
                    'BuyerEmail' => @$response['BuyerEmail'],
                    'BuyerTel' => @$response['BuyerTel'],
                    'PayMethod' => @$response['PayMethod'],
                    'VbankNum' => @$response['VbankNum'],
                    'VbankBankCode' => @$response['VbankBankCode'],
                    'VbankBankName' => @$response['VbankBankName'],
                    'VbankExpDate' => @$response['VbankExpDate'],
                    'BankCode' => @$response['BankCode'],
                    'BankName' => @$response['BankName'],
                    'orderId' => $resultOrder->data->order_numbers,
                    'createdDate' => $resultOrder->data->created_date,
                ];
                return redirect('/order-completed?' . http_build_query($params));
            } catch (\Exception $e) {
                $e->getMessage();
                $data = array(
                    'TID' => $txTid,
                    'AuthToken' => $authToken,
                    'MID' => $mid,
                    'Amt' => $amt,
                    'EdiDate' => $ediDate,
                    'SignData' => $signData,
                    'NetCancel' => '1',
                    'CharSet' => 'utf-8'
                );
                /*
                *************************************************************************************
                * <NET CANCEL>
                * If an exception occurs during communication, cancelation is recommended
                *************************************************************************************
                */
                $response = $this->requestPost($data, $netCancelURL);
                Log::channel('payment')->error('Payment error: ' . jEncode($response));
                return redirect('/');
            }
        } else {
            //When authentication fail
            $ResultCode = $authResultCode;
            $ResultMsg = $authResultMsg;
            Log::channel('payment')->error('Authentication fail: ' . $ResultCode . ' ResultMsg: ' . $ResultMsg);
        }
    }

    /**
     * Post api call function
     *
     * @param array $data
     * @param string $url
     * @return object
     */
    private function requestPost(array $data, $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); //connection timeout 15
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //POST data
        curl_setopt($ch, CURLOPT_POST, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
