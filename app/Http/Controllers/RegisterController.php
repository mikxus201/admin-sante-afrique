<?php
// app/Http/Controllers/RegisterController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        try {
            // 1) Validation souple : name OU (nom/prenoms)
            $data = $request->validate([
                'email'    => ['required','email','max:255','unique:users,email'],
                'password' => ['required','string','min:8'],
                'phone'    => ['nullable','string','max:50'],
                'name'     => ['nullable','string','max:255'],
                'nom'      => ['nullable','string','max:255'],
                'prenoms'  => ['nullable','string','max:255'],
            ]);

            // 2) Fabriquer un display name robuste
            $display = trim((string)($data['name'] ?? ''));
            if ($display === '') {
                $display = trim(implode(' ', array_filter([
                    $data['prenoms'] ?? null,
                    $data['nom'] ?? null,
                ])));
            }
            if ($display === '') {
                $display = ucfirst(strtok($data['email'], '@')) ?: 'Utilisateur';
            }

            // 3) Créer l’utilisateur (⚠️ pas de role/status ici)
            $user = User::create([
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'phone'    => $data['phone']    ?? null,
                'name'     => $display,
                'nom'      => $data['nom']      ?? null,
                'prenoms'  => $data['prenoms']  ?? null,
            ]);

            // 4) Réponse normalisée
            return response()->json([
                'ok'   => true,
                'user' => [
                    'id'      => $user->id,
                    'email'   => $user->email,
                    'phone'   => $user->phone,
                    'name'    => $user->name,
                    'nom'     => $user->nom,
                    'prenoms' => $user->prenoms,
                ],
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            // Aide à diagnostiquer une colonne manquante etc.
            return response()->json([
                'ok' => false,
                'message' => 'Database error',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Unexpected error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
