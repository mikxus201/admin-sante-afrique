<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class AuthController extends Controller
{
    public function passwordLogin(Request $request)
    {
        // Validation simple JSON
        $data = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        /** @var \App\Models\User|null $user */
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json(['ok'=>false,'reason'=>'user_not_found'], 401);
        }

        // Si tu as une colonne "is_active"
        if (Schema::hasColumn($user->getTable(), 'is_active') && !$user->is_active) {
            return response()->json(['ok'=>false,'reason'=>'user_inactive'], 403);
        }

        if (empty($user->password)) {
            return response()->json(['ok'=>false,'reason'=>'password_empty_for_user'], 401);
        }

        if (!Hash::check($data['password'], $user->password)) {
            return response()->json(['ok'=>false,'reason'=>'password_mismatch'], 401);
        }

        // ✅ OK : on connecte via le guard "web" (Sanctum SPA)
        Auth::guard('web')->login($user, true);
        $request->session()->regenerate();

        // Si tu veux forcer une étape OTP quand email non vérifié :
        if (Schema::hasColumn($user->getTable(), 'email_verified_at') && empty($user->email_verified_at)) {
            return response()->json([
                'ok'           => true,
                'otp_required' => true,
                'message'      => 'Adresse email non vérifiée : un code OTP est requis.',
            ], 200);
        }

        return response()->json([
            'ok'   => true,
            'data' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['ok' => true]);
    }
}
