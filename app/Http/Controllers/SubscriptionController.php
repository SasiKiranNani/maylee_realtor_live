<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
     {
        $validated = $request->validate([
            'email' => 'required|email|unique:subscriptions,email',
            'consent' => 'accepted',
        ]);

        $validated['consent'] = true;

        Subscription::create($validated);

        ToastMagic::success('Thank you for subscribing!');

        return back();
    }
}
