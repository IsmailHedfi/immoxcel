<?php

namespace App\Controller;

use App\Repository\SellPurchaseRepository;
use App\Repository\ExpensesRepository;
use App\Repository\SupplierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig');
    }
    #[Route('/dashboard/page', name: 'app_dashboard_page')]
    public function indexdashboard(): Response
    {
        return $this->render('dashboard/index.html.twig');
    }
    #[Route('/Immoxcel', name: 'app_Immoxcel')]
    public function immoxcel(): Response
    {
        return $this->render('Front/index.html.twig');
    }
    #[Route('/expenses', name: 'app_expensesdisplay')]
    public function displayExpenses(ExpensesRepository $rep): Response
    {
        $expenses = $rep->findAll();
        return $this->render('expenses/DisplayExpenses.html.twig', [
            'expenses' => $expenses
        ]);
    }
    #[Route('/dashboard/sell', name: 'app_dashboard_bill')]
    public function sellpage(SellPurchaseRepository $rep): Response
    {
        $sellPur = $rep->findAll();
        return $this->render('dashboard/sell.html.twig', ['sellPur' => $sellPur]);
    }
    #[Route('/dashboard/Supplier', name: 'app_supplier_display')]
    public function DisplaySupplier(SupplierRepository $rep): Response
    {
        $supplier = $rep->findAll();
        return $this->render(
            'supplier/index.html.twig',
            ['supplier' => $supplier]
        );
    }
}
