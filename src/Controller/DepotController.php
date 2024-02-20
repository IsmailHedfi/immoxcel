<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Form\DepotType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DepotController extends AbstractController
{
    #[Route('/depot', name: 'app_depot')]
    public function index(): Response
    {
        return $this->render('depot/index.html.twig', [
            'controller_name' => 'DepotController',
        ]);
    }
    #[Route('/adddepot', name: 'app_adddepot')]
    public function adddepot(Request $request): Response
    {
        $depot = new Depot();
        $form=$this->createForm(DepotType::class,$depot);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($depot);//add
            $em->flush();
            return $this->redirectToRoute('display_materials');
        }
        return $this->render('depot/adddepot.html.twig', ['f'=>$form->createView()]);
    }    
}

