<?php

namespace App\Article;

use App\Entity\Article;
use App\Entity\Categorie;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Champ titre
            ->add('titre', TextType::class, [
                'required' => true,
                'label' => "Titre de l'article",
                'attr' => [
                    'placeholder' => "Titre de l'article"
                ]
            ])
            // Liste déroulante des catégories
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'expanded' => false,
                'multiple' => false,
                'label' => false
            ])
            // Saisie de l'article en WYSIWYG
            ->add('contenu', CKEditorType::class, [
                'required' => true,
                'label' => false,
                'config' => [
                    'toolbar' => 'standard'
                ]
            ])
            // Upload de l'image (drag & drop)
            ->add('featuredImage', FileType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'class' => 'dropify',
                    'data-default-file' => $options['image_url']
                ]
            ])
            // Spécial ou non
            ->add('special', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'data-toggle' => 'toggle',
                    'data-on' => 'Oui',
                    'data-off' => 'Non'
                ]
            ])
            // Spotlight ou non
            ->add('spotlight', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'data-toggle' => 'toggle',
                    'data-on' => 'Oui',
                    'data-off' => 'Non'
                ]
            ])
            // Bouton submit
            ->add('submit', SubmitType::class, [
                'label' => 'Publier'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'image_url' => null
        ]);
    }


}