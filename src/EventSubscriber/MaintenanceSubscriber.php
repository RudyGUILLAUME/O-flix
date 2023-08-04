<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class MaintenanceSubscriber implements EventSubscriberInterface
{

    private $isMaintenance;

    public function __construct(bool $isMaintenance)
    {
        $this->isMaintenance = $isMaintenance;
    }

    /**
     * Methode qui est appelée lorsque l'event kernel.response
     * est lancé.
     *
     * @param ResponseEvent $event
     * @return void
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        if ($this->isMaintenance === false) {
            return;
        }
        // Avel l'ErrorController on a une sous-requete
        // Cette sous-requete permet de générer la page d'erreur

        if (!$event->isMainRequest()) {
            return;
        }
        
        // Si URL du Profiler ou de la WDT, on sort
        // $request->getPathInfo() contient la route
        if (preg_match('/^\/(_profiler|_wdt)/', $event->getRequest()->getPathInfo())) {
            return;
        }

        // Request Ajax, venant du front / accès API
        if ($event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        $response = $event->getResponse();
        $htmlContent = $response->getContent();

        $banner = '<div class="alert alert-danger">Maintenance prévue mardi 10 janvier à 17h00</div>';

        $htmlModifie = str_replace(
            // Ce que je cherche
            '</nav>',
            // Par ce que je veut le remplacer
            '</nav>' . $banner,
            // dans qoui je cherche
            $htmlContent
        );
        $response->setContent($htmlModifie);
    }

    /**
     * Cette fonction permet de préciser les événements
     * que l'on souhaite "écouter", on précise également pour evt écouté, la
     * méthode qui doit être appelée
     *
     * @return void
     */
    public static function getSubscribedEvents()
    {
        return [
            // Types d'evt que je souhaite gérer
            'kernel.response' => 'onKernelResponse'
        ];
    }
}