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
    public function sendBulkSms(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'sender' => 'required|string',
            'receptors' => 'required|array',
            'checkmessageids' => 'required|integer',
        ]);

        $message = $request->input('message');
        $sender = $request->input('sender');
        $receptors = $request->input('receptors');
        $checkMessageIds = $request->input('checkmessageids');

        try {
            $result = $this->smsService->sendBulkSms($message, $sender, $receptors, $checkMessageIds);
            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    public function sendTemplateSms($data)
    {
//        $data = [
//            'type' => 1,
//            'param1' => '1234',
//            'receptor' => '09191111111',
//            'template' => 'testvoice',
//        ];

        $rules = [
            'type' => 'required|integer',
            'param1' => 'required|string',
            'param2' => 'string',
            'param3' => 'string',
            'receptor' => 'required|string',
            'template' => 'required|string',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            // Handle validation errors
            $errors = $validator->errors();
            return response()->json(['success' => false, 'errors' => $errors], 422);
        }

        $type = $data['type'];
        $param1 = $data['param1'];
        $param2 = $data['param2'];
        $param3 = $data['param3'];
        $receptor = $data['receptor'];
        $template = $data['template'];

        try {
            $result = $this->smsService->sendTemplateSms($type, $param1,$param2,$param3, $receptor, $template);
            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

}
