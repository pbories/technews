<?php

namespace App\Controller\TechNews\Security;


use App\Membre\MembreLoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * Connexion d'un membre.
     * @Route("/connexion.html", name="security_connexion")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function connexion(AuthenticationUtils $authenticationUtils)
    {
        /**
         * Si notre utilisateur est déjà authentifié,
         * on le redirige vers la page d'accueil.
         */
        if ($this->getUser()) {
            return $this->redirectToRoute('index');
        }

        // Récupération du formulaire de connexion
        $form = $this->createForm(MembreLoginType::class, [
            'email' => $authenticationUtils->getLastUsername()
        ]);

        // Récupération du message d'erreur
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('security/connexion.html.twig', [
            'form'          => $form->createView(),
            'error'         => $error
        ]);
    }

    /**
     * Déconnexion d'un membre.
     *@Route("/deconnexion.html", name="security_deconnexion")
     */
    public function deconnexion()
    {

    }

    /**
     * On peut définir ici
     * la logique mot de passe oublié,
     * réinitialisation du mot de passe,
     * email de validation, etc.
     */
}
