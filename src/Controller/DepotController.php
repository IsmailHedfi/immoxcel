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
    #[Route('/home', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('depot/home.html.twig', [
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
            return $this->redirectToRoute('display_afficherdepot');
        }
        return $this->render('depot/adddepot.html.twig', ['f'=>$form->createView()]);
    }  
    #[Route('/afficherdepot', name: 'app_afficherdepot')]
    public function afficherdepot(): Response
    {
         // Get all depots
        $depots = $this->getDoctrine()->getRepository(Depot::class)->findAll();

        return $this->render('depot/afficherdepot.html.twig', [
            'depots' => $depots,
        ]);
    } 
    #[Route('/editdepot/{id}', name: 'app_editdepot')]
    public function editdepot(Request $request,int $id): Response
    {
        $depots =$this->getDoctrine()->getManager()->getRepository(Depot::class)->find($id);
        $form=$this->createForm(DepotType::class,$depots);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('display_afficherdepot');
        }
        return $this->render('depot/editdepot.html.twig', ['f'=>$form->createView()]);
    }
    #[Route('/deletedepot/{id}', name:'app_deletedepot')]
    public function deletedepot(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $depot = $entityManager->getRepository(Depot::class)->find($id);
        if (!$depot) {
            throw $this->createNotFoundException('Depot non trouvé avec l\'identifiant: '.$id);
        }
    
        $entityManager->remove($depot);
        $entityManager->flush();
    
        return $this->redirectToRoute('display_afficherdepot');
    } 
    
}

