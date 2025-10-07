<?php

// app/Http/Controllers/AuthOtpController.php
namespace App\Http\Controllers;

use App\Mail\OtpCodeMail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;


class AuthOtpController extends Controller
{
    /** --------- Helper: normalise l'utilisateur pour le front --------- */
    private function mapUser(User $u): array
    {
        // si tu as des colonnes "nom" et "prenoms", on fabrique un "name"
        $fullName = trim(implode(' ', array_filter([data_get($u, 'prenoms'), data_get($u, 'nom')])));
        $name = $u->name ?: $fullName ?: 'Utilisateur';

        return [
            'id'      => $u->id,
            'email'   => $u->email,
            'phone'   => $u->phone,
            'nom'     => $u->nom ?? null,
            'prenoms' => $u->prenoms ?? null,
            'name'    => $name,
            // ajoute d'autres champs si besoin (avatar, roles, etc.)
        ];
    }

    // 1) Demande d’OTP (email ou téléphone)
    public function request(Request $request)
    {
        $identifier = trim((string) $request->input('identifier'));
        abort_unless($identifier, 422, 'Identifier requis');

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::where('identifier', $identifier)->delete();
        OtpCode::create([
            'identifier' => $identifier,
            'code_hash'  => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
        ]);

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            Mail::to($identifier)->send(new OtpCodeMail($code));
        } else {
            // TODO: Intégrer un provider SMS
            logger()->info("OTP {$code} pour {$identifier}");
        }

        // Log DEV utile
        if (app()->environment('local')) {
            logger()->info("DEV OTP for {$identifier}: {$code}");
        }

        return response()->json(['ok' => true]);
    }

    // 2) Vérification de l’OTP (crée le compte s’il n’existe pas encore)
    public function verify(Request $request)
    {
        $identifier = trim((string) $request->input('identifier'));
        $code = trim((string) $request->input('code'));

        $otp = OtpCode::where('identifier', $identifier)->latest()->first();
        abort_unless($otp, 422, 'Aucun OTP demandé.');
        abort_unless(now()->lessThanOrEqualTo($otp->expires_at), 422, 'OTP expiré.');
        abort_unless(Hash::check($code, $otp->code_hash), 422, 'Code incorrect.');

        // Trouve ou crée l’utilisateur
        $user = User::firstOrCreate(
            filter_var($identifier, FILTER_VALIDATE_EMAIL)
                ? ['email' => $identifier]
                : ['phone' => $identifier],
            [
                'name'     => 'Utilisateur',
                'password' => Hash::make(bin2hex(random_bytes(8))),
            ]
        );

        // Email validé si OTP par email
        if ($user->email && $user->email === $identifier) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        // Optionnel: supprimer l’OTP utilisé
        $otp->delete();

        // 🔐 Session Sanctum (SPA cookies)
        Auth::login($user);

        // 🔑 Token API (Bearer) en plus si tu veux t’en servir
        $token = $user->createToken('sa_token')->plainTextToken;

        return response()->json([
            'ok'    => true,
            'token' => $token,
            'user'  => $this->mapUser($user),
        ]);
    }

    public function login(Request $request)
{
    $request->validate([
        'email'    => ['required','email'],
        'password' => ['required','string'],
    ]);

    $email = strtolower(trim($request->input('email')));
    $password = (string) $request->input('password');

    // Récupère l'utilisateur à la main pour éviter les surprises d'Auth::attempt (guard/config)
    $user = \App\Models\User::whereRaw('LOWER(email) = ?', [$email])->first();

    if (!$user || !\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
        // log utile en dev (désactive en prod)
        if (app()->environment('local')) {
            logger()->warning('Login failed', ['email' => $email]);
        }
        return response()->json(['message' => 'Identifiants invalides'], 401);
    }

    // Si email non vérifié : le front doit déclencher OTP
    if (is_null($user->email_verified_at)) {
        return response()->json(['otp_required' => true], 403);
    }

    // Ouvre la session Sanctum + émet un token (si tu t’en sers côté front)
    \Illuminate\Support\Facades\Auth::login($user);              // cookie de session (SPA)
    $token = $user->createToken('sa_token')->plainTextToken;     // bearer facultatif

    return response()->json([
        'ok'    => true,
        'token' => $token,
        'user'  => $this->mapUser($user),
    ]);

    }

    // Profil connecté
    public function me(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        return response()->json(['data' => $this->mapUser($user)]);
    }

    // Déconnexion
    public function logout(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        // Révoquer le token courant s’il existe
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        // Déconnecter la session Sanctum (SPA)
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['ok' => true]);
    }
}
