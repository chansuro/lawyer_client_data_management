<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Customer;
use Carbon\Carbon;
use App\Services\TwilioWhatsAppService;
use App\Jobs\SendWhatsAppMessage;
use App\Jobs\AddMailchimpSubscriber;
use App\Jobs\SendMailchimpCampaign;

class CampaignController extends Controller
{
    public function index(Request $request){
        $campaigns = Campaign::orderBy('created_at', 'desc')->paginate(20);
        return view('Dashboard.listcampaign',['campaigns'=>$campaigns]);
    }

    public function addacmpaign(){
        return view('Dashboard.addcampaign');
    }

    public function create(Request $request){
        $input = $request->except('_token');
        $request->validate([
            'name' => 'required',
            'uploadfile' => 'required|file|mimes:csv,txt|max:2048',
        ]);
        $file = $request->file('uploadfile');
        $campaignInput = ['name'=>$input['name'],'sms'=>(isset($input['sms']))?$input['sms']:'N','email'=>(isset($input['email']))?$input['email']:'N','whatsapp'=>(isset($input['whatsapp']))?$input['whatsapp']:'N'];

        $campaign = Campaign::create($campaignInput);
        $insertedId = $campaign->id;
         // Open and read the CSV
         if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle); // Read the header row

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                // Map each row to header
                $dataRaw = array_combine($header, $row);
                $data['name'] = $dataRaw['CUSTOMER_NAME'];
                $data['ref_no'] = $dataRaw['REF_NO'];
                $data['card_no'] = $dataRaw['CARD_NO'];
                $data['aan_no'] = $dataRaw['AAN_NO'];
                $data['account_no'] = $dataRaw['ACCOUNT_NUMBER'];
                $data['amount'] = $dataRaw['AMOUNT'];
                $data['date_filing'] = Carbon::createFromFormat('d.m.Y', $dataRaw['DATE'])->format('Y-m-d');
                $data['reason'] = $dataRaw['DISHONOUR_REASON'];
                $data['address'] = $dataRaw['ADDRESS'];
                $data['notice_date'] = Carbon::createFromFormat('d.m.Y', $dataRaw['NOTICE_DATE'])->format('Y-m-d');
                $data['email'] = strtolower($dataRaw['EMAIL_ID']);
                $data['campaign_id'] = $insertedId;
                Customer::create($data);

                // Example: Insert into DB (if you have a model)
                // \App\Models\Item::create($data);
            }
            fclose($handle);
        }
        return back()->with('success', 'CSV uploaded and processed!');

    }

    public function campaigndelete($id){
        Customer::where('campaign_id', $id)->delete();
        Campaign::where('id', $id)->delete();
        return back()->with('success', 'Campaign deleted successfully!');
    }
    // send bulk whatsapp
    public function sendBulk(Request $request, TwilioWhatsAppService $whatsapp)
    {
        // $numbers = $request->input('numbers'); // array of raw numbers like ['+1234567890']
        // $message = $request->input('message');

        // $results = [];

        // foreach ($numbers as $number) {
        //     try {
        //         $results[$number] = $whatsapp->sendMessage($number, $message);
        //     } catch (\Exception $e) {
        //         $results[$number] = 'Error: ' . $e->getMessage();
        //     }
        // }

        // return response()->json([
        //     'status' => 'complete',
        //     'results' => $results
        // ]);
        $recipients = [
            '+919874386721'
        ];

        $message = '🔥 Hello! This is a bulk WhatsApp message via Laravel & Twilio.';
        
        foreach ($recipients as $contact) {
            SendWhatsAppMessage::dispatch($contact, $message);
        }
    
        return response()->json(['status' => 'queued', 'count' => count($recipients)]);
    }

    public function sendBulkEmail(Request $request){
        $emails = [
            ['email' => 'chansuro@gmail.com', 'first_name' => 'Surajit', 'last_name' => 'Koly'],
            ['email' => 'skoly06@gmail.com', 'first_name' => 'Surajit', 'last_name' => 'Koley'],
        ];
    
        foreach ($emails as $user) {
            AddMailchimpSubscriber::dispatch(
                $user['email'],
                $user['first_name'],
                $user['last_name']
            )->delay(now()->addSeconds(2)); // Optional delay
        }

        SendMailchimpCampaign::dispatch(
            'March Newsletter',
            'Surajit',
            'skoly79@gmail.com',
            '<h1>Hello!</h1><p>This is the April newsletter.</p>'
        )->delay(now()->addMinutes(1)); // Wait to ensure subscribers are added

        return response()->json(['message' => 'Newsletter scheduled']);
    }
}
