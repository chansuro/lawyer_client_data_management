<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UpdateProfileController extends Controller
{
    //
    public function index(){
        return view('Dashboard.updatepassword');
    }

    public function updatePassword(Request $request)
    {
        // Validate the input
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
        $user = Auth::guard('admin')->user();
        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        // Update the password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function updateprofile(){
        $user = Auth::guard('admin')->user();
        $userDetails = User::where('id',$user->id)->where('role','admin')->first();
        return view('Dashboard.updateprofile',['name'=>$userDetails->name,'email'=>$userDetails->email,'email_from'=>$userDetails->email_from,'whatsapp_from'=>$userDetails->whatsapp_from,'sms_from'=>$userDetails->sms_from]);
    }

    public function updateProfileAction(Request $request)
    {
        // Validate the input
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $input = $request->except('_token');
        $user = Auth::guard('admin')->user();
        $user->update($input);
        return back()->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048' // Max 2MB
        ]);

        // Get the authenticated user
        $user = Auth::guard('admin')->user();

        // Delete old avatar if exists
        if ($user->avatar && Storage::exists($user->avatar)) {
            Storage::delete($user->avatar);
        }

        // Store the new avatar in the `public/avatars` directory
        $path = $request->file('avatar')->store('avatar', 'public');
        // Update the user's avatar path in the database
        $user->update(['avatar' => $path]);

        return back()->with('success', 'Avatar updated successfully!');
    }

}
