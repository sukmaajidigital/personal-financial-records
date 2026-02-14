<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerificationCode;
use App\Notifications\EmailVerificationCodeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationCodeController extends Controller
{
    /**
     * Show the email verification code form.
     */
    public function show(Request $request): Response|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(config('fortify.home', '/dashboard'));
        }

        return Inertia::render('auth/VerifyEmailCode', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Verify the email verification code.
     */
    public function verify(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(config('fortify.home', '/dashboard'));
        }

        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $verificationCode = EmailVerificationCode::where('user_id', $request->user()->id)
            ->where('code', $request->input('code'))
            ->first();

        if (! $verificationCode) {
            throw ValidationException::withMessages([
                'code' => 'Kode verifikasi tidak valid.',
            ]);
        }

        if ($verificationCode->isExpired()) {
            $verificationCode->delete();

            throw ValidationException::withMessages([
                'code' => 'Kode verifikasi sudah kedaluwarsa. Silakan minta kode baru.',
            ]);
        }

        $request->user()->markEmailAsVerified();
        $verificationCode->delete();

        return redirect()->intended(config('fortify.home', '/dashboard'))
            ->with('success', 'Email berhasil diverifikasi!');
    }

    /**
     * Resend a new verification code.
     */
    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(config('fortify.home', '/dashboard'));
        }

        $code = EmailVerificationCode::generateFor($request->user());
        $request->user()->notify(new EmailVerificationCodeNotification($code->code));

        return back()->with('status', 'verification-code-sent');
    }
}
