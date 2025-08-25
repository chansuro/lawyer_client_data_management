<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Template;
use App\Models\Customer;
use Carbon\Carbon;
//use App\Services\TwilioWhatsAppService;
use App\Jobs\SendWhatsAppMessage;
//use App\Jobs\AddMailchimpSubscriber;
//use App\Jobs\SendMailchimpCampaign;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendEmailMailgun;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
                $data['phone'] = strtolower($dataRaw['PHONE']);
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
    // send bulk whatsapp via twilio
    //public function sendBulk(Request $request, TwilioWhatsAppService $whatsapp)
    public function sendBulk(Request $request)
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


        // --- twilio settings
        // $recipients = [
        //     '+919874386721'
        // ];

        // $message = '🔥 Hello! This is a bulk WhatsApp message via Laravel & Twilio.';
        
        // foreach ($recipients as $contact) {
        //     SendWhatsAppMessage::dispatch($contact, $message);
        // }
        // --- twilio settings

        $input = $request->except('_token');
        $campaign = Campaign::where('id',$input['campaignid'])->first();

        $whatsapptemplateId = $campaign->wp_template_id;        ;
        $templateDetails = Template::where('id',$whatsapptemplateId)->first();
        $whatsappSubject = $templateDetails->subject;
        $wildCards = config('app.TEMPLATE_WILDCARDS');
        $getCustomers = Customer::where('campaign_id',$input['campaignid'])->get();
        
        foreach($getCustomers as $value){
            $recipientsData = [];
            $whatsappMessage = $templateDetails->message;
            foreach($wildCards as $k=>$v){
                if(Str::contains($whatsappMessage, $v)){
                    if($v == '[NAME]'){
                        $recipientsData[] = ['type' => 'text', 'text' => $value->name];
                    }elseif($v == '[EMAIL]'){
                        $recipientsData[] =  ['type' => 'text', 'text' => $value->email];
                    }elseif($v == '[PHONE]'){
                        $recipientsData[] = ['type' => 'text', 'text' => $value->phone];
                    }elseif($v == '[ADDRESS]'){
                        $recipientsData[] = ['type' => 'text', 'text' => $value->address];
                    }
                }
            }
            $phone = $value->phone;
            $phone = str_replace('+91','',$phone);
            $phone = '+91'.$phone;
            SendWhatsAppMessage::dispatch($phone, $whatsappMessage,$recipientsData);
        }
    
        return response()->json(['status' => 'queued', 'count' => []]);
    }

    // public function sendBulkEmail(Request $request){
    //     $input = $request->except('_token');
    //     $campaign = Campaign::where('id',$input['campaignid'])->first();
    //     $emailtemplateId = $campaign->email_template_id;        ;
    //     $templateDetails = Template::where('id',$emailtemplateId)->first();
        
    //     $wildCards = config('app.TEMPLATE_WILDCARDS');
    //     $getCustomers = Customer::where('campaign_id',$input['campaignid'])->whereNull('message_id')->get();
    //     $recipients = [];
    //     foreach($getCustomers as $value){
    //         $emailSubject = $templateDetails->subject;
    //         $emailMessage = $templateDetails->message;
    //         foreach($wildCards as $k=>$v){
    //             if($v == '[NAME]'){
    //                 $emailSubject = str_replace($v, $value->name,$emailSubject);
    //                 $emailMessage = str_replace($v, $value->name,$emailMessage);
    //             }elseif($v == '[EMAIL]'){
    //                 $emailSubject = str_replace($v, $value->email,$emailSubject);
    //                 $emailMessage = str_replace($v, $value->email,$emailMessage);
    //             }elseif($v == '[PHONE]'){
    //                 $emailSubject = str_replace($v, $value->phone,$emailSubject);
    //                 $emailMessage = str_replace($v, $value->phone,$emailMessage);
    //             }elseif($v == '[ADDRESS]'){
    //                 $emailSubject = str_replace($v, $value->address,$emailSubject);
    //                 $emailMessage = str_replace($v, $value->address,$emailMessage);
    //             }elseif($v == '[REF_NO]'){
    //                 $emailSubject = str_replace($v, $value->ref_no,$emailSubject);
    //                 $emailMessage = str_replace($v, $value->ref_no,$emailMessage);
    //             }elseif($v == '[CARD_NO]'){
    //                 $emailSubject = str_replace($v, $value->card_no,$emailSubject);
    //                 $emailMessage = str_replace($v, $value->card_no,$emailMessage);
    //             }elseif($v == '[AAN_NO]'){
    //                 $emailSubject = str_replace($v, $value->aan_no,$emailSubject);
    //                 $emailMessage = str_replace($v, $value->aan_no,$emailMessage);
    //             }elseif($v == '[ACCOUNT_NO]'){
    //                 $emailSubject = str_replace($v, $value->account_no,$emailSubject);
    //                 $emailMessage = str_replace($v, $value->account_no,$emailMessage);
    //             }elseif($v == '[AMOUNT]'){
    //                 $emailSubject = str_replace($v, $value->amount,$emailSubject);
    //                 $emailMessage = str_replace($v, $value->amount,$emailMessage);
    //             }elseif($v == '[FILING_DATE]'){
    //                 $emailSubject = str_replace($v, Carbon::parse($value->date_filing)->format('F j, Y'),$emailSubject);
    //                 $emailMessage = str_replace($v, Carbon::parse($value->date_filing)->format('F j, Y'),$emailMessage);
    //             }elseif($v == '[NOTICE_DATE]'){
    //                 $emailSubject = str_replace($v, Carbon::parse($value->notice_date)->format('F j, Y'),$emailSubject);
    //                 $emailMessage = str_replace($v, Carbon::parse($value->notice_date)->format('F j, Y'),$emailMessage);
    //             }elseif($v == '[CURRENT_DATE]'){
    //                 $emailSubject = str_replace($v, Carbon::now()->format('F j, Y'),$emailSubject);
    //                 $emailMessage = str_replace($v, Carbon::now()->format('F j, Y'),$emailMessage);
    //             }elseif($v == '[DISHONOUR_REASON]'){
    //                 $emailSubject = str_replace($v, $value->reason,$emailSubject);
    //                 $emailMessage = str_replace($v, $value->reason,$emailMessage);
    //             }
    //         }
    //         $recipients[] = [
    //             'id'=>$value->id,
    //             'email' => $value->email,
    //             'custom_subject' => $emailSubject,
    //             'custom_message' => $emailMessage,
    //         ];
    //     }
    //     SendEmailMailgun::dispatch($recipients,$emailMessage,$input['campaignid']);
    //     //SendMailchimpBulkCampaignJob::dispatch($recipients,$emailMessage,$input['campaignid']);

    //     $nowtime = Carbon::now();
    //     $campaignInput = ['sent_on_email'=>$nowtime];
    //     Campaign::where('id',$input['campaignid'])->update($campaignInput);
    //     return response()->json(['message' => 'Email scheduled', 'campaignId'=>$input['campaignid'],'sent_date'=>$nowtime->format('d-M-Y'),'template'=>$wildCards]);
    // }

    public function sendBulkEmail(Request $request){
        $input = $request->except('_token');
        $campaign = Campaign::where('id',$input['campaignid'])->first();
        $emailtemplateId = $campaign->email_template_id;        ;
        $templateDetails = Template::where('id',$emailtemplateId)->first();
        $campaignId = $input['campaignid'];
        
        $wildCards = config('app.TEMPLATE_WILDCARDS');
        $getCustomers = Customer::where('campaign_id',$input['campaignid'])->where('email_status','Pending')->chunk(50,function ($users) use ($templateDetails,$wildCards,$campaignId) {
            foreach ($users as $value) {
                $recipients = [];
                $emailSubject = $templateDetails->subject;
                $emailMessage = $templateDetails->message;
                foreach($wildCards as $k=>$v){
                    if($v == '[NAME]'){
                        $emailSubject = str_replace($v, $value->name,$emailSubject);
                        $emailMessage = str_replace($v, $value->name,$emailMessage);
                    }elseif($v == '[EMAIL]'){
                        $emailSubject = str_replace($v, $value->email,$emailSubject);
                        $emailMessage = str_replace($v, $value->email,$emailMessage);
                    }elseif($v == '[PHONE]'){
                        $emailSubject = str_replace($v, $value->phone,$emailSubject);
                        $emailMessage = str_replace($v, $value->phone,$emailMessage);
                    }elseif($v == '[ADDRESS]'){
                        $emailSubject = str_replace($v, $value->address,$emailSubject);
                        $emailMessage = str_replace($v, $value->address,$emailMessage);
                    }elseif($v == '[REF_NO]'){
                        $emailSubject = str_replace($v, $value->ref_no,$emailSubject);
                        $emailMessage = str_replace($v, $value->ref_no,$emailMessage);
                    }elseif($v == '[CARD_NO]'){
                        $emailSubject = str_replace($v, $value->card_no,$emailSubject);
                        $emailMessage = str_replace($v, $value->card_no,$emailMessage);
                    }elseif($v == '[AAN_NO]'){
                        $emailSubject = str_replace($v, $value->aan_no,$emailSubject);
                        $emailMessage = str_replace($v, $value->aan_no,$emailMessage);
                    }elseif($v == '[ACCOUNT_NO]'){
                        $emailSubject = str_replace($v, $value->account_no,$emailSubject);
                        $emailMessage = str_replace($v, $value->account_no,$emailMessage);
                    }elseif($v == '[AMOUNT]'){
                        $emailSubject = str_replace($v, $value->amount,$emailSubject);
                        $emailMessage = str_replace($v, $value->amount,$emailMessage);
                    }elseif($v == '[FILING_DATE]'){
                        $emailSubject = str_replace($v, Carbon::parse($value->date_filing)->format('F j, Y'),$emailSubject);
                        $emailMessage = str_replace($v, Carbon::parse($value->date_filing)->format('F j, Y'),$emailMessage);
                    }elseif($v == '[NOTICE_DATE]'){
                        $emailSubject = str_replace($v, Carbon::parse($value->notice_date)->format('F j, Y'),$emailSubject);
                        $emailMessage = str_replace($v, Carbon::parse($value->notice_date)->format('F j, Y'),$emailMessage);
                    }elseif($v == '[CURRENT_DATE]'){
                        $emailSubject = str_replace($v, Carbon::now()->format('F j, Y'),$emailSubject);
                        $emailMessage = str_replace($v, Carbon::now()->format('F j, Y'),$emailMessage);
                    }elseif($v == '[DISHONOUR_REASON]'){
                        $emailSubject = str_replace($v, $value->reason,$emailSubject);
                        $emailMessage = str_replace($v, $value->reason,$emailMessage);
                    }
                }
                $recipients[] = [
                    'id'=>$value->id,
                    'email' => $value->email,
                    'custom_subject' => $emailSubject,
                    'custom_message' => $emailMessage,
                ];
                SendEmailMailgun::dispatch($recipients,$emailMessage,$campaignId)->onQueue('emails')->delay(now()->addSeconds(rand(1, 5)));
            }
        });
        $nowtime = Carbon::now();
        $campaignInput = ['sent_on_email'=>$nowtime];
        //Campaign::where('id',$input['campaignid'])->update($campaignInput);
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

    public function handlemailchimpwebhook($campaignId){
        $serverPrefix = config('services.mailchimp.server_prefix');
        $apiKey = config('services.mailchimp.key');
        $getUrl ='https://'.$serverPrefix.'.api.mailchimp.com/3.0/reports/'.$campaignId.'/email-activity';
        $response = Http::withBasicAuth('anystring', $apiKey)
        ->get($getUrl);
        
        if ($response->successful()) {
            $returnArr = json_decode($response, true);
            print '<pre>';
            print_r($returnArr['emails']);
            die;
        }
    
        return response()->json([
            'status' => 'error',
            'message' => $response->body(),
        ], $response->status());
    }

    public function downloadPDF()
    {
        $data = [ 'title' => 'Welcome to Laravel PDF!' ]; // Pass your data
        $pdf = Pdf::loadView('Dashboard.whatsapp', $data); // Your Blade view
        return $pdf->stream('invoice.pdf'); // This opens in browser
    }
    public function downloadPDFEmail($campaignId,$customerId)
    {
        $campaign = Campaign::where('id',$campaignId)->first();
        $emailtemplateId = $campaign->email_template_id;        ;
        $templateDetails = Template::where('id',$emailtemplateId)->first();
        
        $wildCards = config('app.TEMPLATE_WILDCARDS');
        $getCustomers = Customer::where('id',$customerId)->first();
        $emailSubject = $templateDetails->subject;
        $emailMessage = $templateDetails->message;
        foreach($wildCards as $k=>$v){
            if($v == '[NAME]'){
                $emailSubject = str_replace($v, $getCustomers->name,$emailSubject);
                $emailMessage = str_replace($v, $getCustomers->name,$emailMessage);
            }elseif($v == '[EMAIL]'){
                $emailSubject = str_replace($v, $getCustomers->email,$emailSubject);
                $emailMessage = str_replace($v, $getCustomers->email,$emailMessage);
            }elseif($v == '[PHONE]'){
                $emailSubject = str_replace($v, $getCustomers->phone,$emailSubject);
                $emailMessage = str_replace($v, $getCustomers->phone,$emailMessage);
            }elseif($v == '[ADDRESS]'){
                $emailSubject = str_replace($v, $getCustomers->address,$emailSubject);
                $emailMessage = str_replace($v, $getCustomers->address,$emailMessage);
            }elseif($v == '[REF_NO]'){
                $emailSubject = str_replace($v, $getCustomers->ref_no,$emailSubject);
                $emailMessage = str_replace($v, $getCustomers->ref_no,$emailMessage);
            }elseif($v == '[CARD_NO]'){
                $emailSubject = str_replace($v, $getCustomers->card_no,$emailSubject);
                $emailMessage = str_replace($v, $getCustomers->card_no,$emailMessage);
            }elseif($v == '[AAN_NO]'){
                $emailSubject = str_replace($v, $getCustomers->aan_no,$emailSubject);
                $emailMessage = str_replace($v, $getCustomers->aan_no,$emailMessage);
            }elseif($v == '[ACCOUNT_NO]'){
                $emailSubject = str_replace($v, $getCustomers->account_no,$emailSubject);
                $emailMessage = str_replace($v, $getCustomers->account_no,$emailMessage);
            }elseif($v == '[AMOUNT]'){
                $emailSubject = str_replace($v, $getCustomers->amount,$emailSubject);
                $emailMessage = str_replace($v, $getCustomers->amount,$emailMessage);
            }elseif($v == '[FILING_DATE]'){
                $emailSubject = str_replace($v, Carbon::parse($getCustomers->date_filing)->format('F j, Y'),$emailSubject);
                $emailMessage = str_replace($v, Carbon::parse($getCustomers->date_filing)->format('F j, Y'),$emailMessage);
            }elseif($v == '[NOTICE_DATE]'){
                $emailSubject = str_replace($v, Carbon::parse($getCustomers->notice_date)->format('F j, Y'),$emailSubject);
                $emailMessage = str_replace($v, Carbon::parse($getCustomers->notice_date)->format('F j, Y'),$emailMessage);
            }elseif($v == '[CURRENT_DATE]'){
                $emailSubject = str_replace($v, Carbon::now()->format('F j, Y'),$emailSubject);
                $emailMessage = str_replace($v, Carbon::now()->format('F j, Y'),$emailMessage);
            }elseif($v == '[DISHONOUR_REASON]'){
                $emailSubject = str_replace($v, $getCustomers->reason,$emailSubject);
                $emailMessage = str_replace($v, $getCustomers->reason,$emailMessage);
            }
        }

        $data = ['subject'=>$emailSubject,'message'=>$emailMessage,'year'=>date('Y') ]; // Pass your data

        $pdf = Pdf::loadView('Dashboard.emailbody', $data); // Your Blade view
        return $pdf->stream('email.pdf'); // This opens in browser
    }
}
