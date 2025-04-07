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
        $query = Customer::selectRaw("id,name,ref_no,card_no,aan_no,account_no,amount,date_filing,reason,address,notice_date,email,campaign_id,created_at");
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
        $query = Customer::selectRaw("id,name,ref_no,card_no,aan_no,account_no,amount,date_filing,reason,address,notice_date,email,campaign_id,created_at")->where('campaign_id',$campaignId);
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
        $customers = Customer::selectRaw("name, email")->where('campaign_id',$id)->get();
        $callback = function () use ($customers) {
            $handle = fopen('php://output', 'w');

            // CSV header
            fputcsv($handle, [ 'Name', 'Email']);

            // Fetch data from DB
            
            foreach ($customers as $customer) {
               fputcsv($handle, [ $customer->name, $customer->email]);
            }

            fclose($handle);
        };
            
        return new StreamedResponse($callback, 200, $headers);
    }
    
}
