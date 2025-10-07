<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /** Vue Blade (si utilisée côté admin) */
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    /**
     * API: mise à jour du profil connecté (JSON)
     * Attendu côté front: { nom?, prenoms?, phone?, gender?, country?, avatar_url?, email? }
     */
    public function updateMe(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        abort_unless($user, 401, 'Unauthenticated');

        $data = $request->validate([
            'nom'        => ['sometimes', 'nullable', 'string', 'max:120'],
            'prenoms'    => ['sometimes', 'nullable', 'string', 'max:120'],
            'phone'      => ['sometimes', 'nullable', 'string', 'max:40'],
            'gender'     => ['sometimes', 'nullable', Rule::in(['M', 'Mme'])],
            'country'    => ['sometimes', 'nullable', 'string', 'max:120'],
            'avatar_url' => ['sometimes', 'nullable', 'string', 'max:255'],
            // facultatif: autoriser le changement d’email (on invalide la vérif)
            'email'      => ['sometimes', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        // Appliquer les champs simples
        foreach (['nom','prenoms','phone','gender','country','avatar_url'] as $k) {
            if ($request->has($k)) {
                $user->{$k} = $data[$k] ?? null;
            }
        }

        // Changement d'email => on invalide la vérification jusqu’à revalidation
        if ($request->has('email') && isset($data['email']) && $data['email'] !== $user->email) {
            $user->email = $data['email'];
            $user->email_verified_at = null;
        }

        // Maintenir un "name" cohérent si non renseigné explicitement ailleurs
        // (exploité par le front pour afficher les initiales)
        if (empty($user->name)) {
            $full = trim(implode(' ', array_filter([$user->prenoms, $user->nom])));
            if ($full !== '') {
                $user->name = $full;
            }
        }

        $user->save();

        // Normaliser la réponse front
        $payload = [
            'id'         => $user->id,
            'email'      => $user->email,
            'phone'      => $user->phone,
            'nom'        => $user->nom,
            'prenoms'    => $user->prenoms,
            'gender'     => $user->gender,
            'country'    => $user->country,
            'avatar_url' => $user->avatar_url,
            'name'       => $user->name ?: trim(implode(' ', array_filter([$user->prenoms, $user->nom]))) ?: 'Utilisateur',
            'created_at' => $user->created_at,
        ];

        return response()->json(['data' => $payload]);
    }

    public function subscription(Request $request)
{
    $user = $request->user();

    // Réponse par défaut : pas d’abonnement
    $payload = [
        'status'     => 'none',   // 'active' | 'expired' | 'none'
        'plan'       => null,     // ['id'=>..., 'name'=>..., 'description'=>...]
        'started_at' => null,
        'ends_at'    => null,
    ];

    // Si tu as une relation invoices() (tu l'as dans User), on déduit un "abonnement"
    try {
        /** @var \Illuminate\Database\Eloquent\Model|null $last */
        $last = $user->invoices()
            ->orderByDesc('period_to')
            ->orderByDesc('created_at')
            ->first();

        if ($last) {
            $started = $last->period_from ?? null;
            $ends    = $last->period_to   ?? null;

            // Nom du plan : on prend ce qu'on trouve
            $planName = $last->plan_name
                ?? ($last->plan->name ?? null)   // si relation plan() existe
                ?? 'Abonnement';

            $payload['plan'] = [
                'id'          => $last->plan_id   ?? null,
                'name'        => $planName,
                'description' => $last->plan_description ?? null,
            ];
            $payload['started_at'] = $started;
            $payload['ends_at']    = $ends;

            // Statut
            if ($last->status === 'paid') {
                // Si on a une date de fin et qu'elle est passée => expiré
                if ($ends && now()->gt($ends)) {
                    $payload['status'] = 'expired';
                } else {
                    $payload['status'] = 'active';
                }
            } elseif ($last->status === 'canceled') {
                $payload['status'] = 'none';
            } else {
                // unknown/unpaid => on laisse "none"
            }
        }
    } catch (\Throwable $e) {
        // en dev tu peux logger si besoin
        // logger()->warning('subscription inference failed', ['err' => $e->getMessage()]);
    }

    return response()->json(['data' => $payload]);
}

    /** Supprime le compte (protégé par mot de passe courant) */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
