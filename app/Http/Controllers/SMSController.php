<?php

namespace App\Http\Controllers;

use App\Services\GhasedakSMSService;
use Illuminate\Http\Request;

class SMSController extends Controller
{
    protected $smsService;

    public function __construct(GhasedakSMSService $smsService)
    {
        $this->smsService = $smsService;
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
}
