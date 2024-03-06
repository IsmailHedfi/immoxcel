<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Form\DepotType;
use App\Repository\DepotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use TCPDF;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;

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
            
            $depot->setQuantityAvailable(0);
            $em=$this->getDoctrine()->getManager();
            $em->persist($depot);//add
            $em->flush();
            return $this->redirectToRoute('display_afficherdepot');
        }
        return $this->render('depot/adddepot.html.twig', ['f'=>$form->createView()]);
    }  
    #[Route('/afficherdepot', name: 'app_afficherdepot')]
    public function afficherdepot(Request $request): Response
    {
         // Get all depots
        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->getRepository(Depot::class)->createQueryBuilder('m')
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
        $depots = $paginator->getIterator();
    
        // Get total count
        $totalCount = count($paginator);
    
        // Calculate total pages
        $totalPages = ceil($totalCount / $limit);
    
        return $this->render('depot/afficherdepot.html.twig', [
            'depots' => $depots,
            'totalPages' => $totalPages,
            'page' => $page,
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
            $limit=$form->get('LimitStock')->getData();
            if($limit >= $depots->getQuantityAvailable())
            {
                $em=$this->getDoctrine()->getManager();
                $em->flush();
                return $this->redirectToRoute('display_afficherdepot');
            }
            else
            {
                $form->get('LimitStock')->addError(new FormError('Limit Stock is below the quantity of materials in the Depot'));
            }
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
    #[Route('/generatePdf/{id}', name: 'depot_pdf_generate')]
    public function generatePdf($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $depot = $entityManager->getRepository(Depot::class)->find($id);

        if (!$depot) {
            throw $this->createNotFoundException('Le dépôt avec l\'ID ' . $id . ' n\'existe pas.');
        }

        $pdf = new TCPDF();

        $pdf->SetCreator('Your Name');
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Depot Details');

        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->Cell(0, 10, 'Depot Details', 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Location: ' . $depot->getLocation(), 0, 1);
        $pdf->Cell(0, 10, 'Adresse: ' . $depot->getAdresse(), 0, 1);
        $pdf->Cell(0, 10, 'Limit Stock: ' . $depot->getLimitStock(), 0, 1);

        // Afficher la liste des matériaux
        $header = array('Type Material', 'Unit Price', 'Quantity');

        // Afficher les en-têtes du tableau
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetFont('helvetica', 'B', 10);
        foreach ($header as $col) {
            $pdf->Cell(45, 7, $col, 1, 0, 'C', 1);
        }
        $pdf->Ln();
        
        // Afficher les données du tableau
        $pdf->SetFont('helvetica', '', 10);
        foreach ($depot->getMaterials() as $material) {
            $pdf->Cell(45, 7, $material->getTypeMaterial(), 1);
            $pdf->Cell(45, 7, $material->getUnitPrice(), 1);
            $pdf->Cell(45, 7, $material->getQuantity(), 1);
            $pdf->Ln();
        }
        return new Response($pdf->Output('depot_details.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
   
}

