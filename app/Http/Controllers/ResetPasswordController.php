<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules\Password as PasswordRules;


class ResetPasswordController extends Controller
{
    public function showForm(Request $request)
    {
        //
        return inertia('ResetPassword/SendEmailPage');
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['success' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(string $token)
    {
        return inertia('ResetPassword/ResetPasswordPage', ['token' => $token]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'string',
                'confirmed',
                PasswordRules::min(12)
                    ->letters() //checa que tenga letras
                    ->mixedCase() //al menos 1 mayúscula y 1 minúscula
                    ->numbers() //al menos 1 numero
                    ->symbols() //al menos un símbolo
                    ->uncompromised(),],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with(['success' => __($status)])
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
