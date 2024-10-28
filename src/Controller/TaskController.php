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
            // Sauvegarder la nouvelle tâche
            $entityManager->persist($task);
            $entityManager->flush();

            // Rediriger vers la page listant toute les taches
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/task', name: 'task_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        // Récupère toutes les tâches
        $tasks = $entityManager->getRepository(Task::class)->findAll();

        // Retourne la vue avec les tâches
        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
        ]);
    }


}