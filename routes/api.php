<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Front data
use App\Http\Controllers\Front\ArticleController;

// Public data API
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\IssueController as ApiIssueController;
use App\Http\Controllers\Api\RubricController  as ApiRubricController;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\PaymentController;

// Admin API (back-office)
use App\Http\Controllers\Admin\PlanAdminController;
use App\Http\Controllers\HelpBlockController;

// “Mon compte”
use App\Http\Controllers\Api\MeController;

/* -------------------------------------------------------
 | ⚠️ IMPORTANT
 | Toute l’authentification (register/login/OTP/logout)
 | vit désormais dans routes/web.php (middleware "web").
 | Ici, on garde surtout les endpoints API “données”.
 * ------------------------------------------------------*/

/* ---------------------------
 | PAIEMENT (PUBLIC)
 * --------------------------- */
Route::post('/subscribe-request', [PaymentController::class, 'init']);

/* ---------------------------
 | ARTICLES (PUBLIC)
 * --------------------------- */
Route::get('/articles',                 [ArticleController::class, 'index'])->name('api.articles.index');
Route::get('/articles/slug/{slug}',     [ArticleController::class, 'apiShowSlug'])->name('api.articles.show-by-slug');
Route::get('/articles/{slug}',          [ArticleController::class, 'showBySlug'])->name('api.articles.show');

/* ---------------------------
 | AUTEURS (PUBLIC)
 * --------------------------- */
Route::get('/authors',              [AuthorController::class, 'index']);
Route::get('/authors/slug/{slug}',  [AuthorController::class, 'showBySlug']);
Route::get('/authors/{id}',         [AuthorController::class, 'show']);

/* ---------------------------
 | MAGAZINE / ISSUES (PUBLIC)
 * --------------------------- */
Route::get('/issues',         [ApiIssueController::class, 'index']);
Route::get('/issues/{issue}', [ApiIssueController::class, 'show']);

/* ---------------------------
 | RUBRIQUES (PUBLIC)
 * --------------------------- */
Route::get('/rubrics',                 [ApiRubricController::class, 'index']);
Route::get('/rubrics/{slug}',          [ApiRubricController::class, 'show']);
Route::get('/rubrics/{slug}/articles', [ApiRubricController::class, 'articles']);

/* ---------------------------
 | CATEGORIES (PUBLIC)
 * --------------------------- */
Route::get('/categories',                 [ApiCategoryController::class, 'index']);
Route::get('/categories/{slug}',          [ApiCategoryController::class, 'show']);
Route::get('/categories/{slug}/articles', [ApiCategoryController::class, 'articles']);

/* ---------------------------
 | PLANS (PUBLIC, LECTURE)
 * --------------------------- */
Route::get('/plans',        [PlanController::class, 'index']);
Route::get('/plans/{slug}', [PlanController::class, 'show']);

/* ---------------------------
 | HELP (PUBLIC, LECTURE)
 * --------------------------- */
Route::get('/help/subscribe', [HelpController::class, 'subscribe']);
// Ancienne implémentation :
// Route::get('/help/subscribe', [HelpBlockController::class, 'subscribe']);

/* ---------------------------
 | AUTH PUBLIC : /api/auth/me
 | -> Démarre la session "web" pour lire le cookie laravel_session
 | -> Jamais de redirection, toujours JSON
 * --------------------------- */
Route::middleware('web')->get('/auth/me', function (Request $r) {
    // Grâce à 'web', la session est démarrée : Auth::user() fonctionne
    $u = Auth::user();

    if (!$u) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    // Injecte l'utilisateur résolu et délègue au contrôleur qui formate le JSON
    $r->setUserResolver(fn () => $u);
    return app(\App\Http\Controllers\Api\MeController::class)->me($r);
})->name('api.auth.me');

/* ---------------------------
 | ADMIN API (PROTÉGÉE SANCTUM)
 * --------------------------- */
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get   ('plans',                  [PlanAdminController::class, 'index']);
    Route::post  ('plans',                  [PlanAdminController::class, 'store']);
    Route::put   ('plans/{plan}',           [PlanAdminController::class, 'update']);
    Route::delete('plans/{plan}',           [PlanAdminController::class, 'destroy']);
    Route::patch ('plans/{plan}/publish',   [PlanAdminController::class, 'publish']);

    // Help blocks upsert
    Route::post('help/subscribe', [HelpBlockController::class, 'upsert']);

    // Paiement (stub)
    Route::post('pay/init', [PaymentController::class, 'init']);
});

/* ---------------------------
 | MON COMPTE (PROTÉGÉ SANCTUM)
 | -> Accès via cookie stateful OU token Bearer
 * --------------------------- */
Route::middleware('auth:sanctum')->group(function () {
    // Profil (version protégée)
    Route::get ('/me', [MeController::class, 'me'])->name('api.me');
    Route::post('/me', [MeController::class, 'update'])->name('api.me.update');

    // Factures
    Route::get('/me/invoices',          [MeController::class, 'invoices'])->name('api.me.invoices');
    Route::get('/me/invoices/{id}/pdf', [MeController::class, 'invoicePdf'])->name('api.me.invoices.pdf');

    // Newsletters (par thèmes)
    Route::get ('/me/newsletters',        [MeController::class, 'newsletterIndex'])->name('api.me.newsletters.index');
    Route::post('/me/newsletters/toggle', [MeController::class, 'newsletterToggleTopic'])->name('api.me.newsletters.toggle');
});
