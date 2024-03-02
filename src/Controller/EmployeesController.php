<?php

namespace App\Controller;

use App\Entity\Employees;
use App\Form\AddEmployeeType;
use App\Repository\EmployeesRepository;
use App\Repository\LeavesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class EmployeesController extends AbstractController
{
    #[Route('/land', name: 'app_landing')]
    public function land(): Response
    {
        return $this->render('basef.html.twig', [
        ]);
    }
    #[Route('/dashboard', name: 'app_dashboard')]
    public function dash(): Response
    {
        return $this->render('employees/index.html.twig', [
        ]);
    }
    #[Route('/employees', name: 'app_employees')]
    public function index(EmployeesRepository $eR,LeavesRepository $lR,PaginatorInterface $paginator,Request $request): Response
    {
        $numberofemployees=$eR->countEmployees();
        $employeesdata=$eR->findAll();
        $employees=$paginator->paginate($employeesdata,$request->query->getInt('page',1),4);

        $leaves=$lR->findAll();
        return $this->render('employees/index.html.twig', [
            'controller_name' => 'EmployeesController',
            'numberofemployees'=> $numberofemployees,
            'employees'=>$employees,
            'leaves'=>$leaves
        ]);
    }
    #[Route('/addEmployee', name: 'app_employee_add')]
    public function addEmployee(Request $request,EntityManagerInterface $en): Response
    {
       
        $employee=new Employees();
        $form=$this->createForm(AddEmployeeType::class,$employee);
        $form->handleRequest($request);
        if($form->isSubmitted() ){
            switch($form->get('contractType')->getData()){
                case 'CIVP':
                    $employee->setAllowedLeaveDays(12) ;
                    break;
                case 'KARAMA':
                    $employee->setAllowedLeaveDays(20) ;
                    break;
                case 'CSC':
                    $employee->setAllowedLeaveDays(25) ;
                    break;
                case 'CDI':
                    $employee->setAllowedLeaveDays(30) ;
                    break;
                case 'Autre':
                    $employee->setAllowedLeaveDays(15) ;
                    break;

            }
            $employee->setEmpTakenLeaves(0);
            if($form->isValid()){ 

           // $en=$this->getDoctrine()->getManager();
            $en->persist($employee);//Add
            $en->flush();
            $ok=1;
           // $_SESSION['status'] = 'employee_added';
            //var_dump($_SESSION['status']);
            return $this->redirectToRoute('app_employees',['ok'=>$ok]);
        }}
        return $this->render('employees/add.html.twig',['formAdd'=>$form->createView()]);
    }
    #[Route('/editEmployee/{id}', name: 'app_employee_edit')]
    public function editEmployee($id,Request $request,EntityManagerInterface $en,EmployeesRepository $eR)
    {
        $employee=$eR->find($id);
        $leaves=$employee->getLeaves();
        $form=$this->createForm(AddEmployeeType::class,$employee);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
           // $en=$this->getDoctrine()->getManager();
            $en->persist($employee);//Add
            $en->flush();
            return $this->redirectToRoute('app_employees');
        }
        return $this->render('employees/edit.html.twig',
            ['formAdd'=>$form->createView(),
             'leaves'=>$leaves,
             'id'=>$id
             ] );
    }

    #[Route('/deleteEmployee/{id}', name: 'app_employee_delete')]
    public function deleteEmployee($id,EntityManagerInterface $en,EmployeesRepository $eR): Response
    {
        $employee=$eR->find($id);
        $en->remove($employee);
        $en->flush();
        return $this->redirectToRoute('app_employees');  
    }

    #[Route('/employee/{id}', name: 'app_employee_show')]
    public function show($id,Request $request,EmployeesRepository $eR,LeavesRepository $lR): Response
    {
        $employee=$eR->find($id);
        $leaves=$employee->getLeaves();
        $form=$this->createForm(AddEmployeeType::class,$employee);
        $form->handleRequest($request);
        return $this->render('employees/profile.html.twig', [
            'formAdd'=>$form->createView(),
            'employees'=>$employee,
            'leaves'=>$leaves,
            'id'=>$id
        ]);
    }

    #[Route('/email', name: 'app_email')]
    public function email(): Response
    {
        return $this->render('employees/email.html');  
    }

}
