<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Template;

class TemplateController extends Controller
{
    //
    public function getTemplates($type){
        $templates = Template::where('type',$type)->paginate(20);
        return view('Dashboard.gettemplate',['type'=>$type,'templates'=>$templates]);
    }

    public function createTemplates($type){
        return view('Dashboard.createtemplate',['type'=>$type,'wildcards'=>config('app.TEMPLATE_WILDCARDS')]);
    }

    public function create(Request $request){
        $input = $request->except('_token');
            $request->validate([
                'type' => 'required',
                'subject' => 'required',
                'message' => 'required|string',
            ]);
        
        $user = Template::create($input);
        return back()->with('success', 'Template added successfully.');
    }

    public function editTemplates($type,$id){
        $template = Template::where('type',$type)->where('id',$id)->first();
        return view('Dashboard.edittemplate',['type'=>$type,'id'=>$id,'wildcards'=>config('app.TEMPLATE_WILDCARDS'),'template'=>$template]);
    }

    public function edit(Request $request){
        $input = $request->except('_token');
        $id = $input['id'];
        $type = $input['type'];
        $request->validate([
                'type' => 'required',
                'subject' => 'required',
                'message' => 'required|string',
        ]);
        $updateArr = ['subject'=>$input['subject'],'message'=>$input['message']];
        Template::where('id', $id)->where('type',$type)->update($updateArr);
        return back()->with('success', 'Template updated successfully.');
    }
}
