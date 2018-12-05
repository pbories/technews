<?php

namespace App\Controller\TechNews;


use App\Entity\Membre;
use App\Membre\MembreType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MembreController extends Controller
{
    /**
     * @Route("/inscription.html",
     *     methods={"GET", "POST"},
     *     name="membre_inscription")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function inscription(Request $request,
                                UserPasswordEncoderInterface $passwordEncoder)
    {
        // Création d'un utilisateur

        $membre = new Membre();

        // Création du formulaire
        $form = $this->createForm(MembreType::class, $membre);
        $form->handleRequest($request);

        // Si le formulaire est soumis et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {

            $membre->setPassword($passwordEncoder->encodePassword($membre, $membre->getPassword()));

            // Sauvegarde en BDD
            $em= $this->getDoctrine()->getManager();
            $em->persist($membre);
            $em->flush();

            // Notification
            $this->addFlash('notice',
                'Félicitations, vous pouvez vous connecter');

            // Redirection vers la page de connexion
            return $this->redirectToRoute('index');
        }

        // Affichage du formulaire
        return $this->render('membre/inscription.html.twig', [
            'form' => $form->createView()
        ]);

    }
}