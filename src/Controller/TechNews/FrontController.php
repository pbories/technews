<?php

namespace App\Controller\TechNews;


use App\Article\Provider\YamlProvider;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Membre;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends Controller
{
    /**
     * Page d'acceuil de notre site internet
     */
    public function index()
    {
        // Récupération des articles depuis le YamlProvider
        //$articles = $yamlProvider->getArticles();

        $repository = $this->getDoctrine()
            ->getRepository(Article::class);

        $articles = $repository->findBy([], ['id' => 'DESC']);
        $spotlight = $repository->findSpotlightArticles();

        // return new Response("<html><body><h1>TechNews, coming soon...</h1></body></html>");
        return $this->render('front/index.html.twig', [
            'articles' => $articles,
            'spotlight' => $spotlight
        ]);
    }

    /**
     * Affiche les articles d'une catégorie.
     * @Route("/categorie/{slug<\w+>}",
     *     name="index_categorie",
     *     defaults={"slug"="politique"},
     *     methods={"GET"},
     *     requirements={"slug":"\w+"})
     * @param $slug
     * @param Categorie|null $categorie
     * @return Response
     */
    public function categorie($slug, Categorie $categorie = null)
    {
        if (null === $categorie) {
            // On génère une exception :
            //throw $this->createNotFoundException(
            //    "Nous n'avons pas trouvé cette catégorie"
            //);

            // Ou on redirige l'utilisateur sur la page d'accueil :
            return $this->redirectToRoute('index', [],
                Response::HTTP_MOVED_PERMANENTLY);
        }

        // Méthode 1
        // $categorie = $this->getDoctrine()
        //      ->getRepository(Categorie::class)
        //      ->findOneBy(['slug' => $slug]);
        // $articles = $categorie->getArticles();

        // Méthode 2
        // $articles = $this->getDoctrine()
        //     ->getRepository(Categorie::class)
        //     ->findOneBySlug($slug)
        //     ->getArticles();

        // Méthode 3
        return $this->render('front/categorie.html.twig', [
            'articles' => $categorie->getArticles(),
            'categorie' => $categorie
        ]);
    }

    /**
     * Afficher les articles d'un membre.
     * @Route("/membre/{prenom}_{nom}.html",
     *     name="index_membre",
     *     defaults={"prenom"="La", "nom"="redaction"},
     *     methods={"GET"},
     *     requirements={"prenom":"\w+", "nom":"\w+"})
     * @param Membre|null $membre
     * @return Response
     */
    public function membre(Membre $membre = null)
    {
        if (null === $membre) {
            // On génère une exception :
            throw $this->createNotFoundException(
                "Nous n'avons pas trouvé ce membre"
            );

            // Ou on redirige l'utilisateur sur la page d'accueil :
            // return $this->redirectToRoute('index', [],
            //    Response::HTTP_MOVED_PERMANENTLY);
        }

        // Méthode 1
        // $categorie = $this->getDoctrine()
        //      ->getRepository(Categorie::class)
        //      ->findOneBy(['slug' => $slug]);
        // $articles = $categorie->getArticles();

        // Méthode 2
        // $articles = $this->getDoctrine()
        //     ->getRepository(Categorie::class)
        //     ->findOneBySlug($slug)
        //     ->getArticles();

        // Méthode 3
        return $this->render('front/membre.html.twig', [
            'membre' => $membre,
            'articles' => $membre->getArticles(),
        ]);
    }

    /**
     * Afficher un article.
     * @Route(
     *     "/{categorie<\w+>}/{slug<[a-zA-Z1-9\-_\/]+>}_{id<\d+>}.html",
     *     name="index_article"
     *     )
     * @param $id
     * @param $slug
     * @param $categorie
     * @param Article|null $article
     * @return Response
     */
    public function article($id,
                            $slug,
                            $categorie,
                            Article $article = null)
    {
        //article = $this->getDoctrine()
        //    ->getRepository(Article::class)
        //    ->find($id);

        if (null === $article) {
            // On génère une exception :
            // throw $this->createNotFoundException(
            //    "Nous n'avons pas trouvé votre article ID : " . $id
            // );

            // Ou on redirige l'utilisateur sur la page d'accueil :
            return $this->redirectToRoute('index', [],
                Response::HTTP_MOVED_PERMANENTLY);
        }

        # Vérification du slug
        if ($article->getSlug() !== $slug || $article->getCategorie()->getSlug() !== $categorie) {
            return $this->redirectToRoute('index_article', [
                'categorie' => $article->getCategorie()->getSlug(),
                'slug' => $article->getSlug(),
                'id' => $id
            ]);
        }

        // Récupération des suggestions
        $suggestions = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findArticlesSuggestions($article->getId(), $article->getCategorie()->getId());

        // Transmission des données à la vue
        return $this->render('front/article.html.twig', [
            'article' => $article,
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Gérer l'affichage de la sidebar
     */
    public function sidebar()
    {
        // Récupération du Repository
        $repository = $this->getDoctrine()
            ->getRepository(Article::class);

        // Récupérer les 5 derniers articles
        $articles = $repository->findLatestArticles();

        // Récupérer les articles "spécial"
        $specials = $repository->findSpecialArticles();

        return $this->render('components/_sidebar.html.twig', [
            'articles' => $articles,
            'specials' => $specials
        ]);
    }
}
