<?php

namespace App\Http\Controllers;

use App\Services\GhasedakSMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SMSController extends Controller
{
    protected $smsService;

    public function __construct()
    {
        $this->smsService = new GhasedakSMSService();
    }

    public function sendSms(Request $request)
    {
        $message = $request->input('message');
        $sender = $request->input('sender');
        $receptor = $request->input('receptor');

        try {
            $result = $this->smsService->sendSms($message, $sender, $receptor);
            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    public function sendBulkSmsWithCustomizeText(Request $request){
        $data = [
            'message' => $request->message,
            'receptors' => $request->receptors
        ];

       return $this->sendBulkSms($data,2);
    }
    public function sendBulkSms($data,$bulk=1)
    {
        $rules = [
            'receptors' => 'required|string',
            'message' => 'required|string',
        ];
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            // Handle validation errors
            $errors = $validator->errors();
            return response()->json(['success' => false, 'errors' => $errors], 400);
        }

        $receptors = $data['receptors'];
        $message = $data['message'];

        try {
            $result = $bulk == 1 ? $this->smsService->sendBulkSMS($message, $receptors) : $this->smsService->sendBulkSMS2($message, $receptors);
            return response()->json(['success' => true, 'data' => $result ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    public function sendTemplateSms($data)
    {
        $rules = [
            'type' => 'required|integer',
            'param1' => 'required|string',
            'param2' => 'nullable|string',
            'param3' => 'nullable|string',
            'receptor' => 'required|string',
            'template' => 'required|string',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            // Handle validation errors
            $errors = $validator->errors();
            return response()->json(['success' => false, 'errors' => $errors], 400);
        }

        $type = $data['type'];
        $param1 = urlencode($data['param1']);
        $param2 = urlencode($data['param2']);
        $param3 = urlencode($data['param3']);
        $receptor = $data['receptor'];
        $template = $data['template'];

        try {
            $result = $this->smsService->sendTemplateSms($type, $param1,$param2,$param3, $receptor, $template);
            return response()->json(['success' => true, 'data' => $result ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

}
