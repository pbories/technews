<?php

namespace App\Newsletter;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class NewsletterSubscriber implements EventSubscriberInterface
{
    private $session;

    /**
     * NewsletterSubscriber constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::RESPONSE => 'onKernelResponse'
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        // Vérifier que la requête vient de l'utilisateur et non de Symfony.
        if (!$event->isMasterRequest() || $event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        // Incrémentation du compteur de pages visitées par l'utilisateur.
        $this->session->set('compteur',
            $this->session->get('compteur', 0) + 1);

        // A la troisième requête, inviter l'utilisateur à s'inscrire.
        if($this->session->get('compteur') === 3){
            $this->session->set('inviteUserModal', true);
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        // Vérifier que la requête vient de l'utilisateur et non de Symfony.
        if (!$event->isMasterRequest() || $event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        // On passe notre inviteUserModal à false
        $this->session->set('inviteUserModal', false);
    }
}
