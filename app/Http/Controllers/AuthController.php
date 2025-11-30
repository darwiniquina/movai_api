<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendEmailRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use SendGrid;
use SendGrid\Mail\Mail;

class AuthController extends Controller
{
    public function verifyEmail(Request $request, $id, $hash)
    {
        try {
            $user = User::findOrFail($id);

            if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
                $message = 'Oops! This verification link is invalid or expired.';
            } elseif ($user->hasVerifiedEmail()) {
                $message = 'Welcome back! Your email is already verified.';
            } else {
                $user->markEmailAsVerified();
                event(new Verified($user));

                $message = 'Email verified successfully! ';
            }

            return view('auth.verify-email', [
                'message' => $message,
            ]);

        } catch (\Exception $e) {
            Log::error('Email verification failed', [
                'user_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return view('auth.verify-email', [
                'message' => 'Something went wrong. Please try again later.',
            ]);
        }
    }

    public function resendVerification(ResendEmailRequest $request): JsonResponse
    {
        $email = $request->validated('email');

        $user = User::where('email', $email)->first();

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 422);
        }

        $response = response()->json([
            'message' => 'Verification link sent to your email.',
        ], 200);

        $user->sendEmailVerificationNotification();

        return $response;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $user = User::create($validated);

            $verification_url = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1($user->email)]
            );

            $email = new Mail;
            $email->setFrom(env('MAIL_FROM_ADDRESS'), env('APP_NAME'));
            $email->setSubject('Welcome to '.env('APP_NAME').'! Verify Your Email');
            $email->addTo($user->email, $user->name);

            $emailContent = view('emails.custom_verification', [
                'url' => $verification_url,
                'user' => $user,
                'appName' => env('APP_NAME'),
            ])->render();

            $email->addContent('text/html', $emailContent);

            $sendgrid = new SendGrid(env('SENDGRID_API_KEY'));
            $sendgrid->send($email);

            return response()->json([
                'message' => 'Registration successful. Please check your email for the verification link.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Registration failed. Please try again.',
            ], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        if (! $user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Please verify your email address before logging in.',
                'requires_verification' => true,
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Successfully logged out',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Logout failed', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Logout failed',
            ], 500);
        }
    }
}
