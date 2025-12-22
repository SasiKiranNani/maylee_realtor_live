<?php

namespace App\Http\Controllers;

use App\Mail\ContactAdminMail;
use App\Mail\ContactUserReplyMail;
use App\Models\ContactSubmission;
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'nullable|string',
            'source' => 'required|string',
            'city' => 'nullable|string',
            'listing_key' => 'nullable|string',
        ]);
        $submission = ContactSubmission::create($validated);

        try {
            $adminEmail = env('ADMIN_EMAIL', 'info@mayleerealtor.com');
            Mail::to($adminEmail)->send(new ContactAdminMail($submission));

            Mail::to($submission->email)->send(new ContactUserReplyMail($submission));
        } catch (\Exception $e) {
            \Log::error('Contact form email failed: ' . $e->getMessage());
        }

        ToastMagic::success('Your message has been sent successfully!');
        return back();
    }
}
