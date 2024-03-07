<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UpdateType;
use App\Form\UserType;
use App\Form\LoginType;
use App\Form\ResetPassType;
use App\Form\ResetPasswordType;
use App\Repository\EmployeesRepository;
use App\Repository\UserRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Knp\Component\Pager\PaginatorInterface;

class UserController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    #[Route('/dashboard', name: 'display_dashboard')]
    public function AdminDashboard(HttpFoundationRequest $request, UserRepository $rep, PaginatorInterface $paginator, EmployeesRepository $rep1, SessionInterface $session)
    {
        $userid = $session->get('user_id');
        $user_to_show = $rep->find($userid);
        $username = $user_to_show->getUsername();
        $isAdmin = $user_to_show->getEmployee()->getEmpFunction();
        if ($isAdmin != 'Admin') {
            return $this->render('admin/access_denied.html.twig');
        }
        $emp = $rep1->findAll();
        $users = $rep->findAll();
        $reversed_users = array_reverse($users);
        $num_emp = count($emp);
        $num_users = count($users);
        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->getRepository(User::class)->createQueryBuilder('m')->orderBy('m.id', 'DESC')
            ->getQuery();

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 2);
        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
        $user = $paginator->getIterator();
        $totalCount = count($paginator);
        $totalPages = ceil($totalCount / $limit);
        if ($num_users > 0 && $num_emp > 0) {
            return $this->render('admin/AdminDashboard.html.twig', ['users' => $user, 'numUsers' => $num_users, 'numEmp' => $num_emp, 'username' => $username, 'totalPages' => $totalPages, 'page' => $page]);
        } else {
            return $this->render('admin/AdminDashboard.html.twig', ['users' => $reversed_users, 'numUsers' => $num_users, 'numEmp' => $num_emp, 'username' => $username, 'totalPages' => $totalPages, 'page' => $page]);
        }
    }
    #[Route('/login', name: 'display_login')]
    public function login(HttpFoundationRequest $request, UserRepository $userRep, UserPasswordEncoderInterface $passwordEncoder, SessionInterface $session)
    {
        $user1 = new User();
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $userRepository = $this->getDoctrine()->getRepository(User::class);
            $username = $form->get('Username')->getData();
            $password = $form->get('Password')->getData();
            $user = $userRepository->findOneBy(['Username' => $username]);
            $recaptchaResponse = $request->request->get('g-recaptcha-response');
            $recaptchaData = $this->verifyCaptcha($recaptchaResponse);
            if ($user && $passwordEncoder->isPasswordValid($user, $password) && $recaptchaData->success) {
                if ($user->getIsVerified() == false) {
                    $this->addFlash('warning', 'Account not activated. <a href="' . $this->generateUrl('display_resend_verif', ['id' => $user->getId()]) . '">Resend activation link</a>');
                } else {
                    $role = $user->getEmployee()->getEmpFunction();
                    $session->start();
                    $session->set('user', $user);
                    $session->set('user_id', $user->getId());
                    $session->set('emp', $user->getEmployee()->getId());
                    $session->set('role', $user->getEmployee()->getEmpFunction());
                    if ($role == "Admin") {
                        return $this->redirectToRoute('display_admin');
                    } else if ($role == "HR_Manager") {
                        return $this->redirectToRoute('app_employees');
                    } else if ($role == "Production_Manager") {
                        return $this->redirectToRoute('app_projects');
                    } else if ($role == "Inventory_Manager") {
                        return $this->redirectToRoute('display_afficherdepot');
                    } else if ($role != "Admin") {
                        return $this->redirectToRoute('display_work');
                    }
                }
            } else if (!$user) {
                $form->get('Username')->addError(new FormError('User not found'));
            } else if (!$recaptchaData->success) {
                $this->addFlash('warning', 'reCAPTCHA verification failed. Please try again.');
            } else {
                $form->get('Username')->addError(new FormError('Wrong Username or Password'));
                $form->get('Password')->addError(new FormError('Wrong Username or Password'));
            }
        }

        return $this->render('admin/login.html.twig', ['f' => $form->createView()]);
    }

    private function verifyCaptcha(string $recaptchaResponse): object
    {
        $recaptchaSecret = '6LcOUYcpAAAAANpKXcd5P0MIsRX5_x7N30RIlcD8'; // Replace with your reCAPTCHA secret key
        $recaptchaVerifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptchaVerifyResponse = file_get_contents($recaptchaVerifyUrl . '?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);
        return json_decode($recaptchaVerifyResponse);
    }

    #[Route('/sign_up', name: 'display_admin_sign_up')]
    public function sign_up(HttpFoundationRequest $request, SendMailService $mail, JWTService $jwt, UserPasswordEncoderInterface $passwordEncoder, UserRepository $rep, SessionInterface $session)
    {
        $userid = $session->get('user_id');
        $user_to_show = $rep->find($userid);
        $username = $user_to_show->getUsername();
        $isAdmin = $user_to_show->getEmployee()->getEmpFunction();
        if ($isAdmin != 'Admin') {
            return $this->render('admin/access_denied.html.twig');
        }

        $sign_up = new User();
        $form = $this->createForm(UserType::class, $sign_up);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ConfirmPassword = $request->request->get('confirm_password');
            $Password = $form->get('Password')->getData();
            $username = $form->get('Username')->getData();
            $userRepository = $this->getDoctrine()->getRepository(User::class);
            $user = $userRepository->findOneBy(['Username' => $username]);
            if ($user) {


                $form->get('Username')->addError(new FormError('Username already taken'));
            } else {
                if ($ConfirmPassword === $Password) {
                    $encodedPassword = $passwordEncoder->encodePassword($sign_up, $form->get('Password')->getData());
                    $sign_up->setPassword($encodedPassword);
                    $sign_up->setIsVerified(false);
                    $sign_up->setresetToken('');
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($sign_up);
                    $em->flush();

                    #Create Header
                    $header = [
                        'typ' => 'JWT',
                        'alg' => 'HS256'
                    ];

                    #Create Payload
                    $payload = [
                        'user_id' => $sign_up->getId()
                    ];

                    #Generate Token
                    $token = $jwt->generateToken($header, $payload, $this->getParameter('app.jwtservice'));
                    #Send verification mail
                    $mail->SendMail('noreply@ImmoXcel.com', $sign_up->getEmail(), 'ImmoXcel Account Activiation', 'register', ['user' => $sign_up, 'token' => $token]);
                    return $this->redirectToRoute('display_admin');
                } else if ($ConfirmPassword != $Password) {
                    $form->get('Password')->addError(new FormError('Passwords must match'));
                }
            }
        }
        return $this->render('admin/sign_up.html.twig', ['f' => $form->createView(), 'username' => $username]);
    }
    #[Route('/profile/{id}', name: 'display_profile')]
    public function profile(UserRepository $rep, $id, HttpFoundationRequest $request, UserPasswordEncoderInterface $passwordEncoder, SessionInterface $session)
    {
        $userid = $session->get('user_id');
        $user_to_show = $rep->find($userid);
        $username = $user_to_show->getUsername();
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $profile = $userRepository->find($id);
        $form = $this->createForm(UpdateType::class, $profile);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $newPassword = $request->request->get('new_password');
            $oldPassword = $request->request->get('old_password');
            if (!empty($newPassword) && !empty($oldPassword)) {
                if ($passwordEncoder->isPasswordValid($profile, $oldPassword) && strlen($newPassword) >= 8) {

                    $hashedPassword = $passwordEncoder->encodePassword($profile, $newPassword);
                    $profile->setPassword($hashedPassword);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->flush();
                    return $this->redirectToRoute('display_admin');
                } else if (!$passwordEncoder->isPasswordValid($profile, $oldPassword)) {
                    $error_old = 'Wrong old Password';
                    return $this->render('admin/profile.html.twig', ['f' => $form->createView(), 'error_old' => $error_old, 'username' => $username]);
                } else {
                    $error_new = 'Password must be at least 8 characters long';
                    return $this->render('admin/profile.html.twig', ['f' => $form->createView(), 'error_new' => $error_new, 'username' => $username]);
                }
            } else {
                $profile->setPassword($profile->getPassword());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
                return $this->redirectToRoute('display_admin');
            }
        }

        return $this->render('admin/profile.html.twig', ['f' => $form->createView(), 'username' => $username]);
    }
    #[Route('/delete_User/{id}', name: 'delete_admin_user')]
    public function deleteUser($id, UserRepository $rep)
    {
        $factory = $rep->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($factory);
        $em->flush();
        return $this->redirectToRoute('display_admin');
    }
    public function startSession(SessionInterface $session)
    {
        // Start a session
        $session->start();

        // Set data in the session
        $session->set('key', 'value');

        // Retrieve data from the session
        $value = $session->get('key');
    }
    #[Route('/logout', name: 'go_to_logout')]
    public function logout(SessionInterface $session)
    {
        // Invalidate the session
        $session->invalidate();

        // Redirect to the login page
        return $this->redirectToRoute('display_home');
    }
    #[Route('/home', name: 'display_home')]
    public function home()
    {

        return $this->render('admin/home.html.twig');
    }
    #[Route('/work_in_progress', name: 'display_work')]
    public function work(SessionInterface $session)
    {
        $userid = $session->get('user_id');
        $user_to_show = $this->getDoctrine()->getRepository(User::class)->find($userid);
        $username = $user_to_show->getUsername();

        return $this->render('admin/work_in_progress.html.twig', ['username' => $username]);
    }
    #[Route('/verif/{token}', name: 'verif_user')]
    public function VerifyUser($token, JWTService $jwt, UserRepository $User_rep, EntityManagerInterface $em, SessionInterface $session): Response
    {
        if (
            $jwt->IsTokenValid($token) && !$jwt->isTokenExpired($token)
            && $jwt->VerifySignature($token, $this->getParameter('app.jwtservice'))
        ) {
            $payload = $jwt->getPayload($token);
            $user = $User_rep->find($payload['user_id']);
            if ($user && !$user->getIsVerified()) {
                $user->setIsVerified(true);
                $em->flush($user);
                $this->addFlash('success', 'Account Verified');
                return $this->redirectToRoute('display_login');
            }
        }
        $this->addFlash('danger', 'Invalid or Expired Token');

        return $this->redirectToRoute('display_login');
    }

    #[Route('/resend_verif/{id}', name: 'display_resend_verif')]
    public function resendVerif(JWTService $jwt, UserRepository $User_rep, SendMailService $mail, SessionInterface $session, $id): response
    {
        $user = $User_rep->find($id);
        #Create Header
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        #Create Payload
        $payload = [
            'user_id' => $id
        ];

        #Generate Token
        $token = $jwt->generateToken($header, $payload, $this->getParameter('app.jwtservice'));

        #Send verification mail
        $mail->SendMail('noreply@ImmoXcel.com', $user->getEmail(), 'ImmoXcel Account Activiation', 'register', ['user' => $user, 'token' => $token]);
        $this->addFlash('success', 'Validation Mail Sent Successfully');
        return $this->redirectToRoute('display_login');
    }

    #[Route('/forgetPassword', name: 'display_forgetPassword')]
    public function forgottenPassword(HttpFoundationRequest $request, TokenGeneratorInterface $tokenGenrator, EntityManagerInterface $em, SendMailService $mail): response
    {
        $user = new User();
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $email = $form->get('Email')->getData();
            $userRepository = $this->getDoctrine()->getRepository(User::class);

            $userRepository = $this->getDoctrine()->getRepository(User::class);
            $user = $userRepository->findOneBy(['Email' => $email]);

            if ($user) {

                $token = $tokenGenrator->generateToken();
                $user->setresetToken($token);
                $em->persist($user);
                $em->flush();
                $url = $this->generateUrl('resetPass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                $context = [
                    'url' => $url,
                    'user' => $user
                ];
                $mail->SendMail('noreply@ImmoXcel.com', $user->getEmail(), 'Reset Your Password', 'resetPasswordMail', $context);
                $this->addFlash('Success', 'Mail sent Seccessfully');
                return $this->redirectToRoute('display_login');
            } else {
                $this->addFlash('warning', 'Problem has been Occured');
                return $this->redirectToRoute('display_login');
            }
        }
        return $this->render('admin/reset_password_request.html.twig', ['f' => $form->createView()]);
    }

    #[Route('/resetPassword/{token}', name: 'resetPass')]
    public function resetPassword(
        $token,
        HttpFoundationRequest $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em
    ): Response {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->findOneBy(['resetToken' => $token]);
        if ($user) {
            $form = $this->createForm(ResetPassType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                $user->setresetToken('');

                $ConfirmPassword = $request->request->get('confirm_password');
                $Password = $form->get('Password')->getData();
                var_dump($Password);
                var_dump($ConfirmPassword);
                if ($Password === $ConfirmPassword) {
                    var_dump($Password);
                    $encodedPassword = $passwordEncoder->encodePassword($user, $Password);
                    $user->setPassword($encodedPassword);
                    $em->persist($user);
                    $em->flush();
                    $this->addFlash('Success', 'Password Updated');
                    return $this->redirectToRoute('display_login');
                }
            }
            return $this->render('admin/resetPassword.html.twig', ['f' => $form->createView()]);
        }
        $this->addFlash('danger', 'invalid Token');
        $this->redirectToRoute('display_login');
    }
}
