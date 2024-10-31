<?php
namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/task/new', name: 'task_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Associer l'utilisateur connecté à la tâche
            $task->setAssignedUser($this->getUser());

            // Sauvegarder la nouvelle tâche
            $entityManager->persist($task);
            $entityManager->flush();

            // Rediriger vers la page listant toutes les tâches
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/task/toggle/{id}', name: 'task_toggle')]
    public function toggleStatus(Task $task, EntityManagerInterface $entityManager): Response
    {
        $task->setIsDone(!$task->getIsDone());
        $entityManager->flush();

        return $this->redirectToRoute('task_list');
    }

    #[Route('/task', name: 'task_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        // Récupère les tâches de l'utilisateur connecté uniquement
        $tasks = $entityManager->getRepository(Task::class)->findBy(['assignedUser' => $this->getUser()]);

        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}