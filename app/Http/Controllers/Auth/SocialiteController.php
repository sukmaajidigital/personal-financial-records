<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception) {
            return redirect()->route('login')->with('status', 'Gagal melakukan autentikasi dengan Google. Silakan coba lagi.');
        }

        // Check if user already exists with this Google ID
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            // Existing Google user — log in
            Auth::login($user, remember: true);

            return redirect()->intended(config('fortify.home', '/dashboard'));
        }

        // Check if user exists with the same email (registered via email/password)
        $existingUser = User::where('email', $googleUser->getEmail())->first();

        if ($existingUser) {
            // Link Google account to existing user
            $existingUser->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);

            // Mark email as verified since Google already verified it
            if (! $existingUser->hasVerifiedEmail()) {
                $existingUser->markEmailAsVerified();
            }

            Auth::login($existingUser, remember: true);

            return redirect()->intended(config('fortify.home', '/dashboard'));
        }

        // Create new user from Google
        $user = User::create([
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'password' => null,
        ]);

        // Mark email as verified — Google already verified it
        $user->markEmailAsVerified();

        Auth::login($user, remember: true);

        return redirect()->intended(config('fortify.home', '/dashboard'));
    }
}
