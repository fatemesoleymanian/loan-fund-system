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

//    public function sendTemplateSms(int $type, string $param1, string $param2, string $param3 , string $receptor, string $template)
//    {
//        $curl = curl_init();
//        curl_setopt_array($curl,
//            array(
//                CURLOPT_URL => "http://api.ghasedaksms.com/v2/send/verify",
//                CURLOPT_RETURNTRANSFER => true,
//                CURLOPT_ENCODING => "",
//                CURLOPT_MAXREDIRS => 10,
//                CURLOPT_TIMEOUT => 30,
//                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                CURLOPT_CUSTOMREQUEST => "POST",
//                CURLOPT_POSTFIELDS => "type=1&receptor=09908285709&template=deposit&param1=90&param2=89&param3=32",
//                CURLOPT_HTTPHEADER => array(
//                    "apikey: ". $this->apiKey,
//                    "cache-control: no-cache",
//                    "content-type: application/x-www-form-urlencoded",
//                )));
//            $response = curl_exec($curl);
//            $err = curl_error($curl);
//            curl_close($curl);
//            if ($err) {
//                return "cURL Error #:" . $err;
//            } else {
//                return $response;
//            }
//    }
public function sendTemplateSms(int $type, string $param1, string $receptor, string $template, string $param2 = '', string $param3 = '')
{
    // Validate required parameters
    if (empty($param1) || empty($receptor) || empty($template)) {
        throw new InvalidArgumentException('param1, receptor, and template are required parameters.');
    }

    // Define the API URL
    $apiUrl = 'http://api.ghasedaksms.com/v2/send/verify';

    // Initialize cURL session
    $curl = curl_init();

    // Set cURL options
    curl_setopt_array($curl, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query([
            'type' => $type,
            'receptor' => $receptor,
            'template' => $template,
            'param1' => $param1,
            'param2' => $param2,
            'param3' => $param3,
        ]),
        CURLOPT_HTTPHEADER => [
            'apikey: ' . $this->apiKey,
            'Content-Type: application/x-www-form-urlencoded',
        ],
    ]);

    // Execute cURL request
    $response = curl_exec($curl);

    // Check for cURL errors
    if ($response === false) {
        $errorMsg = 'cURL Error: ' . curl_error($curl);
        curl_close($curl);
        throw new RuntimeException($errorMsg);
    }

    // Close cURL session
    curl_close($curl);

    // Decode JSON response
    $responseData = json_decode($response, true);

    // Handle JSON decoding errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new RuntimeException('Invalid JSON response: ' . json_last_error_msg());
    }

    // Check for API errors in the response
    if (isset($responseData['error'])) {
        // Handle specific API error codes as needed
        throw new RuntimeException('API Error: ' . $responseData['error']['message']);
    }

    // Return the successful response data
    return $responseData;
}

}
