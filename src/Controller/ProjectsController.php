<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use SebastianBergmann\Environment\Console;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProjectsController extends AbstractController
{
    #[Route('/projects', name: 'app_projects')]
    public function index(Request $request): Response
    {
        // Create a new project instance
        $project = new Project();

        // Create the form for adding a new project
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        // Check if form is submitted and valid, then save the new project
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the project entity
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            // Redirect to the same page to display the updated list
            return $this->redirectToRoute('app_projects');
        }

        // Get all existing projects
        $projects = $this->getDoctrine()->getRepository(Project::class)->findAll();

        // Render the template with the form and the list of projects
        return $this->render('projects/projects2.html.twig', [
            'form' => $form->createView(),
            'projects' => $projects,
        ]);
    }



    #[Route('/projects/edit/{id}', name: 'app_projects_edit')]
    public function EditProject($id,Request $req,EntityManagerInterface $en,ProjectRepository $pR): Response
    {
        $project=$pR->find($id);
        $form=$this->createForm(ProjectType::class,$project);
        //$form->add('submit', SubmitType::class);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $en->persist($project);
            $en->flush();
            return $this->redirectToRoute('app_projects');
        }
        return $this->renderForm('projects/Editproject.html.twig',['form'=>$form]);
    }

    #[Route('/projects/delete/{id}', name: 'app_projects_delete')]
    public function deletecar($id,EntityManagerInterface $en,ProjectRepository $pR): Response
    {
        $project=$pR->find($id);
        $en->remove($project);
        $en->flush();
        return $this->redirectToRoute('app_projects');
    }
}
