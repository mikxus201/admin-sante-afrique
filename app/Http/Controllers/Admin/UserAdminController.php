<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserAdminController extends Controller
{
    /**
     * ⚠️ Accès protégé dans routes/web.php via:
     * Route::middleware(['auth','can:manage-users'])->group(...)
     */

    /** Rôles autorisés (adapte si besoin) */
    private function roles(): array
    {
        return ['admin', 'moderator', 'editor', 'viewer'];
    }

    /** Sécurité : pas d’actions dangereuses sur soi-même */
    private function forbidSelf(User $user): void
    {
        if (auth()->id() === $user->id) {
            abort(403, 'Action interdite sur votre propre compte.');
        }
    }

    /* =========================================================
       INDEX — Liste avec filtres “business”
       ========================================================= */
    public function index(Request $r)
    {
        // Recherche : ?q= ou ?search=
        $qText = trim($r->string('q')->toString() ?: $r->string('search')->toString());
        // Filtre rôle : ?role=editor
        $roleFilter = $r->string('role')->toString();
        // Filtre abonnement actif : ?active_only=1
        $activeOnly = $r->boolean('active_only');
        // Filtre plan : ?plan_id=xx (ou ?plan=slug)
        $planId     = $r->integer('plan_id');
        $planSlug   = $r->string('plan')->toString();
        // Filtre pays : ?country=Côte d'Ivoire
        $country    = $r->string('country')->toString();
        // Filtre période d’abonnement courant (dates inclusives) : ?from=YYYY-MM-DD&to=YYYY-MM-DD
        $fromDate   = $r->date('from');
        $toDate     = $r->date('to');

        $users = User::query()
            // Eager-load pour l’index
            ->with([
                'currentSubscription.plan',
            ])
            // Recherche full-text simple
            ->when($qText !== '', function ($qb) use ($qText) {
                $like = "%{$qText}%";
                $qb->where(function ($w) use ($like) {
                    $w->where('name', 'like', $like)
                      ->orWhere('email', 'like', $like)
                      ->orWhere('role', 'like', $like)
                      ->orWhere('nom', 'like', $like)
                      ->orWhere('prenoms', 'like', $like)
                      ->orWhere('phone', 'like', $like);
                });
            })
            // Filtre rôle
            ->when($roleFilter !== '', fn($qb) => $qb->where('role', $roleFilter))
            // Filtre pays
            ->when($country !== '', fn($qb) => $qb->where('country', $country))
            // Filtre "a un abonnement actif"
            ->when($activeOnly, function ($qb) {
                $qb->whereHas('subscriptions', function ($s) {
                    $s->where('status', 'active')->where('ends_at', '>=', now());
                });
            })
            // Filtre plan (id ou slug)
            ->when($planId || $planSlug !== '', function ($qb) use ($planId, $planSlug) {
                $qb->whereHas('subscriptions', function ($s) use ($planId, $planSlug) {
                    $s->where('status', 'active')
                      ->when($planId, fn($x) => $x->where('plan_id', $planId))
                      ->when($planSlug !== '', fn($x) =>
                          $x->whereHas('plan', fn($p) => $p->where('slug', $planSlug))
                      );
                });
            })
            // Filtre période sur l’abonnement en cours
            ->when($fromDate || $toDate, function ($qb) use ($fromDate, $toDate) {
                $qb->whereHas('subscriptions', function ($s) use ($fromDate, $toDate) {
                    $s->where('status', 'active');
                    if ($fromDate) $s->whereDate('starts_at', '>=', $fromDate);
                    if ($toDate)   $s->whereDate('ends_at',   '<=', $toDate);
                });
            })
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        // Pour filtres UI : liste de plans actifs & pays distincts
        $plans = Plan::query()->orderBy('name')->get(['id','name','slug']);
        $countries = User::query()
            ->whereNotNull('country')
            ->select('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        if ($r->expectsJson()) {
            return response()->json([
                'users'     => $users,
                'roles'     => $this->roles(),
                'plans'     => $plans,
                'countries' => $countries,
                'filters'   => [
                    'q'          => $qText,
                    'role'       => $roleFilter,
                    'active_only'=> $activeOnly,
                    'plan_id'    => $planId,
                    'plan'       => $planSlug,
                    'country'    => $country,
                    'from'       => $fromDate,
                    'to'         => $toDate,
                ],
            ]);
        }

        return view('admin.users.index', [
            'users'     => $users,
            'roles'     => $this->roles(),
            'plans'     => $plans,
            'countries' => $countries,
            'search'    => $qText,
            'q'         => $qText,
            'role'      => $roleFilter,
            'active_only' => $activeOnly,
            'plan_id'   => $planId,
            'plan'      => $planSlug,
            'country'   => $country,
            'from'      => $fromDate,
            'to'        => $toDate,
        ]);
    }

    /* =========================================================
       FICHE — Profil + abonnements + factures
       ========================================================= */
    public function show(User $user)
    {
        $user->load([
            'subscriptions.plan' => fn($q) => $q->orderBy('starts_at','desc'),
            'invoices' => fn($q) => $q->orderByDesc('period_to')->orderByDesc('created_at'),
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'user'          => $user,
                'subscriptions' => $user->subscriptions,
                'invoices'      => $user->invoices,
            ]);
        }

        return view('admin.users.show', compact('user'));
    }

    /* =========================================================
       CRUD (inchangé + petites retouches)
       ========================================================= */
    public function create()
    {
        return view('admin.users.form', [
            'u'     => new User(),
            'roles' => $this->roles(),
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'                  => ['required', 'string', 'max:120'],
            'email'                 => ['required', 'email', 'max:190', 'unique:users,email'],
            'role'                  => ['required', Rule::in($this->roles())],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'is_active'             => ['sometimes', 'boolean'],
        ]);

        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = $r->boolean('is_active', true);

        $user = User::create($data);

        if ($r->expectsJson()) {
            return response()->json($user, 201);
        }

        return redirect()->route('admin.users.index')->with('status', 'Utilisateur créé.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', [
            'u'     => $user,
            'roles' => $this->roles(),
        ]);
    }

    public function update(Request $r, User $user)
    {
        $data = $r->validate([
            'name'                  => ['required', 'string', 'max:120'],
            'email'                 => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'role'                  => ['required', Rule::in($this->roles())],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active'             => ['sometimes', 'boolean'],
        ]);

        if (auth()->id() === $user->id) {
            unset($data['role']);
            if ($r->has('is_active') && $r->boolean('is_active') === false) {
                unset($data['is_active']);
            }
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($r->has('is_active')) {
            $data['is_active'] = $r->boolean('is_active');
        }

        $user->update($data);

        if ($r->expectsJson()) {
            return response()->json($user);
        }

        return back()->with('status', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user)
    {
        $this->forbidSelf($user);
        $user->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('status', 'Utilisateur supprimé.');
    }

    /* =========================================================
       Actions rapides
       ========================================================= */
    public function updateRole(Request $r, User $user)
    {
        $this->forbidSelf($user);

        $data = $r->validate([
            'role' => ['required', Rule::in($this->roles())],
        ]);

        $user->role = $data['role'];
        $user->save();

        if ($r->expectsJson()) {
            return response()->json(['ok' => true, 'user' => $user]);
        }

        return back()->with('status', "Rôle mis à jour en « {$user->role} ».");
    }

    public function toggleActive(Request $r, User $user)
    {
        $this->forbidSelf($user);

        if ($r->has('is_active')) {
            $user->is_active = (bool) $r->boolean('is_active');
        } else {
            $user->is_active = ! $user->is_active;
        }
        $user->save();

        if ($r->expectsJson()) {
            return response()->json(['ok' => true, 'is_active' => $user->is_active, 'user' => $user]);
        }

        return back()->with('status', $user->is_active ? 'Utilisateur activé.' : 'Utilisateur désactivé.');
    }

    /* =========================================================
       BONUS : Export CSV des utilisateurs filtrés
       ========================================================= */
    public function exportCsv(Request $r): StreamedResponse
    {
        // On réutilise EXACTEMENT la même logique de filtres que index()
        $r->headers->set('Accept', 'application/json'); // pour récupérer le même set de données si besoin
        // On copie-colle une version simplifiée du builder :
        $builder = User::query()
            ->with(['currentSubscription.plan'])
            ->when($r->filled('q'), function ($qb) use ($r) {
                $qText = trim((string)$r->get('q'));
                $like = "%{$qText}%";
                $qb->where(function ($w) use ($like) {
                    $w->where('name','like',$like)
                      ->orWhere('email','like',$like)
                      ->orWhere('role','like',$like)
                      ->orWhere('nom','like',$like)
                      ->orWhere('prenoms','like',$like);
                });
            })
            ->when($r->filled('role'), fn($qb) => $qb->where('role',$r->get('role')))
            ->when($r->boolean('active_only'), function ($qb) {
                $qb->whereHas('subscriptions', fn($s) => $s->where('status','active')->where('ends_at','>=',now()));
            })
            ->when($r->filled('plan_id') || $r->filled('plan'), function ($qb) use ($r) {
                $planId = (int) $r->get('plan_id');
                $planSlug = (string) $r->get('plan');
                $qb->whereHas('subscriptions', function ($s) use ($planId, $planSlug) {
                    $s->where('status','active')
                      ->when($planId, fn($x) => $x->where('plan_id',$planId))
                      ->when($planSlug !== '', fn($x) => $x->whereHas('plan', fn($p) => $p->where('slug',$planSlug)));
                });
            });

        $rows = $builder->orderBy('id')->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="users-export.csv"',
        ];

        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8 pour Excel
            fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF));
            // Entêtes
            fputcsv($out, ['ID','Nom complet','Email','Téléphone','Pays','Rôle','Abonnement','Début','Fin','Statut']);
            foreach ($rows as $u) {
                $sub = $u->currentSubscription;
                fputcsv($out, [
                    $u->id,
                    $u->full_name ?? $u->display_name,
                    $u->email,
                    $u->phone,
                    $u->country,
                    $u->role,
                    $sub?->plan?->name,
                    optional($sub?->starts_at)->format('Y-m-d'),
                    optional($sub?->ends_at)->format('Y-m-d'),
                    $sub?->status,
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }
}
