<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Customer;
use Carbon\Carbon;

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
}
