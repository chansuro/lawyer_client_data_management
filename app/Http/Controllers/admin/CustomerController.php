<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    //
    public function index(Request $request){
        $input = $request->except('_token');
        $query = Customer::selectRaw("id,name,ref_no,card_no,aan_no,account_no,amount,date_filing,reason,address,notice_date,email,campaign_id,created_at,phone");
        $query->when((isset($input['search'])), function ($query) use ($input) {
            $query->where('name','like','%'.$input['search'].'%')
                    ->orWhere('ref_no',$input['search'])
                    ->orWhere('card_no',$input['search'])
                    ->orWhere('aan_no',$input['search'])
                    ->orWhere('account_no',$input['search'])
                    ->orWhere('email','like','%'.$input['search'].'%');
        });
        $customers = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('Dashboard.getcustomer',['customers'=>$customers,'campaignId'=>null]);
    }
    public function campaignwise($campaignId,Request $request){
        $input = $request->except('_token');
        $query = Customer::selectRaw("id,name,ref_no,card_no,aan_no,account_no,amount,date_filing,reason,address,notice_date,email,campaign_id,created_at,phone")->where('campaign_id',$campaignId);
        $query->when((isset($input['search'])), function ($query) use ($input) {
            $query->where('name','like','%'.$input['search'].'%')
                    ->orWhere('ref_no',$input['search'])
                    ->orWhere('card_no',$input['search'])
                    ->orWhere('aan_no',$input['search'])
                    ->orWhere('account_no',$input['search'])
                    ->orWhere('email','like','%'.$input['search'].'%');
        });
        $customers = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('Dashboard.getcustomer',['customers'=>$customers,'campaignId'=>$campaignId]);
    }

    public function customerdelete($id){
        Customer::where('id', $id)->delete();
        return back()->with('success', 'Customer deleted successfully!');
    }

    public function export($id)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users.csv"',
        ];
        $customers = Customer::selectRaw("id, name, email, email_status")->where('campaign_id',$id)->get();
        $callback = function () use ($customers,$id) {
            $handle = fopen('php://output', 'w');

            // CSV header
            fputcsv($handle, [ 'Name', 'Email','whatsapp link','SMS link','Email Status','Email link']);

            // Fetch data from DB
            
            foreach ($customers as $customer) {
                $url = 'http://kblegalassociates.com/download-pdf-whatsapp/'.$id.'/'.$customer->id;
                $url_sms = 'http://kblegalassociates.com/download-pdf-sms/'.$id.'/'.$customer->id;
                $url_email = 'http://kblegalassociates.com/download-pdf-email/'.$id.'/'.$customer->id;
                fputcsv($handle, [ $customer->name, $customer->email,$url,$url_sms,$customer->email_status,$url_email]);
            }

            fclose($handle);
        };
            
        return new StreamedResponse($callback, 200, $headers);
    }
    
}
