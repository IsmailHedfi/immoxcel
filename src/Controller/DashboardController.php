<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Capital;
use App\Entity\Expenses;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\CapitalRepository;
use App\Repository\SellPurchaseRepository;
use App\Repository\ExpensesRepository;
use App\Repository\SupplierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig');
    }
    #[Route('/dashboard/hell', name: 'app_dashboard_page')]
    public function indexdashboard(ExpensesRepository $rep, ChartBuilderInterface $chartBuilder, CapitalRepository $capitalrep): Response
    {
        $cap = $this->getDoctrine()->getRepository(Capital::class)->findAll();
        $expenses = $rep->findActiveTransactions();
        $capitals = $capitalrep->findAll();
        $expenses = $rep->findActiveTransactions();
        foreach ($capitals as $capital) {
            $CapExpenses = $capital->getExpensess();
            $CapProfits = $capital->getProfits();
            $CapSalary = $capital->getSalary();
            // Affichage de stat 
            return $this->render('expenses/yoyo.html.twig', [
                'expenses' => $expenses,
                'cap' => $cap,
                'CapExpenses' => $CapExpenses,
                'CapProfits' => $CapProfits,
                'CapSalary' => $CapSalary
            ]);
        }
    }
    #[Route('/Immoxcel', name: 'app_Immoxcel')]
    public function immoxcel(): Response
    {
        return $this->render('Front/base.html.twig');
    }
    #[Route('/expenses', name: 'app_expensesdisplay')]
    public function displayExpenses(ExpensesRepository $rep, Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $expenses = $rep->findbySearch($searchData);

            return $this->render('expenses/DisplayExpenses.html.twig', [
                'form' => $form->createView(),
                'expenses' => $expenses,
                'searchQuery' => $searchData->q // Pass the search query string to the template
            ]);
        }

        // If the form is not submitted or not valid, render the template without passing $searchData
        return $this->render('expenses/DisplayExpenses.html.twig', [
            'form' => $form->createView(),
            'expenses' => $rep->orderByDest(),
        ]);
    }
    #[Route('/dashboard/sell', name: 'app_dashboard_bill')]
    public function sellpage(SellPurchaseRepository $rep): Response
    {
        $sellPur = $rep->findAll();
        return $this->render('dashboard/sell.html.twig', ['sellPur' => $sellPur]);
    }
    #[Route('/dashboard/Supplier', name: 'app_supplier_display')]
    public function DisplaySupplier(SupplierRepository $rep, Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $supplier = $rep->findbySearch($searchData);
            return $this->render('supplier/index.html.twig', [
                'form' => $form->createView(),
                'supplier' => $supplier,
                'searchQuery' => $searchData->q // Pass the search query string to the template
            ]);
        }
        return $this->render('supplier/index.html.twig', [
            'form' => $form->createView(),
            'supplier' => $rep->orderByDest()
        ]);
    }
    // function to render the archived Transactions
    #[Route('/Archive', name: 'app_archive')]
    public function archived(ExpensesRepository $rep): Response
    {
        $expenses = $rep->findNotActiveTransactions();
        return $this->render('expenses/archive.html.twig', [
            'expenses' => $expenses
        ]);
    }
    #[Route('/dashboard/generate/{id}', name: 'app_generatePdf')]
    public function generatePdf($id): Response
    {
        $expenses = $this->getDoctrine()->getRepository(Expenses::class)->find($id);
        if (!$expenses) {
            throw $this->createNotFoundException('The expense with ID ' . $id . ' does not exist.');
        }

        // Create Dompdf options
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with options
        $dompdf = new Dompdf($pdfOptions);

        // Generate HTML content from Twig template
        $html = $this->renderView('expenses/Pdf.html.twig', [
            'expenses' => $expenses
        ]);

        // Load HTML content into Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();

        // Output the generated PDF directly to the browser
        $response = new Response($dompdf->output(), Response::HTTP_OK, [
            'Content-Type' => 'application/pdf'
        ]);

        // Optionally, set the PDF filename for downloading
        $response->headers->set('Content-Disposition', 'attachment; filename="mypdff.pdf"');

        return $response;
    }
    #[Route('/dashboard/expnTEST', name: 'app_Disp2')]
    public function displyExpenses(ExpensesRepository $rep, Request $request): Response
    {
        $expenses = $rep->findall();
        return $this->render('expenses/Display2.html.twig', [
            'expenses' => $expenses,
        ]);
    }
    #[Route('/home', name: 'app_homepage')]
    public function chartjsDisplay(ChartBuilderInterface $chartBuilder, CapitalRepository $capitalrep): Response
    {
        $capitals = $capitalrep->findAll();
        foreach ($capitals as $capital) {
            $CapExpenses = $capital->getExpensess();
            $CapProfits = $capital->getProfits();
            $CapSalary = $capital->getSalary();
            return $this->render('dashboard/chart.html.twig', [
                'CapExpenses' => $CapExpenses,
                'CapProfits' => $CapProfits,
                'CapSalary' => $CapSalary
            ]);
        }
    }
}
