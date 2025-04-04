<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Template;

class TemplateController extends Controller
{
    //
    public function getTemplates($type){
        $templates = Template::where('type',$type)->paginate(2);
        return view('Dashboard.gettemplate',['type'=>$type,'templates'=>$templates]);
    }

    public function createTemplates($type){
        return view('Dashboard.createtemplate',['type'=>$type,'wildcards'=>config('app.TEMPLATE_WILDCARDS')]);
    }

    public function create(Request $request){
        $input = $request->except('_token');
        if($input['type'] == 'email'){
            $request->validate([
                'type' => 'required',
                'subject' => 'required',
                'message' => 'required|string',
            ]);
        }else{
            $request->validate([
                'type' => 'required',
                'message' => 'required',
            ]);
        }
        
        $user = Template::create($input);
        return back()->with('success', 'Template added successfully.');
    }
}
