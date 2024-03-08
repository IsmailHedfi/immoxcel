<?php

namespace App\Controller;

use App\Entity\Supplier;
use App\Form\SupplierType;
use App\Form\SearchType;
use App\Form\SmsType;
use App\Model\SearchData;
use App\Repository\SupplierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use App\Service\SmsGenerator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SupplierController extends AbstractController
{

    #[Route('/dashboard/Supplierr', name: 'app_supplier')]
    public function DisplaySupplier(SupplierRepository $rep, Request $request, SessionInterface $session): Response
    {
        $username = $session->get('username');
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $supplier = $rep->findbySearch($searchData);
            return $this->render('supplier/index.html.twig', [
                'form' => $form->createView(),
                'supplier' => $supplier,
                'searchQuery' => $searchData->q,
                'username' => $username // Pass the search query string to the template
            ]);
        }
        return $this->render('supplier/index.html.twig', [
            'form' => $form->createView(),
            'supplier' => $rep->orderByDest(),
            'username' => $username
        ]);
    }

    #[Route('/dashboard/addsupplier', name: 'app_add_supplier')]
    public function addsupplier(Request $request, SessionInterface $session)
    {
        $username = $session->get('username');
        $supplier = new  Supplier();
        $form = $this->CreateForm(SupplierType::class, $supplier);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($supplier);
            $em->flush();
            $this->addFlash('success', 'Supplier added successfully.');


            return $this->redirectToRoute('app_supplier', ['smsSent' => true]);
        }
        // si le formulaire n'est pas valide ou il n'a pas submitted on va le retourner a la vue de l'ajout pour ajouter une autre fois 
        return $this->render('supplier/addSup.html.twig', ['f' => $form->createView(), 'smsSent' => false, 'username' => $username]);
    }
    #[Route('/dashboard/edditSupplier/{id}', name: 'app_edit_supplier')]
    public function EditSupplier(SupplierRepository $rep, $id, Request $request, SessionInterface $session)
    {
        $username = $session->get('username');
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
            $this->addFlash('success', 'Supplier Edited successfully.');


            return $this->redirectToRoute('app_supplier', ['Edit' => true, 'username' => $username]);
        }
        // si le formulaire n'est pas valide ou il n'a pas submitted on va le retourner a la vue de l'ajout pour ajouter une autre fois 
        return $this->render('supplier/addsup.html.twig', ['f' => $form->createView(), ['Edit' => false]]);
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
    #[Route('/supplier/smonotif/{id}', name: 'heloosmo', methods: ['GET', 'POST'])]
    public function sendSms(Request $request, SmsGenerator $smsGenerator, SupplierRepository $rep, SessionInterface $session): Response
    {
        $username = $session->get('username');
        $form = $this->createForm(SmsType::class); // Create the form
        $form->handleRequest($request); // Handle form submission

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Get form data
            $formData = $form->getData();
            $name = $formData['name']; // Access form data using array notation
            $text = $formData['text']; // Access form data using array notation
            $number_test = $_ENV['TWILIO_TO_NUMBER']; // Numéro vérifié par Twilio. Un seul numéro autorisé pour la version de test.

            // Call the service to send SMS
            $smsGenerator->sendSms($number_test, $name, $text);

            // Add flash message
            $this->addFlash('success', 'SMS sent successfully.');

            // Redirect to a success route
            return $this->redirectToRoute('app_supplier');
        }

        // Render the form template
        return $this->render('supplier/smsSend.html.twig', [
            'form' => $form->createView(), 'username' => $username
        ]);
    }
}
