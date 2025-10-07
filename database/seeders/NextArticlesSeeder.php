<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NextArticlesSeeder extends Seeder
{
    public function run(): void
    {
        // === Colle tes données Next.js ici (copiées de ton message) ===
        $items = [
            [
                "id" => "a-001",
                "title" => "Présidentielle au Cameroun : Léopold Bessiping, le grand gagnant des recalés",
                "excerpt" => "Ancien enseignant privé de ses arriérés de salaire, il relance sa cause via la présidentielle.",
                "image" => "/articles/cameroun-01.jpg",
                "href" => "/articles/presidentielle-cameroun-bessiping",
                "category" => "dossier",
                "publishedAt" => "2025-09-02T11:35:00Z",
                "featured" => true
            ],
            [
                "id" => "a-002",
                "title" => "Foot : pourquoi l’Olympique lyonnais a choisi Brazzaville",
                "excerpt" => "Le club poursuit sa stratégie africaine avec un nouveau partenariat.",
                "image" => "/articles/lyon-brazzaville.jpg",
                "href" => "/articles/ol-choisit-brazzaville",
                "category" => "actualites",
                "publishedAt" => "2025-09-02T10:45:00Z",
                "featured" => true
            ],
            [
                "id" => "a-003",
                "title" => "À La Marsa, le saccage du magasin Carrefour est-il anti-français ?",
                "excerpt" => "Décryptage des tensions locales et enjeux politiques.",
                "image" => "/articles/marsa-carrefour.jpg",
                "href" => "/articles/marsa-carrefour-incident",
                "category" => "sante-nutrition-infantile",
                "publishedAt" => "2025-09-02T10:05:00Z",
                "featured" => true
            ],
            [
                "id" => "a-004",
                "title" => "RDC : Joseph Kabila met Félix Tshisekedi en garde",
                "excerpt" => "« Tôt ou tard, la supercherie sera évidente », affirme-t-il.",
                "image" => "/articles/rdc-kabila.jpg",
                "href" => "/tribunes/rdc-kabila-tshisekedi",
                "category" => "actualites",
                "publishedAt" => "2025-09-02T09:40:00Z",
                "featured" => true
            ],
            [
                "id" => "d-101",
                "title" => "One Health : penser la santé humaine, animale et environnementale",
                "excerpt" => "Principes, acteurs et politiques publiques sur le continent.",
                "image" => "/articles/dossier-one-health.jpg",
                "href" => "/dossiers/one-health-afrique",
                "category" => "one-health",
                "publishedAt" => "2025-08-25T08:00:00Z",
                "featured" => true
            ],
            [
                "id" => "d-102",
                "title" => "Vaccination en Afrique : mythes, réalités et logistique",
                "excerpt" => "Couvertures, ruptures de stocks et innovations froid-chaud.",
                "image" => "/articles/dossier-vaccination.jpg",
                "href" => "/dossiers/vaccination-afrique",
                "category" => "vaccination",
                "publishedAt" => "2025-08-18T08:00:00Z",
                "featured" => true
            ],
            [
                "id" => "d-103",
                "title" => "Financement de la santé : quelle place pour l’assurance maladie ?",
                "excerpt" => "Panorama des réformes et retours d’expérience.",
                "image" => "/articles/dossier-financement.jpg",
                "href" => "/dossiers/financement-sante",
                "category" => "dossier",
                "publishedAt" => "2025-08-10T08:00:00Z",
                "featured" => true
            ],
            [
                "id" => "i-201",
                "title" => "« L’intelligence artificielle peut renforcer les systèmes de santé »",
                "excerpt" => "Entretien avec Dr. A. Touré, spécialiste santé digitale.",
                "image" => "/articles/itw-toure-ia-sante.jpg",
                "href" => "/interviews/ia-et-systemes-de-sante",
                "category" => "business-sante",
                "publishedAt" => "2025-09-01T14:30:00Z",
                "featured" => true
            ],
            [
                "id" => "i-202",
                "title" => "« Nutrition infantile : agir sur les 1000 premiers jours »",
                "excerpt" => "La priorité selon la pédiatre F. M’Baye.",
                "image" => "/articles/itw-mbaye-nutrition.jpg",
                "href" => "/interviews/nutrition-1000-premiers-jours",
                "category" => "sante-nutrition-infantile",
                "publishedAt" => "2025-08-28T10:00:00Z",
                "featured" => true
            ],
            [
                "id" => "t-301",
                "title" => "Soins primaires : l’urgence d’une approche communautaire",
                "excerpt" => "Opinion : remettre le centre de santé au cœur du village.",
                "image" => "/articles/tribune-soins-primaires.jpg",
                "href" => "/tribunes/soins-primaires-approche-communautaire",
                "category" => "one-health",
                "publishedAt" => "2025-08-30T09:00:00Z",
                "featured" => true
            ],
            [
                "id" => "t-302",
                "title" => "Données de santé : souveraineté et partage responsable",
                "excerpt" => "Comment concilier protection et innovation ?",
                "image" => "/articles/tribune-donnees-sante.jpg",
                "href" => "/tribunes/donnees-souverainete",
                "category" => "les-odd",
                "publishedAt" => "2025-08-20T09:00:00Z",
                "featured" => true
            ],
            [
                "id" => "a-005",
                "title" => "Santé mentale : gérer le stress au quotidien",
                "excerpt" => "Cinq techniques simples validées par la science.",
                "image" => "/articles/stress-quotidien.jpg",
                "href" => "/tribunes/stress-quotidien",
                "category" => "bien-etre-mental",
                "publishedAt" => "2025-09-01T08:00:00Z",
                "featured" => true
            ],
            [
                "id" => "a-006",
                "title" => "Paludisme : une nouvelle campagne de prévention",
                "excerpt" => "Moustiquaires imprégnées et dépistage de masse.",
                "image" => "/articles/paludisme-campagne.jpg",
                "href" => "/articles/paludisme-prevention",
                "category" => "equite-acces-produits-sante",
                "publishedAt" => "2025-08-31T18:10:00Z",
                "featured" => true
            ],
            [
                "id" => "a-007",
                "title" => "Césarienne : mieux informer les futures mamans",
                "excerpt" => "Guide pratique illustré mis à jour 2025.",
                "image" => "/articles/cesarienne-guide.jpg",
                "href" => "/articles/cesarienne-guide",
                "category" => "sante-maternelle",
                "publishedAt" => "2025-08-29T12:00:00Z",
                "featured" => true
            ],
            [
                "id" => "a-008",
                "title" => "Allaitement exclusif : 10 idées reçues à combattre",
                "excerpt" => "Déconstruire les mythes les plus répandus.",
                "image" => "/articles/allaitement-idees-recues.jpg",
                "href" => "/articles/allaitement-idees-recues",
                "category" => "sante-nutrition-infantile",
                "publishedAt" => "2025-08-27T16:30:00Z",
                "featured" => true
            ],
            [
                "id" => "a-009",
                "title" => "Hypertension : quand consulter ?",
                "excerpt" => "Signes d’alerte et parcours de soins recommandé.",
                "image" => "/articles/hypertension-consulter.jpg",
                "href" => "/articles/hypertension-quand-consulter",
                "category" => "Conseils-pratiques",
                "publishedAt" => "2025-08-26T09:20:00Z",
                "featured" => true
            ],
            [
                "id" => "a-010",
                "title" => "Diabète : le point sur les traitements oraux",
                "excerpt" => "Efficacité, tolérance, coûts et accès.",
                "image" => "/articles/diabete-traitements.jpg",
                "href" => "/articles/diabete-traitements-oraux",
                "category" => "equite-acces-produits-sante",
                "publishedAt" => "2025-08-22T11:00:00Z",
                "featured" => true
            ],
            [
                "id" => 101,
                "title" => "Vaccination : répondre aux idées reçues",
                "href" => "/articles/vaccination-idees-recues",
                "image" => "/images/articles/vaccinations.jpg",
                "excerpt" => "Mythes et réalités autour de la vaccination en Afrique.",
                "category" => "vaccination",
                "publishedAt" => "2025-06-22T10:00:00Z",
                "author" => "Rédaction Santé Afrique",
                "body" => [
                    "## Pourquoi vacciner ?",
                    "La vaccination sauve des millions de vies chaque année...",
                    "### Idée reçue n°1 : Les vaccins rendent malades",
                    "Explications, études, chiffres..."
                ]
            ],
        ];

        foreach ($items as $i) {
            $slug = isset($i['href']) ? trim(Str::of($i['href'])->afterLast('/')) : Str::slug($i['title']);
            $thumb = $i['image'] ?? null;
            $publishedAt = $i['publishedAt'] ?? null;

            Article::updateOrCreate(
                ['slug' => $slug],
                [
                    'title'        => $i['title'],
                    'excerpt'      => $i['excerpt'] ?? null,
                    'thumbnail'    => $thumb,
                    'category'     => $i['category'] ?? null,
                    'published_at' => $publishedAt ? date('Y-m-d H:i:s', strtotime($publishedAt)) : null,
                    'featured'     => (bool)($i['featured'] ?? false),
                    'views'        => 0,
                ]
            );
        }
    }
}
