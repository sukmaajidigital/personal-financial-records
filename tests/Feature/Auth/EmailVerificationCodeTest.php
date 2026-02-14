<?php

use App\Models\EmailVerificationCode;
use App\Models\User;
use App\Notifications\EmailVerificationCodeNotification;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;

it('shows the verification code page for unverified users', function () {
    $user = User::factory()->unverified()->create();

    $response = actingAs($user)->get(route('verification.code.show'));

    $response->assertOk();
});

it('redirects verified users away from verification page', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->get(route('verification.code.show'));

    $response->assertRedirect('/dashboard');
});

it('sends a verification code on resend', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();

    actingAs($user)->post(route('verification.code.resend'));

    Notification::assertSentTo($user, EmailVerificationCodeNotification::class);

    $code = EmailVerificationCode::where('user_id', $user->id)->first();
    expect($code)->not->toBeNull()
        ->and($code->code)->toHaveLength(6);
});

it('verifies email with valid code', function () {
    $user = User::factory()->unverified()->create();
    $code = EmailVerificationCode::generateFor($user);

    $response = actingAs($user)->post(route('verification.code.verify'), [
        'code' => $code->code,
    ]);

    $response->assertRedirect('/dashboard');

    $user->refresh();
    expect($user->email_verified_at)->not->toBeNull();
    expect(EmailVerificationCode::where('user_id', $user->id)->exists())->toBeFalse();
});

it('rejects invalid verification code', function () {
    $user = User::factory()->unverified()->create();
    EmailVerificationCode::generateFor($user);

    $response = actingAs($user)->post(route('verification.code.verify'), [
        'code' => '000000',
    ]);

    $response->assertSessionHasErrors('code');
    $user->refresh();
    expect($user->email_verified_at)->toBeNull();
});

it('rejects expired verification code', function () {
    $user = User::factory()->unverified()->create();
    $code = EmailVerificationCode::generateFor($user);

    // Manually expire the code
    $code->update(['expires_at' => now()->subMinute()]);

    $response = actingAs($user)->post(route('verification.code.verify'), [
        'code' => $code->code,
    ]);

    $response->assertSessionHasErrors('code');
    $user->refresh();
    expect($user->email_verified_at)->toBeNull();
});
