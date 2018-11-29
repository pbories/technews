<?php

namespace App\Controller\TechNews;


use App\Article\Provider\YamlProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends Controller
{
    /**
     * Page d'acceuil de notre site internet
     */
    public function index(YamlProvider $yamlProvider)
    {
        // Récupération des articles depuis le YamlProvider
        $articles = $yamlProvider->getArticles();
        // return new Response("<html><body><h1>TechNews, coming soon...</h1></body></html>");
        return $this->render('front/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * Page that displays the articles of a category
     * @Route("/categorie/{slug<\w+>}",
     *     name="index_categorie",
     *     defaults={"slug"="politique"},
     *     methods={"GET"},
     *     requirements={"slug":"\w+"})
     * @param $slug
     * @return Response
     */
    public function categorie($slug)
    {
        $x = new YamlProvider();
        $x->getArticles();
        return $this->render('front/categorie.html.twig');
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
     * @return Response
     */
    public function article($id, $slug, $categorie)
    {
        return $this->render('front/article.html.twig');
    }
}
