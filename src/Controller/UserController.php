<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UpdateType;
use App\Form\UserType;
use App\Form\LoginType;
use App\Repository\EmployeesRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    #[Route('/dashboard', name: 'display_dashboard')]
    public function AdminDashboard(UserRepository $rep ,EmployeesRepository $rep1,SessionInterface $session)
    {
        $userid=$session->get('user_id');
        if(!$userid){var_dump('null');}
        $user_to_show=$rep->find($userid);
        $username = $user_to_show->getUsername();
        $isAdmin = $user_to_show->getEmployee()->getEmpFunction();
        if ($isAdmin!='Admin') {
            return $this->render('admin/access_denied.html.twig');
        }
        $emp=$rep1->findAll();
        $users=$rep->findAll();
        $num_emp=count($emp);
        $num_users=count($users);
        if($num_users>0 && $num_emp>0)
            {
                return $this->render('admin/AdminDashboard.html.twig',['users'=>$users,'numUsers' => $num_users,'numEmp'=>$num_emp,'username'=>$username]);
            }
        else
            {
                return $this->render('admin/AdminDashboard.html.twig',['users'=>$users,'numUsers' => $num_users,'numEmp'=>$num_emp,'username'=>$username]);
            }
        
    }
    #[Route('/login', name: 'display_login')]
    public function login(HttpFoundationRequest $request, UserRepository $userRep,UserPasswordEncoderInterface $passwordEncoder,SessionInterface $session)
    {
        $user1=new User();
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);
        
        if($form->isSubmitted()){
            $userRepository = $this->getDoctrine()->getRepository(User::class);
            $username=$form->get('Username')->getData();
            $password=$form->get('Password')->getData();
            $user=$userRepository->findOneBy(['Username' => $username]); 
            
          if($user && $passwordEncoder->isPasswordValid($user, $password))
          {
            $role=$user->getEmployee()->getEmpFunction();
            if($role=="Admin"){
                $session->set('user_id', $user->getId());
                
                return $this->redirectToRoute('display_admin');
            }
            else if($role!="Admin")
            {
                $session->set('user_id', $user->getId());
                
                return $this->redirectToRoute('display_work');
            }
          }
          else if(!$user)
          {
            $form->get('Username')->addError(new FormError('User not found'));
          }
          else 
          {
            $form->get('Username')->addError(new FormError('Wrong Username or Password'));
            $form->get('Password')->addError(new FormError('Wrong Username or Password'));
          }
        }    
        
        return $this->render('admin/login.html.twig', ['f' => $form->createView()]);
    }
    
    #[Route('/sign_up', name: 'display_admin_sign_up')]
    public function sign_up(HttpFoundationRequest $request, UserPasswordEncoderInterface $passwordEncoder,UserRepository $rep,SessionInterface $session)
    {
        $userid=$session->get('user_id');
        $user_to_show=$rep->find($userid);
        $username = $user_to_show->getUsername();
        $isAdmin = $user_to_show->getEmployee()->getEmpFunction();
        if ($isAdmin!='Admin') {
            return $this->render('admin/access_denied.html.twig');
        }
        $sign_up=new User();
        $form=$this->createForm(UserType::class,$sign_up);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
            {
                $ConfirmPassword=$request->request->get('confirm_password'); 
                $Password=$form->get('Password')->getData();
                if($ConfirmPassword===$Password){
                $encodedPassword=$passwordEncoder->encodePassword($sign_up,$form->get('Password')->getData());
               
                $sign_up->setPassword($encodedPassword);
                $em=$this->getDoctrine()->getManager();
                $em->persist($sign_up);
                $em->flush();
                return $this->redirectToRoute('display_admin');
                }
                else
                    {
                        $form->get('Password')->addError(new FormError('Passwords must match'));
                    }
            }
        return $this->render('admin/sign_up.html.twig', ['f'=>$form->createView(),'username'=>$username]);
    }
    #[Route('/profile/{id}', name: 'display_profile')]
    public function profile(UserRepository $rep,$id,HttpFoundationRequest $request, UserPasswordEncoderInterface $passwordEncoder,SessionInterface $session)
    {
        $userid=$session->get('user_id');
        $user_to_show=$rep->find($userid);
        $username = $user_to_show->getUsername();
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $profile=$userRepository->find($id);
        $form=$this->createForm(UpdateType::class,$profile);
        $form->handleRequest($request);
        if($form->isSubmitted())
            {  
                $newPassword = $request->request->get('new_password'); 
                $oldPassword = $request->request->get('old_password');
                if(!empty($newPassword)&&!empty($oldPassword)){
                if($passwordEncoder->isPasswordValid($profile, $oldPassword) && strlen($newPassword)>=8){
            
                    $hashedPassword = $passwordEncoder->encodePassword($profile, $newPassword);
                    $profile->setPassword($hashedPassword);
                    $entityManager=$this->getDoctrine()->getManager(); 
                    $entityManager->flush();
                    return $this->redirectToRoute('display_admin');
                }
                else if(!$passwordEncoder->isPasswordValid($profile, $oldPassword))
                    {
                        $error_old = 'Wrong old Password';
                            return $this->render('admin/profile.html.twig', ['f'=>$form->createView(),'error_old' => $error_old,'username'=>$username]);
                    }
                    else
                    {
                        $error_new = 'Password must be at least 8 characters long';
                            return $this->render('admin/profile.html.twig', ['f'=>$form->createView(),'error_new' => $error_new,'username'=>$username]);
                    }
                }
                else
                {
                    $profile->setPassword($profile->getPassword());
                    $entityManager=$this->getDoctrine()->getManager(); 
                    $entityManager->flush();
                    return $this->redirectToRoute('display_admin');
                }
            }
               
        return $this->render('admin/profile.html.twig', ['f'=>$form->createView(),'username'=>$username]);
    }
    #[Route('/delete_User/{id}', name: 'delete_admin_user')]
        public function deleteUser($id,UserRepository $rep)
            {
                $factory=$rep->find($id);
                $em=$this->getDoctrine()->getManager();
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
        $userid=$session->get('user_id');
        $user_to_show=$this->getDoctrine()->getRepository(User::class)->find($userid);
        $username = $user_to_show->getUsername();
        
        return $this->render('admin/work_in_progress.html.twig',['username'=>$username]);
    }
}
