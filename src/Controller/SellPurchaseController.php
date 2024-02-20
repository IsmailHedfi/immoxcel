<?php

namespace App\Controller;

use App\Form\SellPurchaseType;
use App\Entity\SellPurchase;
use App\Repository\SellPurchaseRepository;
use App\Repository\BillInvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SellPurchaseController extends AbstractController
{
    #[Route('/dashboard/sell', name: 'app_sell_purchase')]
    public function index(SellPurchaseRepository $rep): Response
    {
        $sellPur = $rep->findAll();
        return $this->render('dashboard/sell.html.twig', ['sellPur' => $sellPur]);
    }

    #[Route('/dashboard/addsell', name: 'app_addsell')]
    public function addsellpurchase(Request $request)
    {
        $sellPur = new SellPurchase();
        $form = $this->CreateForm(SellPurchaseType::class, $sellPur);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sellPur->setDate(new \DateTime());
            $sellPur->setTotalAmount($sellPur->getCoast() * $sellPur->getQuantity());
            if ($sellPur->getType() == 'Sell') {
                $sellPur->setFund($sellPur->getFund() + $sellPur->getTotalAmount());
            } else {
                $sellPur->setFund($sellPur->getFund() - $sellPur->getTotalAmount());
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($sellPur);
            $em->flush();

            return $this->redirectToRoute('app_sell_purchase');
        }
        // si le formulaire n'est pas valide ou il n'a pas submitted on va le retourner a la vue de l'ajout pour ajouter une autre fois 
        return $this->render('sell_purchase/Sell_add.html.twig', ['f' => $form->createView()]);
    }
    #[Route('/dashboard/edditsell/{id}', name: 'app_edit_sell')]
    public function Editsellpurchase(SellPurchaseRepository $rep, $id, Request $request)
    {
        $sellPur = $rep->find($id);
        if (!$sellPur) {
            throw $this->createNotFoundException('The sell purchase with id ' . $id . ' does not exist');
        }
        $form = $this->CreateForm(SellPurchaseType::class, $sellPur);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sellPur->setDate(new \DateTime());
            $sellPur->setTotalAmount($sellPur->getCoast() * $sellPur->getQuantity());
            if ($sellPur->getType() == 'Sell') {
                $sellPur->setFund($sellPur->getFund() + $sellPur->getTotalAmount());
            } else {
                $sellPur->setFund($sellPur->getTotalAmount() - $sellPur->getFund());
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($sellPur);
            $em->flush();  // O n L'uitilise sur entitymanager pour energistrer les modifications en base de donee 

            return $this->redirectToRoute('app_sell_purchase');
        }
        // si le formulaire n'est pas valide ou il n'a pas submitted on va le retourner a la vue de l'ajout pour ajouter une autre fois 
        return $this->render('sell_purchase/Sell_add.html.twig', ['f' => $form->createView()]);
    }
    #[Route('/dashboard/deletePurchase/{id}', name: 'app_delete_purchase')]
    public function DeletePurchase(SellPurchaseRepository $rep, $id)
    {
        $sellPur = $rep->find($id);
        if (!$sellPur) {
            throw $this->createNotFoundException('The sell purchase with id ' . $id . ' does not exist');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($sellPur);
        $em->flush();  // O n L'uitilise sur entitymanager pour energistrer les modifications en base de donee 

        return $this->redirectToRoute('app_sell_purchase');
    }
}
