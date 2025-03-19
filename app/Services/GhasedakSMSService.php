<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class GhasedakSMSService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.ghasedak.api_key');
    }
    public function sendSMS(string $message, string $sender, string $receptor):array
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->post('http://api.ghasedaksms.com/v2/sms/send/simple',[
                'message' => $message,
                'sender' => $sender,
                'Receptor' => $receptor
            ]);
            if ($response->successful()){
                return $response->json();
            } else {
                throw new Exception('Ghasedak API error: ' . $response->body());
            }
        }catch (Exception $exception){
            logger()->error('Ghasedak bulk sms failed: '. $exception->getMessage());
            throw  $exception;
        }
    }
    public function sendBulkSMS(string $message, string $sender, array $receptors, int $checkMessageIds):array
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->post('http://api.ghasedaksms.com/v2/sms/send/bulk',[
                'message' => $message,
                'sender' => $sender,
                'Receptor' => implode(',', $receptors),
                'checkmessageids' => $checkMessageIds
            ]);
            if ($response->successful()){
                return $response->json();
            } else {
                throw new Exception('Ghasedak API error: ' . $response->body());
            }
        }catch (Exception $exception){
            logger()->error('Ghasedak bulk sms failed: '. $exception->getMessage());
            throw  $exception;
        }
    }

    public function sendTemplateSms(int $type, string $param1, ?string $param2, ?string $param3 , string $receptor, string $template)
    {
        $postFields = "type=$type&receptor=$receptor&template=$template&param1=" . urlencode($param1);

        if (!empty($param2)) {
            $postFields .= "&param2=" . urlencode($param2);
        }

        if (!empty($param3)) {
            $postFields .= "&param3=" . urlencode($param3);
        }

        $curl = curl_init();
        curl_setopt_array($curl,
            array(
                CURLOPT_URL => "http://api.ghasedaksms.com/v2/send/verify",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $postFields,
                CURLOPT_HTTPHEADER => array(
                    "apikey: $this->apiKey",
//                    "cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded",
                )));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return "cURL Error #:" . $err;
            } else {
                return $response;
            }
    }

}
