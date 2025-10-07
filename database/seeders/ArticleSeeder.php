<?php

namespace Database\Seeders;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        // 👉 Mets ici TON grand tableau d’articles (ceux de NextArticlesSeeder)
        $items = [
           [
                "id" => "a-001",
                "title" => "Présidentielle au Cameroun : Léopold Bessiping, le grand gagnant des recalés",
                "excerpt" => "Ancien enseignant privé de ses arriérés de salaire, il relance sa cause via la présidentielle.",
                "thumbnail" => "/articles/cameroun-01.jpg",
                "slug" => "presidentielle-cameroun-bessiping",
                "category" => "dossier",
                "published_at" => "2025-09-02 11:35:00",
                "featured" => true,
            ],
            [
                "id" => "a-002",
                "title" => "Foot : pourquoi l’Olympique lyonnais a choisi Brazzaville",
                "excerpt" => "Le club poursuit sa stratégie africaine avec un nouveau partenariat.",
                "thumbnail" => "/articles/lyon-brazzaville.jpg",
                "slug" => "ol-choisit-brazzaville",
                "category" => "actualites",
                "published_at" => "2025-09-02 10:45:00",
                "featured" => true,
            ],
            [
                "id" => "a-003",
                "title" => "À La Marsa, le saccage du magasin Carrefour est-il anti-français ?",
                "excerpt" => "Décryptage des tensions locales et enjeux politiques.",
                "thumbnail" => "/articles/marsa-carrefour.jpg",
                "slug" => "marsa-carrefour-incident",
                "category" => "sante-nutrition-infantile",
                "published_at" => "2025-09-02 10:05:00",
                "featured" => true,
            ],
            [
                "id" => "a-004",
                "title" => "RDC : Joseph Kabila met Félix Tshisekedi en garde",
                "excerpt" => "« Tôt ou tard, la supercherie sera évidente », affirme-t-il.",
                "thumbnail" => "/articles/rdc-kabila.jpg",
                "slug" => "rdc-kabila-tshisekedi",
                "category" => "actualites",
                "published_at" => "2025-09-02 09:40:00",
                "featured" => true,
            ],
            [
                "id" => "d-101",
                "title" => "One Health : penser la santé humaine, animale et environnementale",
                "excerpt" => "Principes, acteurs et politiques publiques sur le continent.",
                "thumbnail" => "/articles/dossier-one-health.jpg",
                "slug" => "one-health-afrique",
                "category" => "one-health",
                "published_at" => "2025-08-25 08:00:00",
                "featured" => true,
            ],
            [
                "id" => "d-102",
                "title" => "Vaccination en Afrique : mythes, réalités et logistique",
                "excerpt" => "Couvertures, ruptures de stocks et innovations froid-chaud.",
                "thumbnail" => "/articles/dossier-vaccination.jpg",
                "slug" => "vaccination-afrique",
                "category" => "vaccination",
                "published_at" => "2025-08-18 08:00:00",
                "featured" => true,
            ],
            [
                "id" => "d-103",
                "title" => "Financement de la santé : quelle place pour l’assurance maladie ?",
                "excerpt" => "Panorama des réformes et retours d’expérience.",
                "thumbnail" => "/articles/dossier-financement.jpg",
                "slug" => "financement-sante",
                "category" => "dossier",
                "published_at" => "2025-08-10 08:00:00",
                "featured" => true,
            ],
            [
                "id" => "i-201",
                "title" => "« L’intelligence artificielle peut renforcer les systèmes de santé »",
                "excerpt" => "Entretien avec Dr. A. Touré, spécialiste santé digitale.",
                "thumbnail" => "/articles/itw-toure-ia-sante.jpg",
                "slug" => "ia-et-systemes-de-sante",
                "category" => "business-sante",
                "published_at" => "2025-09-01 14:30:00",
                "featured" => true,
            ],
            [
                "id" => "i-202",
                "title" => "« Nutrition infantile : agir sur les 1000 premiers jours »",
                "excerpt" => "La priorité selon la pédiatre F. M’Baye.",
                "thumbnail" => "/articles/itw-mbaye-nutrition.jpg",
                "slug" => "nutrition-1000-premiers-jours",
                "category" => "sante-nutrition-infantile",
                "published_at" => "2025-08-28 10:00:00",
                "featured" => true,
            ],
            [
                "id" => "t-301",
                "title" => "Soins primaires : l’urgence d’une approche communautaire",
                "excerpt" => "Opinion : remettre le centre de santé au cœur du village.",
                "thumbnail" => "/articles/tribune-soins-primaires.jpg",
                "slug" => "soins-primaires-approche-communautaire",
                "category" => "one-health",
                "published_at" => "2025-08-30 09:00:00",
                "featured" => true,
            ],
            [
                "id" => "t-302",
                "title" => "Données de santé : souveraineté et partage responsable",
                "excerpt" => "Comment concilier protection et innovation ?",
                "thumbnail" => "/articles/tribune-donnees-sante.jpg",
                "slug" => "donnees-souverainete",
                "category" => "les-odd",
                "published_at" => "2025-08-20 09:00:00",
                "featured" => true,
            ],
            [
                "id" => "a-005",
                "title" => "Santé mentale : gérer le stress au quotidien",
                "excerpt" => "Cinq techniques simples validées par la science.",
                "thumbnail" => "/articles/stress-quotidien.jpg",
                "slug" => "stress-quotidien",
                "category" => "bien-etre-mental",
                "published_at" => "2025-09-01 08:00:00",
                "featured" => true,
            ],
            [
                "id" => "a-006",
                "title" => "Paludisme : une nouvelle campagne de prévention",
                "excerpt" => "Moustiquaires imprégnées et dépistage de masse.",
                "thumbnail" => "/articles/paludisme-campagne.jpg",
                "slug" => "paludisme-prevention",
                "category" => "equite-acces-produits-sante",
                "published_at" => "2025-08-31 18:10:00",
                "featured" => true,
            ],
            [
                "id" => "a-007",
                "title" => "Césarienne : mieux informer les futures mamans",
                "excerpt" => "Guide pratique illustré mis à jour 2025.",
                "thumbnail" => "/articles/cesarienne-guide.jpg",
                "slug" => "cesarienne-guide",
                "category" => "sante-maternelle",
                "published_at" => "2025-08-29 12:00:00",
                "featured" => true,
            ],
            [
                "id" => "a-008",
                "title" => "Allaitement exclusif : 10 idées reçues à combattre",
                "excerpt" => "Déconstruire les mythes les plus répandus.",
                "thumbnail" => "/articles/allaitement-idees-recues.jpg",
                "slug" => "allaitement-idees-recues",
                "category" => "sante-nutrition-infantile",
                "published_at" => "2025-08-27 16:30:00",
                "featured" => true,
            ],
            [
                "id" => "a-009",
                "title" => "Hypertension : quand consulter ?",
                "excerpt" => "Signes d’alerte et parcours de soins recommandé.",
                "thumbnail" => "/articles/hypertension-consulter.jpg",
                "slug" => "hypertension-quand-consulter",
                "category" => "conseils-pratiques",
                "published_at" => "2025-08-26 09:20:00",
                "featured" => true,
            ],
            [
                "id" => "a-010",
                "title" => "Diabète : le point sur les traitements oraux",
                "excerpt" => "Efficacité, tolérance, coûts et accès.",
                "thumbnail" => "/articles/diabete-traitements.jpg",
                "slug" => "diabete-traitements-oraux",
                "category" => "equite-acces-produits-sante",
                "published_at" => "2025-08-22 11:00:00",
                "featured" => true,
            ],

            // ----- Les 2 articles "riches" -----
            [
                "id" => 42,
                "title" => "Vaccination : répondre aux idées reçues",
                "excerpt" => "Mythes et réalités autour de la vaccination en Afrique.",
                "thumbnail" => "/images/articles/vaccination.jpg",
                "slug" => "vaccination-idees-recues-1",
                "category" => "vaccination",
                "published_at" => "2025-05-07 09:00:00",
                "featured" => true,
                "author" => "Rédaction Santé Afrique",
                "author_avatar" => "/images/authors/redaction.jpg",
                "author_bio" => "Collectif de journalistes & experts santé.",
                "tags" => ["vaccination", "pédiatrie", "prévention"],
                "sources" => [
                    ["label" => "OMS — Immunization", "url" => "https://www.who.int/health-topics/vaccines-and-immunization"],
                    "https://www.unicef.org/health/immunization",
                    "Revue médicale Santé Publique, 2024"
                ],
                "body" => "Introduction...\n\nSous-titre 1...\nParagraphe...\n\nSous-titre 2...\nParagraphe..."
            ],
            [
                "id" => 101,
                "title" => "Vaccination : répondre aux idées reçues",
                "excerpt" => "Mythes et réalités autour de la vaccination en Afrique.",
                "thumbnail" => "/images/articles/vaccination.jpg",
                "slug" => "vaccination-idees-recues-2",
                "category" => "vaccination",
                "published_at" => "2025-06-22 10:00:00",
                "featured" => true,
                "author" => "Rédaction Santé Afrique",
                "author_avatar" => "/images/authors/redaction.jpg",
                "author_bio" => "Collectif de journalistes & experts santé.",
                "tags" => ["vaccination", "pédiatrie", "prévention"],
                "sources" => [
                    ["label" => "OMS — Immunization", "url" => "https://www.who.int/health-topics/vaccines-and-immunization"],
                    "https://www.unicef.org/health/immunization",
                    "Revue médicale Santé Publique, 2024"
                ],
                "body" => "## Pourquoi vacciner ?\nLa vaccination sauve des millions de vies chaque année...\n### Idée reçue n°1 : Les vaccins rendent malades\nExplications, études, chiffres..."
            ],
        ];

        foreach ($items as $raw) {
            $data = $this->normalize($raw);

            // Upsert par slug (clé “humaine” stable)
            Article::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }

    /**
     * Normalise une entrée “Next.js” vers notre schéma Laravel.
     */
    private function normalize(array $raw): array
    {
        // 1) Ne JAMAIS toucher à la clé primaire auto-incrémentée
        unset($raw['id']);

        // 2) Slug : priorité à 'slug', sinon on dérive depuis 'href' ou 'title'
        $slug = $raw['slug'] ?? null;
        if (!$slug) {
            if (!empty($raw['href'])) {
                $slug = Str::of($raw['href'])->afterLast('/')->slug();
            } else {
                $slug = Str::slug($raw['title'] ?? Str::random(8));
            }
        }

        // 3) Thumbnail / image
        $thumbnail = $raw['thumbnail'] ?? ($raw['image'] ?? null);

        // 4) Dates
        $publishedAtIso = $raw['publishedAt'] ?? ($raw['published_at'] ?? null);
        $publishedAt = $publishedAtIso ? Carbon::parse($publishedAtIso) : null;

        // 5) Champs simples
        $title     = $raw['title'] ?? '';
        $excerpt   = $raw['excerpt'] ?? null;
        $category  = $raw['category'] ?? null;
        $author    = $raw['author'] ?? 'Rédaction Santé Afrique';
        $views     = (int)($raw['views'] ?? 0);
        $featured  = !empty($raw['featured']) ? 1 : 0;

        // 6) Tableaux → JSON (SQLite stocke en TEXT)
        $tags     = array_values($raw['tags'] ?? []);     // toujours un array
        $sources  = array_values($raw['sources'] ?? []);  // toujours un array

        return [
            'title'        => $title,
            'slug'         => $slug,
            'excerpt'      => $excerpt,
            'category'     => $category,
            'thumbnail'    => $thumbnail,
            'author'       => $author,
            'views'        => $views,
            'featured'     => $featured,
            'published_at' => $publishedAt,

            // stockés comme JSON (TEXT en SQLite) ; les casts du modèle les re-décodent
            'tags'         => json_encode($tags, JSON_UNESCAPED_UNICODE),
            'sources'      => json_encode($sources, JSON_UNESCAPED_UNICODE),
        ];
    }
}
