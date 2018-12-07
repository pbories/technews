<?php

namespace App\Controller\TechNews;


use App\Newsletter\NewsletterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NewsletterController extends AbstractController
{
    /**
     * Afficher une Modal Newsletter.
     */
    public function newsletter()
    {
        $form = $this->createForm(NewsletterType::class);

        return $this->render('newsletter/_modal.html.twig', [
            'form' => $form->createView()
        ]);
    }
}