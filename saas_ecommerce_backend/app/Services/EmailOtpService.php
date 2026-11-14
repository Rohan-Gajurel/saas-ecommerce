<?php

namespace App\Services;

use App\Models\EmailOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailOtpService
{
    public function sendOtp(string $email): void
    {
        $otp = (string) random_int(100000, 999999);

        EmailOtp::query()->updateOrCreate(
            ['email' => $email],
            [
                'otp_hash' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(10),
                'verified_at' => null,
                'verification_expires_at' => null,
                'attempts' => 0,
            ]
        );

        Mail::raw("Your verification OTP is: {$otp}. This OTP will expire in 10 minutes.", function ($message) use ($email) {
            $message->to($email)
                ->subject('Email Verification OTP');
        });
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function verifyOtp(string $email, string $otp): array
    {
        $emailOtp = EmailOtp::query()->where('email', $email)->first();

        if (! $emailOtp || ! $emailOtp->otp_hash || ! $emailOtp->otp_expires_at) {
            return [
                'success' => false,
                'message' => 'Please request an OTP first.',
            ];
        }

        if (now()->greaterThan($emailOtp->otp_expires_at)) {
            return [
                'success' => false,
                'message' => 'OTP has expired. Please request a new OTP.',
            ];
        }

        if ($emailOtp->attempts >= 5) {
            return [
                'success' => false,
                'message' => 'Too many invalid attempts. Please request a new OTP.',
            ];
        }

        if (! Hash::check($otp, $emailOtp->otp_hash)) {
            $emailOtp->increment('attempts');

            return [
                'success' => false,
                'message' => 'Invalid OTP.',
            ];
        }

        $emailOtp->update([
            'otp_hash' => null,
            'otp_expires_at' => null,
            'verified_at' => now(),
            'verification_expires_at' => now()->addMinutes(30),
            'attempts' => 0,
        ]);

        return [
            'success' => true,
            'message' => 'Email verified successfully.',
        ];
    }

    public function isEmailVerifiedForRegistration(string $email): bool
    {
        return EmailOtp::query()
            ->where('email', $email)
            ->whereNotNull('verified_at')
            ->where('verification_expires_at', '>=', now())
            ->exists();
    }

    public function consumeVerification(string $email): void
    {
        EmailOtp::query()->where('email', $email)->delete();
    }
}
