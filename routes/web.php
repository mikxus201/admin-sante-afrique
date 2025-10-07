<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Admin controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ArticleAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\AuthorAdminController;
use App\Http\Controllers\Admin\IssueAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\HelpItemWebController;
use App\Http\Controllers\Admin\PlanWebController;
use App\Http\Controllers\Admin\RubricAdminController;
use App\Http\Controllers\Admin\ArticleRubricController;
use App\Http\Controllers\Payments\CinetpayController;
use App\Http\Controllers\InvoicePdfController;

// Front
use App\Http\Controllers\Front\ArticleController as FrontArticleController;
use App\Http\Controllers\MagazineProxyController;

/* ========= PUBLIC ========= */

Route::get('/', fn () => view('welcome'))->name('home');

Route::controller(FrontArticleController::class)->group(function () {
    Route::get('/articles',        'index')->name('articles.index');
    Route::get('/articles/{slug}', 'show')->name('articles.show');
});

Route::get('/rubrique/{slug}', function ($slug, Request $r) {
    // On délègue à l’index avec ?rubric=slug
    $r->merge(['rubric' => $slug]);
    return app(FrontArticleController::class)->index($r);
})->name('rubrics.show');


// Magazine → Next.js proxy
Route::controller(MagazineProxyController::class)->group(function () {
    Route::get('/magazine',      'index')->name('magazine.index');
    Route::get('/magazine/{id}', 'show')->name('magazine.show');
});


/* ========= AUTH SPA (Next.js) =========
   IMPORTANT : on laisse ces endpoints sous middleware "web"
   pour bénéficier de la session + CSRF (Sanctum SPA).
   - Front-end : faire GET /sanctum/csrf-cookie puis POST /api/auth/login
   - Déconnexion : POST /api/auth/logout
*/
Route::middleware('web')->group(function () {
    // Login SPA (JSON, pas de redirection)
    Route::post('/api/auth/login', function (Request $r) {
        $cred = $r->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($cred, $r->boolean('remember'))) {
            $r->session()->regenerate();
            // 204 No Content = succès sans payload
            return response()->noContent();
        }

        // 422 = échec validation/identifiants (plus parlant que 401 ici)
        return response()->json(['message' => 'Identifiants invalides'], 422);
    })->name('spa.login');

    // Logout SPA (JSON, pas de redirection)
    Route::post('/api/auth/logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->noContent();
    })->name('spa.logout');
});


/* ========= AUTH (ADMIN LOGIN LÉGER avec vues) ========= */

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', fn () => view('admin.auth.login'))->name('admin.login');

    Route::post('/admin/login', function (Request $r) {
        $cred = $r->validate(['email' => 'required|email', 'password' => 'required']);
        if (Auth::attempt($cred, $r->boolean('remember'))) {
            $r->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }
        return back()->withErrors(['email' => 'Identifiants invalides'])->onlyInput('email');
    })->name('admin.login.post');
});

// Déconnexion (version web avec redirection vers login admin)
Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->guest(route('admin.login'));
})->name('logout');

// alias si quelqu’un appelle route('login')
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

// raccourci /dashboard → admin
Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))->name('dashboard');


/* ========= ZONE ADMIN ========= */

Route::middleware(['auth'])->prefix('admin')->as('admin.')->group(function () {
    // Tableau de bord
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Articles (ressource REST)
    Route::resource('articles', ArticleAdminController::class)->except('show');

    // --- Compat legacy : /admin/articles/edit/{id|slug} -> redirige vers la bonne URL ---
    Route::get('articles/edit/{key}', function (string $key) {
        $article = \App\Models\Article::query()->when(
            is_numeric($key),
            fn($q) => $q->whereKey($key),
            fn($q) => $q->where('slug', $key)
        )->firstOrFail();

        return redirect()->route('admin.articles.edit', $article);
    })->where('key', '.*')->name('articles.edit.legacy');

    // Catégories (+ actions groupées + toggle)
    Route::resource('categories', CategoryAdminController::class)->except('show');
    Route::post('categories/{category}/toggle', [CategoryAdminController::class, 'toggle'])->name('categories.toggle');
    Route::post('categories/bulk',            [CategoryAdminController::class, 'bulk'])->name('categories.bulk');

    // Rubriques (+ toggle)
    Route::resource('rubrics', RubricAdminController::class)->except('show');
    Route::post('rubrics/{rubric}/toggle', [RubricAdminController::class, 'toggle'])->name('rubrics.toggle');

    // Auteurs (+ toggle)
    Route::resource('authors', AuthorAdminController::class)->except('show');
    Route::post('authors/{author}/toggle', [AuthorAdminController::class, 'toggle'])->name('authors.toggle');

    // Numéros (magazines)
    Route::resource('issues', IssueAdminController::class)->except('show');

    // Offres d’abonnement (+ publier)
    Route::resource('plans', PlanWebController::class)->except('show');
    Route::post('plans/{plan}/publish', [PlanWebController::class, 'publish'])->name('plans.publish');

    // Besoin d’aide (+ publier + déplacer)
    Route::resource('help-items', HelpItemWebController::class)->except('show');
    Route::post('help-items/{helpItem}/publish', [HelpItemWebController::class, 'publish'])->name('help-items.publish');
    Route::post('help-items/{helpItem}/up',      [HelpItemWebController::class, 'moveUp'])->name('help-items.up');
    Route::post('help-items/{helpItem}/down',    [HelpItemWebController::class, 'moveDown'])->name('help-items.down');

    // Utilisateurs (protégé par la capacité "manage-users")
    Route::middleware('can:manage-users')->group(function () {
    // Ressource sans "show" (on gère show séparément pour éviter tout conflit)
    Route::resource('users', UserAdminController::class)->except('show');

    // FICHE utilisateur (profil + abonnements + factures)
    Route::get('users/{user}', [UserAdminController::class, 'show'])->name('users.show');

    // Actions rapides (PATCH sémantiquement correct) — garde aussi POST pour compat ascendante si tu veux
    Route::patch('users/{user}/role',   [UserAdminController::class, 'updateRole'])->name('users.role');
    Route::patch('users/{user}/toggle', [UserAdminController::class, 'toggleActive'])->name('users.toggle');

    // (Optionnel) Compat anciennes intégrations en POST
    // Route::post('users/{user}/role',   [UserAdminController::class, 'updateRole'])->name('users.role.post');
    // Route::post('users/{user}/toggle', [UserAdminController::class, 'toggleActive'])->name('users.toggle.post');

    // (Bonus) Export CSV des utilisateurs filtrés (mêmes filtres que l'index)
    Route::get('users/export/csv', [UserAdminController::class, 'exportCsv'])->name('users.export.csv');
    });


    // Affectation de rubrique à un article
    Route::get('articles/{article}/rubric',  [ArticleRubricController::class, 'edit'])->name('articles.rubric.edit');
    Route::put('articles/{article}/rubric',  [ArticleRubricController::class, 'update'])->name('articles.rubric.update');
});

Route::middleware(['web','auth'])->group(function () {
    // Alias : /profile → redirige vers l’édition du user connecté dans l’admin
    Route::get('/profile', function () {
        return redirect()->route('admin.users.edit', auth()->user());
    })->name('profile.edit');
});


// ⚠️ IMPORTANT : bien passer un tableau [Classe::class, 'méthode']
Route::match(['get','post'], '/pay/cinetpay/start',  [CinetpayController::class, 'start'])->name('pay.cinetpay.start');
Route::match(['get','post'], '/pay/cinetpay/return', [CinetpayController::class, 'return'])->name('pay.cinetpay.return');
Route::match(['get','post'], '/pay/cinetpay/notify', [CinetpayController::class, 'notify'])->name('pay.cinetpay.notify');


Route::middleware(['auth'])->group(function () {
    Route::get('/invoices/{invoice}/pdf', [InvoicePdfController::class, 'show'])->name('invoices.pdf');
});



/* ========= DEBUG ========= */

Route::get('/debug-session', function (Request $r) {
    $r->session()->put('foo', 'bar');
    return response()->json([
        'cookie_name' => config('session.cookie'),
        'session_id'  => session()->getId(),
        'has_cookie'  => isset($_COOKIE[config('session.cookie')]),
        'user_id'     => optional(auth()->user())->id,
    ]);
});
