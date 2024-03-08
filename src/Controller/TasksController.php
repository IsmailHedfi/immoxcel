<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use SebastianBergmann\Environment\Console;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TasksController extends AbstractController
{
    #[Route('/tasks', name: 'app_tasks')]
    public function index(Request $request, SessionInterface $session): Response
    {
        $username = $session->get('username');
        // Create a new project instance
        $task = new Task();

        // Create the form for adding a new project
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        // Check if form is submitted and valid, then save the new project
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the project entity
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            // Redirect to the same page to display the updated list
            return $this->redirectToRoute('app_tasks');
        }

        // Get all existing projects
        $tasks = $this->getDoctrine()->getRepository(Task::class)->findAll();

        // Render the template with the form and the list of projects
        return $this->render('tasks/index.html.twig', [
            'form' => $form->createView(),
            'tasks' => $tasks,
            'username' => $username
        ]);
    }

    #[Route('/Tasks/edit/{id}', name: 'app_tasks_edit')]
    public function editTask($id, Request $request, EntityManagerInterface $entityManager, TaskRepository $taskRepository, SessionInterface $session): Response
    {
        $username = $session->get('username');
        $task = $taskRepository->find($id);

        if (!$task) {
            throw $this->createNotFoundException('The task does not exist');
        }

        // Get the project associated with the task
        $project = $task->getProject();

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('project_tasks', ['projectId' => $project->getId()]);
        }

        return $this->renderForm('tasks/edittask.html.twig', [
            'task' => $task,
            'form' => $form,
            'project' => $project,
            'username' => $username // Pass the project to the template
        ]);
    }
    #[Route('/Tasks/details/{id}', name: 'app_task_details')]
    public function detailsTask($id, TaskRepository $taskRepository, SessionInterface $session): Response
    {
        $username = $session->get('username');
        $task = $taskRepository->find($id);

        if (!$task) {
            throw $this->createNotFoundException('The task does not exist');
        }

        // Get the project associated with the task
        $project = $task->getProject();


        return $this->renderForm('tasks/taskdetails.html.twig', [
            'project' => $project,
            'task' => $task,
            'username' => $username // Pass the project to the template
        ]);
    }

    #[Route('/task/delete/{id}', name: 'app_tasks_delete')]
    public function deleteTask($id, EntityManagerInterface $entityManager, TaskRepository $taskRepository): Response
    {
        $task = $taskRepository->find($id);

        if (!$task) {
            throw $this->createNotFoundException('The task does not exist');
        }

        // Get the project associated with the task
        $project = $task->getProject();

        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('project_tasks', ['projectId' => $project->getId()]);
    }

    #[Route('/projects/{projectId}/tasks', name: 'project_tasks')]
    public function projectTasks($projectId, Request $request, TaskRepository $taskRepository, SessionInterface $session): Response
    {
        $username = $session->get('username');
        // Retrieve the project by its ID
        $project = $this->getDoctrine()->getRepository(Project::class)->find($projectId);

        if (!$project) {
            throw $this->createNotFoundException('The project does not exist');
        }

        // Create a new task instance
        $task = new Task();
        // Set the project for the task
        $task->setProject($project);

        // Create the form for adding a new task
        $form = $this->createForm(TaskType::class, $task);

        // Handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the task entity
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            // Redirect to the same page to display the updated list
            return $this->redirectToRoute('project_tasks', ['projectId' => $projectId]);
        }

        // Retrieve tasks associated with the project
        $tasks = $taskRepository->findBy(['project' => $project]);

        // Render the template with the form and other data
        return $this->render('tasks/index.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
            'tasks' => $tasks,
            'username' => $username
        ]);
    }

    #[Route('/update-task-status', name: 'update_task_status', methods: ['POST'])]
    public function updateTaskStatus(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $taskId = $data['taskId']; // Get taskId instead of taskIds
        $status = $data['status'];

        // Get the Doctrine entity manager
        $entityManager = $this->getDoctrine()->getManager();

        // Fetch the task by its ID
        $task = $entityManager->getRepository(Task::class)->find($taskId);

        // Update the status if the task is found
        if ($task) {
            $task->setTaskStatus($status);
            $entityManager->flush(); // Flush changes to synchronize with the database

            // Return a JSON response indicating success
            return new JsonResponse(['message' => 'Task status updated successfully'], Response::HTTP_OK);
        } else {
            // Return a JSON response indicating failure (task not found)
            return new JsonResponse(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }
    }
}
