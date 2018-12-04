<?php

namespace App\Controller\TechNews;


use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Membre;
use App\Repository\CategorieRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
     * @Route("/creer-un-article",
     *     name="article_new")
     */
    public function newArticle()
    {
        // Récupération d'un membre
        $membre = $this->getDoctrine()
            ->getRepository(Membre::class)
            ->find(2)
        ;

        $article = new Article();
        $article->setMembre($membre);

        $form = $this->createFormBuilder($article)
            ->add('titre', TextType::class, [
                'required' => true,
                'label' => "Titre de l'article",
                'attr' => [
                    'placeholder' => "Titre de l'article"
                ]
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'expanded' => false,
                'multiple' => false,
                'label' => false
            ])
            ->add('contenu', CKEditorType::class, [
                'required' => true,
                'label' => false,
                'config' => [
                    'toolbar' => 'standard'
                ]
            ])
            ->add('featuredImage', FileType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'class' => 'dropify'
                ]
            ])
            ->add('special', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'data-toggle' => 'toggle',
                    'data-on' => 'Oui',
                    'data-off' => 'Non'
                ]
            ])
            ->add('spotlight', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'data-toggle' => 'toggle',
                    'data-on' => 'Oui',
                    'data-off' => 'Non'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Publier mon article'
            ])
            // catégorie, contenu, featuredimage, special, spotlight
            ->getForm()
        ;

        // Affichage du formulaire
        return $this->render('article/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}