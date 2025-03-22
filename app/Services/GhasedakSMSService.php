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
    public function sendBulkSMS2(string $message,  string $receptors)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.ghasedaksms.com/v2/sms/send/bulk2",
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "message=$message&sender=10002000100924&receptor=$receptors",
            CURLOPT_HTTPHEADER => array(
                "apikey: ".$this->apiKey,
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
    }

    public function sendBulkSMS(string $message,  string $receptors)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.ghasedaksms.com/v2/sms/send/bulk",
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "message=$message&sender=10002000100924&receptor=$receptors",
            CURLOPT_HTTPHEADER => array(
                "apikey: ".$this->apiKey,
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
    }

    public function sendTemplateSms(int $type, string $param1, ?string $param2, ?string $param3 , string $receptor, string $template)
    {
        $postFields = "type=$type&receptor=$receptor&template=$template&param1=$param1";

        if (!empty($param2)) {
            $postFields .= "&param2=$param2";
        }

        if (!empty($param3)) {
            $postFields .= "&param3=$param3";
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
