<?php

namespace App\Controller\TechNews;


use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Membre;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends Controller
{
    /**
     * Démonstration de l'ajout d'un article
     * avec Doctrine.
     * @Route("test/ajouter-un-article",
     *     name="article_test")
     */
    public function test ()
    {
        // Création d'une catégorie
        $categorie = new Categorie();
        $categorie->setNom('Politique');
        $categorie->setSlug('politique');

        // Création d'un membre (auteur de l'article)
        $membre = new Membre();
        $membre
            ->setPrenom('Pascal')
            ->setNom('Bories')
            ->setEmail('superparisian@gmail.com')
            ->setPassword('test')
            ->setRoles(['ROLE_AUTEUR'])
        ;

        // Création de l'article
        $article = new Article();
        $article
            ->setTitre('Une énorme expo Star Wars débarque cet hiver à Paris !')
            ->setSlug('une-enorme-expo-star-wars-debarque-cet-hiver-a-paris')
            ->setContenu('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?')
            ->setFeaturedImage('https://media.timeout.com/images/105327329/750/422/image.jpg')
            ->setSpotlight(0)
            ->setSpecial(1)
            ->setCategorie($categorie)
            ->setMembre($membre)
        ;

        // On sauvegarde le tout avec Doctrine ('em' pour Entity Manager)
        $em = $this->getDoctrine()->getManager();
        $em->persist($categorie);
        $em->persist($membre);
        $em->persist($article);
        $em->flush();

        return new Response(
          'Nouvel article ID : '
          . $article->getId()
          . 'dans la catégorie : '
          . $categorie->getNom()
          . ' de l\'auteur : '
          . $membre->getPrenom()
        );

    }
}