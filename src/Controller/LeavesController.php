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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LeavesController extends AbstractController
{
    #[Route('/leaves/{id}', name: 'app_leaves')]
    public function index($id, LeavesRepository $lR, EmployeesRepository $eR, SessionInterface $session)
    {
        $username = $session->get('username');
        //id employee
        $employee = $eR->find($id);
        $leaves = $employee->getLeaves();
        return $this->render('leaves/index.html.twig', [
            'controller_name' => 'LeavesController',
            'leaves' => $leaves,
            'username' => $username

        ]);
    }

    #[Route('/addLeave/{id}', name: 'app_leave_add')]
    public function addLeave($id, Request $request, EntityManagerInterface $en, EmployeesRepository $eR, SessionInterface $session): Response
    {
        $username = $session->get('username');
        //id employee
        $leave = new Leaves();
        $leave->setStatus("Pending");
        $leave->setEmployee($eR->find($id));
        //EmpTakenLeaves++
        $employee = $leave->getEmployee($eR->find($id));
        $employee->setEmpTakenLeaves($employee->getEmpTakenLeaves() + 1);
        $form = $this->createForm(AddLeaveType::class, $leave);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $en=$this->getDoctrine()->getManager();
            $en->persist($leave); //Add
            $en->flush();
            return $this->redirectToRoute('app_employee_edit', [
                'id' => $id
            ]);
        }
        return $this->render('leaves/add.html.twig', ['formAdd' => $form->createView(), 'id' => $id, 'username' => $username]);
    }

    #[Route('/editLeave/{id}', name: 'app_leave_edit')]
    public function editLeave($id, Request $request, EntityManagerInterface $en, LeavesRepository $lR, SessionInterface $session)
    {
        $username = $session->get('username');
        $leave = $lR->find($id);
        $form = $this->createForm(AddLeaveType::class, $leave);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $en=$this->getDoctrine()->getManager();
            $en->persist($leave); //Add
            $en->flush();
            return $this->redirectToRoute('app_employee_edit', [
                'id' => $leave->getEmployee()->getID() //id of employee
            ]);
        }
        return $this->render('leaves/add.html.twig', ['formAdd' => $form->createView(), 'id' => $leave->getEmployee()->getID(), 'username' => $username]);
    }

    #[Route('/deleteLeave/{id}', name: 'app_leave_delete')]
    public function deleteLeave($id, EntityManagerInterface $en, LeavesRepository $lR): Response
    {
        $leave = $lR->find($id);
        $en->remove($leave);
        $en->flush();
        return $this->redirectToRoute('app_employee_edit', [
            'id' => $leave->getEmployee()->getID() //id of employee
        ]);
    }

    #[Route('/update-leave-status', name: 'update_leave_status')]

    public function updateLeaveStatus(Request $request): Response
    {
        // Retrieve leave ID and status from the submitted form data
        $leaveId = $request->request->get('leaveId');
        $status = $request->request->get('status');

        // Fetch the leave entity from the database
        $entityManager = $this->getDoctrine()->getManager();
        $leave = $entityManager->getRepository(Leaves::class)->find($leaveId);

        if (!$leave) {
            // Return a response indicating the leave was not found
            return new Response('Leave not found', Response::HTTP_NOT_FOUND);
        }

        // Update the leave status
        var_dump($status);
        $leave->setStatus($status);

        // Save the changes to the database
        $entityManager->flush();

        // Redirect back to the page displaying the leaves
        return $this->redirectToRoute('app_employees'); // Adjust the route name as needed
    }
}
