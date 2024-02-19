<?php

namespace App\Controller;

use App\Entity\Leaves;
use App\Form\AddLeaveType;
use App\Repository\EmployeesRepository;
use App\Repository\LeavesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LeavesController extends AbstractController
{
    #[Route('/leaves/{id}', name: 'app_leaves')]
    public function index($id,LeavesRepository $lR,EmployeesRepository $eR)
    {
        $employee=$eR->find($id);
        $leaves=$employee->getLeaves();
        return $this->render('leaves/index.html.twig', [
            'controller_name' => 'LeavesController',
            'leaves'=>$leaves
        ]);
    }

    #[Route('/addLeave/{id}', name: 'app_leave_add')]
    public function addLeave($id,Request $request,EntityManagerInterface $en,EmployeesRepository $eR): Response
    {
        $leave=new Leaves();
        $leave->setStatus("Pending");
        $leave->setEmployee($eR->find($id));
        $form=$this->createForm(AddLeaveType::class,$leave);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
           // $en=$this->getDoctrine()->getManager();
            $en->persist($leave);//Add
            $en->flush();
            return $this->redirectToRoute('app_employee_edit', [
    'id' => $id
]);
        }
        return $this->render('leaves/add.html.twig',['formAdd'=>$form->createView()]);
    }

    #[Route('/editLeave/{id}', name: 'app_leave_edit')]
    public function editLeave($id,Request $request,EntityManagerInterface $en,LeavesRepository $lR)
    {
        $leave=$lR->find($id);
        $form=$this->createForm(AddLeaveType::class,$leave);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
           // $en=$this->getDoctrine()->getManager();
            $en->persist($leave);//Add
            $en->flush();
            return $this->redirectToRoute('app_employee_edit', [
                'id' => $leave->getEmployee()->getID()
            ]);
        }
        return $this->render('leaves/add.html.twig',['formAdd'=>$form->createView()]);
    }

    #[Route('/deleteLeave/{id}', name: 'app_leave_delete')]
    public function deleteLeave($id,EntityManagerInterface $en,LeavesRepository $lR): Response
    {
        $leave=$lR->find($id);
        $en->remove($leave);
        $en->flush();
        return $this->redirectToRoute('app_leaves', [
            'id' => $id
        ]);  
    }
}
