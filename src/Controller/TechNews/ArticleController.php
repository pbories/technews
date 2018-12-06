<?php

namespace App\Controller\TechNews;


use App\Article\ArticleType;
use App\Controller\HelperTrait;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Membre;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends Controller
{
    use HelperTrait;

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
            ->setTitre('Death Stranding : Walmart lâche un mois de sortie, sans pression')
            ->setSlug('death-stranding-walmart-lache-mois-sortie-sans-pression')
            ->setContenu('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?')
            ->setFeaturedImage('2.jpg')
            ->setSpotlight(0)
            ->setSpecial(0)
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
          . ' dans la catégorie : '
          . $categorie->getNom()
          . ' de l\'auteur : '
          . $membre->getPrenom()
        );

    }

    /**
     * Formulaire pour ajouter un article.
     * @Route("/rediger-un-article.html",
     *     name="article_new")
     * @Security("has_role('ROLE_AUTEUR')")
     * @param Request $request
     * @return Response
     */
    public function newArticle(Request $request)
    {
        // Récupération d'un membre
        // $membre = $this->getDoctrine()
        //     ->getRepository(Membre::class)
        //     ->find(2)
        // ;

        $article = new Article();
        $article->setMembre($this->getUser());

        $form = $this->createForm(ArticleType::class, $article)
            ->handleRequest($request);

        // Si le formulaire est soumis et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {

            // dump($article);

            // Traitement de l'upload de l'image

            /** @var UploadedFile $featuredImage */
            $featuredImage = $article->getFeaturedImage();

            $fileName = $this->slugify($article->getTitre()).'.'.$featuredImage->guessExtension();

            // Déplace l'image dans le dossier où elles sont stockées
            try {
                $featuredImage->move(
                    $this->getParameter('articles_assets_dir'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // Mise à jour de l'image
            $article->setFeaturedImage($fileName);

            // Mise à jour du slug
            $article->setSlug($this->slugify($article->getTitre()));

            // Sauvegarde en BDD
            $em= $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            // Notification
            $this->addFlash('notice',
                'Félicitations, votre article est en ligne');

            // Redirection vers l'article créé
            return $this->redirectToRoute('index_article', [
                'categorie' => $article->getCategorie()->getSlug(),
                'slug' => $article->getSlug(),
                'id' => $article->getId()
            ]);
        }

        // Affichage du formulaire
        return $this->render('article/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Formulaire pour modifier un article.
     * @Route("/modifier/{slug<[a-zA-Z1-9\-_\/]+>}_{id<\d+>}.html",
     *     name="article_edit")
     * @Security("has_role('ROLE_AUTEUR')")
     * @param Request $request
     * @param Packages $packages
     * @param Article|null $article
     * @return Response
     */
    public function editArticle(Request $request,
                                Packages $packages,
                                Article $article = null)
    {
        $options = [
            'image_url' => $packages->getUrl('images/product/' . $article->getFeaturedImage())
        ];

        $article->setFeaturedImage(
            new File($this->getParameter('articles_assets_dir')
                .'/'.$article->getFeaturedImage())
        );

        $form = $this->createForm(ArticleType::class, $article, $options)
            ->handleRequest($request);

        // Affichage du formulaire
        return $this->render('article/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
