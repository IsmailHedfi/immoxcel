<?php

namespace App\Controller;

use App\Entity\Employees;
use App\Entity\Leaves;
use App\Form\AddEmployeeType;
use App\Repository\EmployeesRepository;
use App\Repository\LeavesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Proxies\__CG__\App\Entity\Employees as EntityEmployees;
use SessionIdInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twilio\Rest\Proxy\V1\Service\SessionInstance;

class EmployeesController extends AbstractController
{
    #[Route('/land', name: 'app_landing')]
    public function land(): Response
    {
        return $this->render('basef.html.twig', []);
    }
    /*#[Route('/dashboard', name: 'app_dashboard')]
    public function dash(): Response
    {
        return $this->render('employees/index.html.twig', [
        ]);
    }*/
    #[Route('/employees', name: 'app_employees')]
    public function index(EmployeesRepository $eR, LeavesRepository $lR, PaginatorInterface $paginator, Request $request, SessionInterface $session): Response
    {
        $username = $session->get('username');
        $entityManager = $this->getDoctrine()->getManager();
        $employeeCounts = $entityManager->getRepository(Employees::class)->getEmployeeCountByFunction();


        $numberofemployees = $eR->countEmployees();
        $employeesdata = $eR->findAll();
        $reversedEmployeesData = array_reverse($employeesdata);
        $employees = $paginator->paginate($reversedEmployeesData, $request->query->getInt('page', 1), 4);

        $leaves = $lR->findAll();
        return $this->render('employees/index.html.twig', [
            'controller_name' => 'EmployeesController',
            'numberofemployees' => $numberofemployees,
            'employees' => $employees,
            'leaves' => $leaves,
            'employeeCounts' => $employeeCounts,
            'username' => $username

        ]);
    }
    #[Route('/addEmployee', name: 'app_employee_add')]
    public function addEmployee(Request $request, EntityManagerInterface $en, SessionInterface $session): Response
    {
        $username = $session->get('username');
        $employee = new Employees();
        $form = $this->createForm(AddEmployeeType::class, $employee);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            switch ($form->get('contractType')->getData()) {
                case 'CIVP':
                    $employee->setAllowedLeaveDays(12);
                    break;
                case 'KARAMA':
                    $employee->setAllowedLeaveDays(20);
                    break;
                case 'CSC':
                    $employee->setAllowedLeaveDays(25);
                    break;
                case 'CDI':
                    $employee->setAllowedLeaveDays(30);
                    break;
                case 'Autre':
                    $employee->setAllowedLeaveDays(15);
                    break;
            }
            $employee->setEmpTakenLeaves(0);
            if ($form->isValid()) {

                // $en=$this->getDoctrine()->getManager();
                $en->persist($employee); //Add
                $en->flush();
                $ok = 1;
                // $_SESSION['status'] = 'employee_added';
                //var_dump($_SESSION['status']);
                return $this->redirectToRoute('app_employees', ['ok' => $ok]);
            }
        }
        return $this->render('employees/add.html.twig', ['formAdd' => $form->createView(), 'username' => $username]);
    }
    #[Route('/editEmployee/{id}', name: 'app_employee_edit')]
    public function editEmployee($id, Request $request, EntityManagerInterface $en, EmployeesRepository $eR, SessionInterface $session)
    {
        $username = $session->get('username');
        $employee = $eR->find($id);
        $leaves = $employee->getLeaves();
        $form = $this->createForm(AddEmployeeType::class, $employee);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $en=$this->getDoctrine()->getManager();
            $en->persist($employee); //Add
            $en->flush();
            return $this->redirectToRoute('app_employees');
        }
        return $this->render(
            'employees/edit.html.twig',
            [
                'formAdd' => $form->createView(),
                'leaves' => $leaves,
                'id' => $id,
                'username' => $username
            ]
        );
    }

    #[Route('/deleteEmployee/{id}', name: 'app_employee_delete')]
    public function deleteEmployee($id, EntityManagerInterface $en, EmployeesRepository $eR): Response
    {
        $employee = $eR->find($id);
        $en->remove($employee);
        $en->flush();
        return $this->redirectToRoute('app_employees');
    }

    #[Route('/employee/{id}', name: 'app_employee_show')]
    public function show($id, Request $request, EmployeesRepository $eR, LeavesRepository $lR, SessionInterface $session): Response
    {
        $username = $session->get('username');
        $employee = $eR->find($id);
        $leaves = $employee->getLeaves();
        $form = $this->createForm(AddEmployeeType::class, $employee);
        $form->handleRequest($request);
        return $this->render('employees/profile.html.twig', [
            'formAdd' => $form->createView(),
            'employees' => $employee,
            'leaves' => $leaves,
            'id' => $id,
            'username' => $username
        ]);
    }

    #[Route('/email', name: 'app_email')]
    public function email(SessionInterface $session): Response
    {
        $username = $session->get('username');
        return $this->render('employees/email.html', ['username' => $username]);
    }

    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function search(Request $request, EmployeesRepository $eR,): JsonResponse
    {
        // var_dump("famechhhh");

        $searchTerm = $request->query->get('search');
        $employees = $eR->findBySearchTerm($searchTerm);
        //var_dump("am hereee");
        $data = [];
        foreach ($employees as $employee) {
            $data[] = [
                'id' => $employee->getId(),
                'EmpName' => $employee->getEmpName(),
                'EmpLastName' => $employee->getEmpLastName(),
                'EmpEmail' => $employee->getEmpEmail(),
                'EmpFunction' => $employee->getEmpFunction(),
                'EmpPhone' => $employee->getEmpPhone()


            ];
        }
        /*
        if(empty($data)){
            var_dump("famchiiiiii");
        }
        else{
            var_dump("famaaa");
            var_dump($data);

        }
*/
        return new JsonResponse($data);
    }
    #[Route('/statistics', name: 'app_statistics')]
    public function statistics(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $employeeCounts = $entityManager->getRepository(Employees::class)->getEmployeeCountByFunction();

        return $this->render('employees/index.html.twig', [
            'employeeCounts' => $employeeCounts,
        ]);
    }
}
