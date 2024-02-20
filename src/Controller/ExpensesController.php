<?php

namespace App\Controller;

use App\Entity\Expenses;
use App\Form\ExpensesType;
use App\Repository\ExpensesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExpensesController extends AbstractController
{
    #[Route('/expenses', name: 'app_expensesdis')]
    public function index(ExpensesRepository $rep): Response
    {
        $expenses = $rep->findAll();
        return $this->render('expenses/DisplayExpenses.html.twig', [
            'expenses' => $expenses
        ]);
    }
    #[Route('/dashboard/expenses', name: 'app_addexpenses')]
    public function addexpensese(Request $request)
    {
        $expenses = new Expenses();
        $form = $this->CreateForm(ExpensesType::class, $expenses);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $expenses->setDateE(new \DateTime());
            $total = $expenses->getCoast() * $expenses->getQuantityE();
            $expenses->setTotalAmount($total);
            $Materials = $expenses->getMaterials();
            $Materials->setQuantity($Materials->getQuantity() + $expenses->getMaterials());
            $em = $this->getDoctrine()->getManager();
            $em->persist($expenses);
            $em->flush();

            return $this->redirectToRoute('app_expenses');
        }
        // si le formulaire n'est pas valide ou il n'a pas submitted on va le retourner a la vue de l'ajout pour ajouter une autre fois 
        return $this->render('expenses/add.html.twig', ['f' => $form->createView()]);
    }
}
