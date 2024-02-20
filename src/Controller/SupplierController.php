<?php

namespace App\Controller;

use App\Entity\Supplier;
use App\Form\SupplierType;
use App\Repository\SupplierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SupplierController extends AbstractController
{

    #[Route('/dashboard/Supplier', name: 'app_supplier')]
    public function DisplaySupplier(SupplierRepository $rep): Response
    {
        $supplier = $rep->findAll();
        return $this->render(
            'supplier/index.html.twig',
            ['supplier' => $supplier]
        );
    }
    #[Route('/dashboard/addsupplier', name: 'app_add_supplier')]
    public function addsellpurchase(Request $request)
    {
        $supplier = new  Supplier();
        $form = $this->CreateForm(SupplierType::class, $supplier);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($supplier);
            $em->flush();

            return $this->redirectToRoute('app_supplier');
        }
        // si le formulaire n'est pas valide ou il n'a pas submitted on va le retourner a la vue de l'ajout pour ajouter une autre fois 
        return $this->render('supplier/addSupplier.html.twig', ['f' => $form->createView()]);
    }
    #[Route('/dashboard/edditSupplier/{id}', name: 'app_edit_supplier')]
    public function EditSupplier(SupplierRepository $rep, $id, Request $request)
    {
        $supplier = $rep->find($id);
        if (!$supplier) {
            throw $this->createNotFoundException('The sell purchase with id ' . $id . ' does not exist');
        }
        $form = $this->CreateForm(SupplierType::class, $supplier);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $em = $this->getDoctrine()->getManager();
            $em->persist($supplier);
            $em->flush();  // O n L'uitilise sur entitymanager pour energistrer les modifications en base de donee 

            return $this->redirectToRoute('app_supplier');
        }
        // si le formulaire n'est pas valide ou il n'a pas submitted on va le retourner a la vue de l'ajout pour ajouter une autre fois 
        return $this->render('supplier/addSupplier.html.twig', ['f' => $form->createView()]);
    }
    #[Route('/dashboard/deleteSupplier/{id}', name: 'app_delete_Supplier')]
    public function DeletePurchase(SupplierRepository $rep, $id)
    {
        $sellPur = $rep->find($id);
        if (!$sellPur) {
            throw $this->createNotFoundException('The sell purchase with id ' . $id . ' does not exist');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($sellPur);
        $em->flush();  // O n L'uitilise sur entitymanager pour energistrer les modifications en base de donee 

        return $this->redirectToRoute('app_supplier');
    }
}
