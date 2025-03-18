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

    public function sendTemplateSms(int $type, string $param1, string $param2, string $param3 , string $receptor, string $template): array
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'cache-control' => 'no-cache',
                'content-type' => 'application/x-www-form-urlencoded',
            ])->post('http://api.ghasedaksms.com/v2/send/verify', [
                'type' => $type,
                'param1' => $param1,
                'param2' => $param2,
                'param3' => $param3,
                'receptor' => $receptor,
                'template' => $template,
            ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                throw new Exception('Ghasedak API error: ' . $response->body());
            }
        } catch (Exception $e) {
            // Log the error or handle it as needed
            logger()->error('Ghasedak template SMS failed: ' . $e->getMessage());
            throw $e;
        }
    }

}
