<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    //
    public function index(){
        $customers = Customer::orderBy('created_at', 'desc')->paginate(20);
        return view('Dashboard.getcustomer',['customers'=>$customers]);
    }
}
