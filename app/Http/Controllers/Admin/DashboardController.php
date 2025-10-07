<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Issue;
use App\Models\Plan;
use App\Models\Rubric;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $now      = Carbon::now();
        $window30 = $now->copy()->subDays(30);

        // ====== ARTICLES ======
        $articlesTotal          = Article::count();
        $articlesPublishedTotal = Article::whereNotNull('published_at')->count();
        $articlesPublished30    = Article::whereNotNull('published_at')->where('published_at', '>=', $window30)->count();
        $articlesDrafts         = Article::whereNull('published_at')->count();
        $articlesFeatured       = Article::where('is_featured', true)->count();

        // ====== RUBRIQUES ======
        $rubricsTotal  = Rubric::count();
        $rubricsActive = Rubric::where('is_active', true)->count();

        // ====== NUMÉROS ======
        $issuesTotal     = Issue::count();
        $issuesPublished = Issue::where('is_published', true)->count();
        $issuesLast6m    = Issue::where('created_at', '>=', $now->copy()->subMonths(6))->count();

        // ====== UTILISATEURS ======
        $usersTotal = User::count();
        $usersNew30 = User::where('created_at', '>=', $window30)->count();

        // ----- STATISTIQUES UTILISÉES PAR LA VUE -----
        $stats = [
            'articles_total'            => $articlesTotal,
            'articles_published_total'  => $articlesPublishedTotal,
            'articles_published_30'     => $articlesPublished30,
            'articles_drafts'           => $articlesDrafts,
            'articles_featured'         => $articlesFeatured,

            'rubrics_total'             => $rubricsTotal,
            'rubrics_active'            => $rubricsActive,

            'issues_total'              => $issuesTotal,
            'issues_published'          => $issuesPublished,

            'users_total'               => $usersTotal,
            'users_new_30'              => $usersNew30,
        ];

        // ====== ABONNEMENTS (robuste au schéma) ======
        $subsStats = [
            'table'      => null,
            'total'      => 0,
            'active'     => null,   // null si pas de colonne d'état
            'ending_30'  => null,   // null si pas de colonne de fin
            'expired'    => null,   // idem
            'per_plan'   => [],
        ];

        // Détection de la table d'abonnement
        $subsTable = collect(['subscriptions', 'abonnements', 'user_subscriptions', 'mag_subscriptions'])
            ->first(fn ($t) => Schema::hasTable($t));

        if ($subsTable) {
            $subsStats['table'] = $subsTable;

            $total = DB::table($subsTable)->count();
            $subsStats['total'] = (int) $total;

            // Colonnes possibles
            $has_is_active = Schema::hasColumn($subsTable, 'is_active');
            $has_status    = Schema::hasColumn($subsTable, 'status');
            $has_etat      = Schema::hasColumn($subsTable, 'etat');

            $dateCol = collect(['expires_at', 'end_at', 'date_fin', 'ends_at'])
                ->first(fn ($c) => Schema::hasColumn($subsTable, $c));

            // Filtre "actif" si dispo
            $canFilterActive = $has_is_active || $has_status || $has_etat;
            if ($canFilterActive) {
                $subsStats['active'] = (int) DB::table($subsTable)
                    ->where(function ($q) use ($has_is_active, $has_status, $has_etat) {
                        if ($has_is_active) { $q->orWhere('is_active', 1); }
                        if ($has_status)    { $q->orWhereIn('status', ['active','actif','ACTIVE','ACTIF']); }
                        if ($has_etat)      { $q->orWhereIn('etat',   ['active','actif','ACTIVE','ACTIF']); }
                    })->count();
            }

            // Échéances si colonne de fin présente
            if ($dateCol) {
                // Échéance ≤ 30 jours (si possible on filtre sur actifs)
                $endingQuery = DB::table($subsTable)->whereBetween($dateCol, [$now, $now->copy()->addDays(30)]);
                if ($canFilterActive) {
                    $endingQuery->where(function ($q) use ($has_is_active, $has_status, $has_etat) {
                        if ($has_is_active) { $q->orWhere('is_active', 1); }
                        if ($has_status)    { $q->orWhereIn('status', ['active','actif','ACTIVE','ACTIF']); }
                        if ($has_etat)      { $q->orWhereIn('etat',   ['active','actif','ACTIVE','ACTIF']); }
                    });
                }
                $subsStats['ending_30'] = (int) $endingQuery->count();

                // Expirés
                $subsStats['expired'] = (int) DB::table($subsTable)
                    ->where($dateCol, '<', $now)
                    ->count();
            }

            // Répartition par offre
            $planCol = collect(['plan_id', 'offer_id', 'plan_slug', 'plan'])
                ->first(fn ($c) => Schema::hasColumn($subsTable, $c));

            if ($planCol) {
                $totals = DB::table($subsTable)
                    ->select([$planCol . ' as plan_key', DB::raw('COUNT(*) as total')])
                    ->groupBy($planCol)
                    ->get()
                    ->keyBy('plan_key');

                $actives = collect();
                if ($canFilterActive) {
                    $actives = DB::table($subsTable)
                        ->where(function ($q) use ($has_is_active, $has_status, $has_etat) {
                            if ($has_is_active) { $q->orWhere('is_active', 1); }
                            if ($has_status)    { $q->orWhereIn('status', ['active','actif','ACTIVE','ACTIF']); }
                            if ($has_etat)      { $q->orWhereIn('etat',   ['active','actif','ACTIVE','ACTIF']); }
                        })
                        ->select([$planCol . ' as plan_key', DB::raw('COUNT(*) as active')])
                        ->groupBy($planCol)
                        ->get()
                        ->keyBy('plan_key');
                }

                // Mappage clé -> nom d’offre (si table plans dispo)
                $planMap = collect();
                if (Schema::hasTable('plans')) {
                    if (in_array($planCol, ['plan_id', 'offer_id'], true)) {
                        $planMap = Plan::pluck('name', 'id');
                    } else { // plan_slug | plan
                        // Si 'plan' stocke le slug, on mappe sur slug ; sinon libellé brut gardé tel quel
                        $planMap = Plan::pluck('name', 'slug');
                    }
                }

                $subsStats['per_plan'] = $totals->map(function ($row) use ($actives, $planMap) {
                    $key    = (string) $row->plan_key;
                    $name   = $planMap[$key] ?? $key;
                    $active = $actives->has($key) ? (int) $actives[$key]->active : null;

                    return [
                        'plan'   => $name,
                        'total'  => (int) $row->total,
                        'active' => $active,
                    ];
                })->values()->all();
            }
        }

        // ====== KPIs (cartes haut) ======
        $kpis = [
            'users' => [
                'title'  => 'Utilisateurs',
                'value'  => $usersTotal,
                'subtle' => '+' . $usersNew30 . ' sur 30 jours',
            ],
            'articles_published_30' => [
                'title'  => 'Articles publiés (30 j)',
                'value'  => $articlesPublished30,
                'subtle' => $articlesPublishedTotal . ' au total',
            ],
            'articles_total' => [
                'title'  => 'Articles au total',
                'value'  => $articlesTotal,
                'subtle' => $articlesDrafts . ' brouillons',
            ],
            'issues_total' => [
                'title'  => 'Numéros du magazine',
                'value'  => $issuesTotal,
                'subtle' => '+' . $issuesLast6m . ' sur 6 mois',
            ],
        ];

        // KPI Abonnements si une table existe (même avec 0)
        if ($subsStats['table']) {
            $kpis['subscriptions'] = [
                'title'  => 'Abonnements',
                'value'  => $subsStats['total'],
                'subtle' => $subsStats['active'] !== null ? ($subsStats['active'] . ' actifs') : '—',
            ];
        }

        // ====== LISTES ======
        $latestPlans = Schema::hasTable('plans')
            ? Plan::latest('created_at')->limit(5)->get(['id','name','price_fcfa','is_published'])
            : collect();

        // ====== LISTES ======
$lists = ['latest_articles' => Article::orderByDesc('published_at')->orderByDesc('id')->take(8)
        ->get(['id','slug','title','published_at','is_featured']), // << ajouter 'slug'
         'latest_issues'   => Issue::latest('created_at')->take(6)->get(['id','number','date','is_published']),
         'latest_rubrics'  => Rubric::latest('created_at')->take(8)->get(['id','name','slug','is_active','created_at']),
         'latest_plans'    => Plan::latest('created_at')->limit(5)->get(['id','name','price_fcfa','is_published']),
         'subs_per_plan'   => $subsStats['per_plan'],
];

        return view('admin.dashboard', compact('kpis', 'stats', 'lists', 'subsStats'));
    }
}
