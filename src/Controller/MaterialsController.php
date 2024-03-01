<?php

namespace App\Controller;

use App\Entity\Materials;
use App\Form\MaterialsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MaterialsController extends AbstractController
{
    #[Route('/materials', name: 'app_materials')]
    public function index(): Response
    {
        return $this->render('materials/index.html.twig', [
            'controller_name' => 'MaterialsController',
        ]);
    }
    #[Route('/addmaterials', name: 'app_addmaterials')]
    public function addmaterials(Request $request): Response
    {
        $materials = new Materials();
        $form=$this->createForm(MaterialsType::class,$materials);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($materials);//add
            $em->flush();
            return $this->redirectToRoute('display_affichermaterials');
        }
        return $this->render('materials/addmaterials.html.twig', ['f'=>$form->createView()]);
    }
    #[Route('/affichermaterials', name: 'app_affichermaterials')]
    public function affichermaterials(): Response
    {
        $materials=$this->getDoctrine()->getManager()->getRepository(Materials::class)->findAll();
        return $this->render('materials/affichermaterials.html.twig', [
            'b'=>$materials ]);
    }
    #[Route('/editmaterials/{id}', name: 'app_editmaterials')]
    public function editmaterials(Request $request,int $id): Response
    {
        $materials =$this->getDoctrine()->getManager()->getRepository(Materials::class)->find($id);
        $form=$this->createForm(MaterialsType::class,$materials);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('display_affichermaterials');
        }
        return $this->render('materials/editmaterials.html.twig', ['f'=>$form->createView()]);
    }
    #[Route('/deletematerials/{id}', name:'app_deletematerials')]
    public function deletematerials(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $materials = $entityManager->getRepository(Materials::class)->find($id);
    
        if (!$materials) {
            throw $this->createNotFoundException('Matériel non trouvé avec l\'identifiant: '.$id);
        }
    
        $entityManager->remove($materials);
        $entityManager->flush();
    
        return $this->redirectToRoute('display_affichermaterials');
    }


}
