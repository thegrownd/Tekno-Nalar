<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeNewsletter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        // For authenticated users, use their email automatically
        $user = Auth::user();
        
        if ($user) {
            $email = $user->email;
            $name = $user->name;
        } else {
            // Fallback for non-authenticated (if route allows) or validation
            $request->validate([
                'email' => 'required|email',
                'name' => 'nullable|string|max:255',
                // Simple honeypot
                'website' => 'nullable|string|max:0',
            ]);
            $email = $request->email;
            $name = $request->name;
        }

        if ($request->filled('website')) {
            // Honeypot filled, likely a bot
            Log::info("Newsletter bot subscription attempt blocked.", ['ip' => $request->ip()]);
            return response()->json(['message' => 'Subscription successful.'], 200);
        }

        Log::info("Newsletter subscription request received.", [
            'email' => $email,
            'ip' => $request->ip(),
            'user_id' => $user ? $user->id : 'guest'
        ]);

        $subscriber = Subscriber::withTrashed()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'unsubscribed_at' => null // Reactivate if previously unsubscribed
            ]
        );

        // Send welcome email
        if ($subscriber->wasRecentlyCreated || $subscriber->wasChanged('unsubscribed_at')) {
            try {
                Mail::to($subscriber->email)->queue(new WelcomeNewsletter($subscriber));
                Log::info("Welcome email queued for subscriber: {$subscriber->email}");
            } catch (\Exception $e) {
                Log::error("Failed to send welcome email to {$subscriber->email}: " . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Berlangganan berhasil!',
            'data' => $subscriber
        ]);
    }

    public function unsubscribe(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Link unsubscribe tidak valid atau sudah kadaluarsa.');
        }

        $subscriber = Subscriber::where('email', $request->email)->firstOrFail();
        $subscriber->update(['unsubscribed_at' => now()]);

        return view('subscription.unsubscribed');
    }
}
