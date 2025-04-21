<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Template;
use App\Models\Customer;
use Carbon\Carbon;
use App\Services\TwilioWhatsAppService;
use App\Jobs\SendWhatsAppMessage;
use App\Jobs\AddMailchimpSubscriber;
use App\Jobs\SendMailchimpCampaign;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendMailchimpBulkCampaignJob;

class CampaignController extends Controller
{
    public function index(Request $request){
        $campaigns = Campaign::orderBy('created_at', 'desc')->paginate(20);
        return view('Dashboard.listcampaign',['campaigns'=>$campaigns]);
    }

    public function addacmpaign(){
        $smsTemplates = [];
        $emailTemplates = [];
        $whatsAppTemplates = [];
        $templates = Template::orderBy('created_at', 'desc')->get();
        for($i=0;$i<count($templates);$i++){
            if($templates[$i]['type'] == 'sms'){
                $smsTemplates[] = array('id'=>$templates[$i]['id'],'subject'=>$templates[$i]['subject'],'message'=>$templates[$i]['message']);
            }elseif($templates[$i]['type'] == 'email'){
                $emailTemplates[] = array('id'=>$templates[$i]['id'],'subject'=>$templates[$i]['subject'],'message'=>$templates[$i]['message']);
            }elseif($templates[$i]['type'] == 'whatsapp'){
                $whatsAppTemplates[] = array('id'=>$templates[$i]['id'],'subject'=>$templates[$i]['subject'],'message'=>$templates[$i]['message']);
            }
        }
        return view('Dashboard.addcampaign',['smstemplates'=>$smsTemplates,'emailtemplates'=>$emailTemplates,'whatsapptemplates'=>$whatsAppTemplates]);
    }

    public function create(Request $request){
        $input = $request->except('_token');
        
        $request->validate([
            'name' => 'required',
            'uploadfile' => 'required|file|mimes:csv,txt|max:2048',
        ]);
        $file = $request->file('uploadfile');
        $campaignInput = ['name'=>$input['name'],'sms'=>(isset($input['sms']))?$input['sms']:'N','email'=>(isset($input['email']))?$input['email']:'N','whatsapp'=>(isset($input['whatsapp']))?$input['whatsapp']:'N','email_template_id'=>$input['email_template_id'],'sms_template_id'=>$input['sms_template_id'],'wp_template_id'=>$input['wp_template_id']];
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
        $input = $request->except('_token');
        $campaign = Campaign::where('id',$input['campaignid'])->first();
        $emailtemplateId = $campaign->email_template_id;        ;
        $templateDetails = Template::where('id',$emailtemplateId)->first();
        $emailSubject = $templateDetails->subject;
        $emailMessage = $templateDetails->message;
        // $emails = [
        //     ['email' => 'chansuro@gmail.com', 'first_name' => 'Surajit', 'last_name' => 'Koly'],
        //     ['email' => 'skoly06@gmail.com', 'first_name' => 'Surajit', 'last_name' => 'Koley'],
        // ];
        $wildCards = config('app.TEMPLATE_WILDCARDS');
        Log::info('User created', $wildCards);
        foreach($wildCards as $k=>$v){
            if($v == '[NAME]'){
                $emailSubject = str_replace($v, '*|FNAME|* *|LNAME|*',$emailSubject);
                $emailMessage = str_replace($v, '*|FNAME|* *|LNAME|*',$emailMessage);
            }elseif($v == '[EMAIL]'){
                $emailSubject = str_replace($v, '*|EMAIL|*',$emailSubject);
                $emailMessage = str_replace($v, '*|EMAIL|*',$emailMessage);
            }elseif($v == '[PHONE]'){
                $emailSubject = str_replace($v, '*|PHONE|*',$emailSubject);
                $emailMessage = str_replace($v, '*|PHONE|*',$emailMessage);
            }elseif($v == '[ADDRESS]'){
                $emailSubject = str_replace($v, '*|ADDRESS|*',$emailSubject);
                $emailMessage = str_replace($v, '*|ADDRESS|*',$emailMessage);
            }
        }
        // foreach ($emails as $user) {
        //     // AddMailchimpSubscriber::dispatch(
        //     //     $user['email'],
        //     //     $user['first_name'],
        //     //     $user['last_name']
        //     // )->delay(now()->addSeconds(2)); // Optional delay
        // }

        // SendMailchimpCampaign::dispatch(
        //     'March Newsletter',
        //     'Surajit',
        //     'skoly79@gmail.com',
        //     '<h1>Hello!</h1><p>This is the April newsletter.</p>'
        // )->delay(now()->addMinutes(1)); // Wait to ensure subscribers are added

        $recipients = [
            [
                'email' => 'chansuro@gmail.com',
                'first_name' => 'Surajit',
                'last_name' => 'Koly',
                'custom_subject' => $emailSubject,
            ],
            [
                'email' => 'skoly06@gmail.com',
                'first_name' => 'Surajit',
                'last_name' => 'Koley',
                'custom_subject' => $emailSubject,
            ],
        ];
    
        SendMailchimpBulkCampaignJob::dispatch($recipients,$emailMessage);

        $nowtime = Carbon::now();
        $campaignInput = ['sent_on_email'=>$nowtime];
        Campaign::where('id',$input['campaignid'])->update($campaignInput);
        return response()->json(['message' => 'Email scheduled', 'campaignId'=>$input['campaignid'],'sent_date'=>$nowtime->format('d-M-Y'),'template'=>$wildCards]);
    }

    public function editcampaign($id){
        $campaignDetails = Campaign::where('id', $id)->first();
        $smsTemplates = [];
        $emailTemplates = [];
        $whatsAppTemplates = [];
        $templates = Template::orderBy('created_at', 'desc')->get();
        for($i=0;$i<count($templates);$i++){
            if($templates[$i]['type'] == 'sms'){
                $smsTemplates[] = array('id'=>$templates[$i]['id'],'subject'=>$templates[$i]['subject'],'message'=>$templates[$i]['message']);
            }elseif($templates[$i]['type'] == 'email'){
                $emailTemplates[] = array('id'=>$templates[$i]['id'],'subject'=>$templates[$i]['subject'],'message'=>$templates[$i]['message']);
            }elseif($templates[$i]['type'] == 'whatsapp'){
                $whatsAppTemplates[] = array('id'=>$templates[$i]['id'],'subject'=>$templates[$i]['subject'],'message'=>$templates[$i]['message']);
            }
        }
        return view('Dashboard.editcampaign',['campaign'=>$campaignDetails,'smstemplates'=>$smsTemplates,'emailtemplates'=>$emailTemplates,'whatsapptemplates'=>$whatsAppTemplates]);
    }

    public function edit(Request $request){
        $input = $request->except('_token');
        $request->validate([
            'name' => 'required'
        ]);
        $campaignInput = ['name'=>$input['name'],'sms'=>(isset($input['sms']))?$input['sms']:'N','email'=>(isset($input['email']))?$input['email']:'N','whatsapp'=>(isset($input['whatsapp']))?$input['whatsapp']:'N','email_template_id'=>$input['email_template_id'],'sms_template_id'=>$input['sms_template_id'],'wp_template_id'=>$input['wp_template_id']];

        $campaign = Campaign::where('id',$input['id'])->update($campaignInput);
        return back()->with('success', 'Campaign updated successfully!');
    }

    public function downloadPDF()
    {
        $data = [ 'title' => 'Welcome to Laravel PDF!' ]; // Pass your data
        $pdf = Pdf::loadView('Dashboard.whatsapp', $data); // Your Blade view
        return $pdf->stream('invoice.pdf'); // This opens in browser
    }
}
