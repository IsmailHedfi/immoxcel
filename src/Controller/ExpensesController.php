<?php

namespace App\Controller;

use App\Entity\Capital;
use App\Entity\Expenses;
use App\Form\ExpensesType;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\ExpensesRepository;
use App\Repository\CapitalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\SmsGenerator;
use Twilio\Rest\Client;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use TCPDF; // Import TCPDF class
use Twilio\Rest\Proxy\V1\Service\SessionInstance;

class ExpensesController extends AbstractController
{
    #[Route('/expenses', name: 'app_expensesdis')]
    public function index(ExpensesRepository $rep, Request $request, SessionInterface $session): Response
    {
        $username = $session->get('username');
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $expenses = $rep->findbySearch($searchData);

            return $this->render('expenses/DisplayExpenses.html.twig', [
                'form' => $form->createView(),
                'expenses' => $expenses,
                'searchQuery' => $searchData->q,
                'username' => $username // Pass the search query string to the template
            ]);
        }

        // If the form is not submitted or not valid, render the template without passing $searchData
        return $this->render('expenses/DisplayExpenses.html.twig', [
            'form' => $form->createView(),
            'expenses' => $rep->orderByDest(),
            'username' => $username
        ]);
    }


    public function sendSms2(SmsGenerator $smsGenerator, $number, $name, $text)
    {
        $number_test = $_ENV['TWILIO_TO_NUMBER']; // Numéro vérifier par twilio. Un seul numéro autorisé pour la version de test.
        //Appel du service
        $smsGenerator->sendSms($number_test, $name, $text);
    }
    #[Route('/dashboard/expenses', name: 'app_addexpenses')]
    public function addexpensese(Request $request, ExpensesRepository $rep, SmsGenerator $twilioService, MailerInterface $mailer, SessionInterface $session)
    {
        $username = $session->get('username');
        $smsGenerator = new SmsGenerator();
        $expenses = new Expenses();
        $form = $this->CreateForm(ExpensesType::class, $expenses);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $expenses->setArchived(true);
            $expenses->setDateE(new \DateTime());
            // calcule de cout totale
            $total = $expenses->getCoast() * $expenses->getQuantityE();
            $expenses->setTotalAmount($total);
            // ajout de la quantite des matiaux
            // $Materials = $expenses->getProduct();
            // $newQuantity = $Materials->getQuantity() + $expenses->getQuantityE();
            // $Materials->setQuantity($newQuantity);
            // gerer le capital 
            $capitalId = 1; // Replace 1 with the ID you want to fetch
            $capitalRepository = $this->getDoctrine()->getRepository(Capital::class);
            $capital = $capitalRepository->find($capitalId);
            $salaryExpense = $rep->findSalaryExpenseForCurrentMonth($capitalId);
            if ($expenses->getType() == 'Income') {
                $newCap = $expenses->getTotalAmount() + $capital->getProfits();
                $capital->setProfits($newCap);
                $bigcap = $capital->getBigCapital()  + $capital->getProfits();

                $capital->setBigCapital($bigcap);
                $addexpenses = $capital->getBigCapital() * (0.3);
                //$addsalary = $capital->getBigCapital() * (0.2);

                $capital->setExpensess($addexpenses);

                //$capital->setSalary($addsalary);
            } elseif ($expenses->getType() == 'Salary') {
                if ($capital->getSalary() >= $expenses->getTotalAmount()) {
                    $newCapS = $capital->getSalary() - $expenses->getTotalAmount();
                    $capital->setSalary($newCapS);
                    $bigcap = $capital->getBigCapital()  - $capital->getSalary();
                    $capital->setBigCapital($bigcap);
                } else {
                    $this->addFlash('error', 'You don\'t have enough money to pay the salary .');
                    return $this->redirectToRoute('app_expensesdis');
                }
            } else {
                if ($capital->getExpensess() >= $expenses->getTotalAmount()) {
                    $newCapS = $capital->getExpensess() - $expenses->getTotalAmount();
                    $capital->setExpensess($newCapS);
                    $bigcap = $capital->getBigCapital() - $capital->getExpensess();
                    $capital->setBigCapital($bigcap);
                } else {
                    $this->addFlash('error', 'You don\'t have enough money.');
                    return $this->redirectToRoute('app_expensesdis');
                }
            }
            // sms
            $text = "Hello! New Transaction " . $expenses->getType() . " with this amount " . $expenses->getTotalAmount() . " Check it out ";
            //$this->sendSms2($smsGenerator, "+21646381005", "Admin", $text);
            $email = (new Email())
                ->from('souleimarjab@gmail.com')
                ->to('souleimarjab@gmail.com')
                ->text($text);
            $mailer->send($email);
            $em = $this->getDoctrine()->getManager();
            $em->persist($expenses);
            $em->persist($capital);
            // $em->persist($Materials);
            $em->flush();

            $this->addFlash('success', 'Expenses added successfully.');
            return $this->redirectToRoute('app_expensesdis');
        }
        // si le formulaire n'est pas valide ou il n'a pas submitted on va le retourner a la vue de l'ajout pour ajouter une autre fois 
        return $this->render('expenses/add.html.twig', ['f' => $form->createView(), 'username' => $username]);
    }
    #[Route('/dashboard/edditTransaction/{id}', name: 'app_editTransaction')]
    public function EditEpnses(ExpensesRepository $rep, $id, Request $request, SessionInterface $session)
    {
        $username = $session->get('username');
        $expenses = $rep->find($id);
        if (!$expenses) {
            throw $this->createNotFoundException('The Transaction with id ' . $id . ' does not exist');
        }
        $form = $this->CreateForm(ExpensesType::class, $expenses);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $expenses->setArchived(true);

            $total = $expenses->getCoast() * $expenses->getQuantityE();
            $expenses->setTotalAmount($total);
            // ajout de la quantite des matiaux
            // $Materials = $expenses->getProduct();
            // $newQuantity = $Materials->getQuantity() + $expenses->getQuantityE();
            // $Materials->setQuantity($newQuantity);
            //$expenses->setDateE(new \DateTime());
            $capital = $expenses->getCapital();
            $capitalId = 1; // Replace 1 with the ID you want to fetch
            $capitalRepository = $this->getDoctrine()->getRepository(Capital::class);
            $capital = $capitalRepository->find($capitalId);
            $expenses->setTotalAmount($expenses->getCoast() * $expenses->getQuantityE());
            if ($expenses->getType() == 'Income') {
                $newCap = $expenses->getTotalAmount() + $capital->getProfits();
                $capital->setProfits($newCap);
                $bigcap = $capital->getBigCapital()  + $capital->getProfits();
                $capital->setBigCapital($bigcap);
                $addexpenses = $capital->getBigCapital() * (0.07);
                $capital->setExpensess($addexpenses);
            } else if ($expenses->getType() == 'Salary') {

                if ($capital->getSalary() >= $expenses->getTotalAmount()) {
                    $newCapS = $capital->getSalary() - $expenses->getTotalAmount();
                    $capital->setSalary($newCapS);
                    $bigcap = $capital->getBigCapital()  - $capital->getSalary();
                    $capital->setBigCapital($bigcap);
                } else {
                    $this->addFlash('error', 'You don\'t have enough money.');
                    return $this->redirectToRoute('app_expensesdis');
                }
            } else {
                if ($capital->getExpensess() >= $expenses->getTotalAmount()) {
                    $newCapS = $capital->getExpensess() - $expenses->getTotalAmount();
                    $capital->setExpensess($newCapS);
                    $bigcap = $capital->getBigCapital() - $capital->getExpensess();
                    $capital->setBigCapital($bigcap);
                } else {
                    $this->addFlash('error', 'You don\'t have enough money.');
                    return $this->redirectToRoute('app_expensesdis');
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($expenses);
            $em->persist($capital);
            // $em->persist($Materials);
            $em->flush();  // O n L'uitilise sur entitymanager pour energistrer les modifications en base de donee 
            $this->addFlash('success', 'Transaction added successfully.');

            return $this->redirectToRoute('app_expensesdis');
        }
        // si le formulaire n'est pas valide ou il n'a pas submitted on va le retourner a la vue de l'ajout pour ajouter une autre fois 
        return $this->render('expenses/add.html.twig', ['f' => $form->createView(), 'username' => $username]);
    }
    #[Route('/dashboard/DeleteExp/{id}', name: 'app_deltexp')]
    public function DeleteEpe(ExpensesRepository $rep, $id)
    {
        $expenses = $rep->find($id);
        if (!$expenses) {
            throw $this->createNotFoundException('The Expenss with id ' . $id . ' does not exist');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($expenses);
        $em->flush();  // O n L'uitilise sur entitymanager pour energistrer les modifications en base de donee 

        return $this->redirectToRoute('app_expensesdis');
    }
    #[Route('/dashboard/archiveExp/{id}', name: 'app_archive_expenses')]
    public function archiveExp(ExpensesRepository $rep, $id): Response
    {

        $expenses = $rep->find($id);
        if (!$expenses) {
            throw $this->createNotFoundException('The Transaction with id ' . $id . ' does not exist');
        }
        // Archive the transaction
        $expenses->toggleArchived();
        $em = $this->getDoctrine()->getManager();
        $em->persist($expenses);
        $em->flush();

        return $this->redirectToRoute('app_dashboard_page');
    }
    #[Route('/dashboard/archive', name: 'app_archiveyo')]
    public function archived(ExpensesRepository $rep, SessionInterface $session): Response
    {
        $username = $session->get('username');
        $expenses = $rep->findNotActiveTransactions();
        return $this->render('expenses/archive.html.twig', [
            'expenses' => $expenses,
            'username' => $username
        ]);
    }

    #[Route('/dashboard/pdf/{id}', name: 'PDF_genrate')]
    public function generatePdf(ExpensesRepository $expenRep, $id): Response
    {
        $expenses = $expenRep->find($id);

        // Handle case where expense is not found
        if (!$expenses) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        // Load the facture-style PDF template
        $html = $this->renderView('/expenses/pdf_template.html.twig', [
            'expenses' => $expenses // Pass the specific expense data to the template
        ]);

        // Create a new PDF object
        $pdf = new TCPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('PDF Transaction');
        $pdf->SetSubject('Transaction Details');
        $pdf->SetKeywords('PDF, Transaction');

        // Set font
        $pdf->SetFont('helvetica', '', 11);

        // Add a page
        $pdf->AddPage();

        // Output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Close and output PDF
        $pdf->Output('transaction.pdf', 'D'); // 'D' option to force download

        // Return a Symfony response
        return new Response('', Response::HTTP_OK, [
            'Content-Type' => 'application/pdf'
        ]);
    }
    #[Route('/dashboard/desc', name: 'app_desc', methods: 'GET')]
    public function order_By_Dest(Request $request, ExpensesRepository $expensesRepository, SessionInterface $session): Response
    {
        $username = $session->get('username');
        $expenseByDest = $expensesRepository->orderByDest();

        return $this->render('expenses/DisplayExpenses.html.twig', [
            'expenses' => $expenseByDest,
            'username' => $username
        ]);
    }


    #[Route('/expenses/hell/{id}', name: 'app_exHell')]
    public function hell(ExpensesRepository $expenRep, $id, SessionInterface $session): Response
    {
        $username = $session->get('username');
        $expenses = $expenRep->find($id);

        return $this->render('expenses/Hello.html.twig', [
            'id' => $expenses->getId(),
            'Type' => $expenses->getType(),
            'Description' => $expenses->getDescription(),
            'Totalamount' => $expenses->getTotalAmount(),
            'username' => $username

        ]);
    }
    /*  public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $posts = $em->getRepository(Expenses::class)->findEntitiesByString($requestString);
        if (!$posts) {
            $result['post']['error'] = "Expenses not found :( ";
        } else {
            $result['posts'] = $this->getRealEntities($posts);
        }
        return new Response(json_encode($result));
    }
    public function getRealEntities($posts)
    {
        foreach ($posts as $posts) {
            $realEntities[$posts->getId()] = [$posts->getDescription(), $posts->getType(), $posts->getCoast(), $posts->getMaterials(), $posts->getProjects(), $posts->getSupplier()];
        }
        return $realEntities;
    }*/
    #[Route('/searchyo', name: 'ajax_search', methods: ['POST'])]
    public function searchAction2(Request $request, ExpensesRepository $rep, NormalizerInterface $normalizer): Response
    {
        // Get the input from the AJAX request
        $input = $request->request->get('input');
        $expenses = $rep->searchByInput($input);


        // Query the database for expenses matching the input
        /* $expensesRepository = $entityManager->getRepository(Expenses::class);
        $expenses = $expensesRepository->createQueryBuilder('e')
            ->where('e.Type LIKE :input')
            ->orWhere('e.Description LIKE :input')
            ->orWhere('e.Totalamount LIKE :input')
            ->setParameter('input', '%' . $input . '%')
            ->getQuery()
            ->getResult();*/
        $jsonContent = $normalizer->normalize($expenses, 'json', ['groups' => 'post:read']);
        if ($expenses) {
            // Render the expenses as HTML table rows
            return new Response(json_encode($jsonContent));
        } else {
            // Return a response indicating no data found
            return new JsonResponse(['message' => 'No data found'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/expenses/searchh', name: 'expenses_search')]
    public function searchExpenses(Request $request, NormalizerInterface $normalizer, ExpensesRepository $expensesRepository): Response
    {
        $query = $request->query->get('query');
        $type = $request->query->get('type', 'All'); // Providing a default value

        $expenses = $expensesRepository->findExpensesBySearch($query, $type);

        $jsonContent = $normalizer->normalize($expenses, 'json', ['groups' => 'expenses']); // Assuming you have serialization groups configured
        $response = new Response(json_encode($jsonContent));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    #[Route('/mailer', name: 'app_mailer')]
    public function upadatedisplay(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('souleimarjab@gmail.com')
            ->to('souleimarjab@gmail.com')
            ->text('Hey ! New transaction yoyo ');
        $mailer->send($email);
        return new Response('Email was sent');
    }

    #[Route('/dashboard/hell', name: 'app_dashboard_page')]
    public function indexdashboard(ExpensesRepository $rep, ChartBuilderInterface $chartBuilder, CapitalRepository $capitalrep, SessionInterface $session): Response
    {
        $username = $session->get('username');
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
                'CapSalary' => $CapSalary,
                'username' => $username
            ]);
        }
    }
}
