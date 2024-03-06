<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Materials;
use App\Form\EditMaterialsType;
use App\Form\MaterialsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Form\FormError;

use function PHPUnit\Framework\isEmpty;

class MaterialsController extends AbstractController
{
    #[Route('/materials', name: 'app_materials')]
    public function index(): Response
    {
        return $this->render('materials/index.html.twig', [
            'controller_name' => 'MaterialsController',
        ]);
    }
    #[Route('/addmaterials/{id}', name: 'app_addmaterials')]
    public function addmaterials(Request $request,$id): Response
    {
        $depot= new Depot();
        $depotRepository = $this->getDoctrine()->getRepository(Depot::class);
        $depot=$depotRepository->find($id);
        $materials = new Materials();
        $form=$this->createForm(MaterialsType::class,$materials);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $quantity=$form->get('Quantity')->getData();
            $quantitytoAdd=$quantity+$depot->getQuantityAvailable();
            if($quantitytoAdd < $depot->getLimitStock())
            {    $depot->setQuantityAvailable($quantitytoAdd);
                $materials->setDepot($depot);
                $em=$this->getDoctrine()->getManager();
                $em->persist($materials);//add
                $em->flush();
                $em->persist($depot);
                $em->flush();
                return $this->redirectToRoute('display_afficherdepot');
            }
            else
                {
                    $form->get('Quantity')->addError(new FormError('Quantity Over The Depot Limit'));
                }
            
        }
        return $this->render('materials/addmaterials.html.twig', ['f'=>$form->createView()]);
    }
    #[Route('/affichermaterials', name: 'app_affichermaterials')]
    public function affichermaterials(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->getRepository(Materials::class)->createQueryBuilder('m')
            ->getQuery();
    
        // Pagination
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 5);
        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
    
        // Get paginated materials
        $materials = $paginator->getIterator();
    
        // Get total count
        $totalCount = count($paginator);
    
        // Calculate total pages
        $totalPages = ceil($totalCount / $limit);
    
        return $this->render('materials/affichermaterials.html.twig', [
            'materials' => $materials,
            'totalPages' => $totalPages,
            'page' => $page,
        ]);
    }
    #[Route('/editmaterials/{id}', name: 'app_editmaterials')]
    public function editmaterials(Request $request,int $id): Response
    {
        $materials =$this->getDoctrine()->getManager()->getRepository(Materials::class)->find($id);
        $depot=$this->getDoctrine()->getManager()->getRepository(Depot::class)->find($materials->getDepot()->getId());
        $form=$this->createForm(EditMaterialsType::class,$materials);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $quantity=$form->get('Quantity')->getData();
            $depotName=$form->get('Depot')->getData();
           
            if($depotName === $depot)
                {
                    $quantityAdded=$quantity+$depot->getQuantityAvailable();
                    $limit=$depot->getLimitStock();
                    if($quantityAdded < $limit)
                        {   
                       
                        $depot->setQuantityAvailable($quantityAdded);
                        $materials->setDepot($depot);
                        $em=$this->getDoctrine()->getManager();
                        $em->persist($materials);//add
                        $em->flush();
                        $em->persist($depot);
                        $em->flush();
                        return $this->redirectToRoute('display_afficherdepot');
                        }
                    else
                    {
                        $form->get('Quantity')->addError(new FormError('Quantity Over The Depot Limit'));
                    }

                }
                else
                    {
                        $newdepot=$this->getDoctrine()->getManager()->getRepository(Depot::class)->find($depotName->getId());
                        $quantitytoAdd=$quantity+$newdepot->getQuantityAvailable();
                    if($quantitytoAdd < $newdepot->getLimitStock())
                    {    $newdepot->setQuantityAvailable($quantitytoAdd);
                        $materials->setDepot($newdepot);
                        $depot->setQuantityAvailable($depot->getQuantityAvailable() - $quantity);
                        $em=$this->getDoctrine()->getManager();
                        $em->persist($materials);//add
                        $em->flush();
                        $em->persist($newdepot);
                        $em->flush();

                        return $this->redirectToRoute('display_afficherdepot');
                    }
                    else
                    {
                        $form->get('Quantity')->addError(new FormError('Quantity Over The Depot Limit'));
                    }

                    }
            
            
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
        $depot=$materials->getDepot();
        $depot->setQuantityAvailable($depot->getQuantityAvailable()-$materials->getQuantity());
        $entityManager->remove($materials);
        $entityManager->flush();
        $entityManager->persist($depot);
        $entityManager->flush();
    
        return $this->redirectToRoute('display_afficherdepot');
    }

}
