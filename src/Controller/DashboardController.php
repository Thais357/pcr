<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Viajeros;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(): Response
    {
        $entityManager=$this->getDoctrine()->getManager();
        $viajerosNotificados=count($entityManager->getRepository(Viajeros::class)->findBy(['notificado'=>'si']));
        $viajerosSinNotificar=count($entityManager->getRepository(Viajeros::class)->findBy(['notificado'=>'no']));
        $totalResultados=count($this->getDoctrine()->getManager()->getRepository(Viajeros::class)->findAll());
  
     //   dump($viajerosNotificados);die();
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController','notificados'=>$viajerosNotificados,'sinNotificar'=>$viajerosSinNotificar,'totalResultados'=>$totalResultados
        ]);
    }
}
