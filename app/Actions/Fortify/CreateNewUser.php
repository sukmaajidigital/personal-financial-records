<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\EmailVerificationCode;
use App\Models\User;
use App\Notifications\EmailVerificationCodeNotification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Maximum registrations allowed per IP within the decay window.
     */
    private const MAX_REGISTRATIONS_PER_IP = 1;

    /**
     * Decay window in seconds (1 hour).
     */
    private const DECAY_SECONDS = 3600;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $this->ensureRegistrationIsNotThrottled();

        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);

        // Send email verification code
        $code = EmailVerificationCode::generateFor($user);
        $user->notify(new EmailVerificationCodeNotification($code->code));

        RateLimiter::hit($this->throttleKey(), self::DECAY_SECONDS);

        return $user;
    }

    /**
     * Ensure registration is not throttled for the current IP.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function ensureRegistrationIsNotThrottled(): void
    {
        $key = $this->throttleKey();

        if (! RateLimiter::tooManyAttempts($key, self::MAX_REGISTRATIONS_PER_IP)) {
            return;
        }

        $seconds = RateLimiter::availableIn($key);
        $minutes = (int) ceil($seconds / 60);

        throw ValidationException::withMessages([
            'email' => trans('Terlalu banyak akun dibuat dari alamat IP ini. Silakan coba lagi dalam :minutes menit.', [
                'minutes' => $minutes,
            ]),
        ]);
    }

    /**
     * Get the rate limiter throttle key for the current IP.
     */
    private function throttleKey(): string
    {
        return 'register:' . request()->ip();
    }
}
