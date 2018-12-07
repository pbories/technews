<?php

namespace App\Membre;


use App\Entity\Membre;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class MembreLoginSubscriber implements EventSubscriberInterface
{
    private $em;

    /**
     * MembreLoginSubscriber constructor.
     * @param $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->em = $manager;
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onMembreLogin'
        ];
    }

    public function onMembreLogin(InteractiveLoginEvent $event)
    {
        // Récupération du membre.
        $membre = $event->getAuthenticationToken()->getUser();

        // Mise à jour de la date de dernière connexion en BDD
        if ($membre instanceof Membre) {

            $membre->setDerniereConnexion();
            $this->em->flush();

        }
    }
}
