<?php

// src/Controller/TaskController.php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_index")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Fetch all tasks from the database
        $tasks = $entityManager->getRepository(Task::class)->findAll();

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/task/new", name="task_new")
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setCreatedAt(new \DateTime()); // Set creation date

            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/task/{id}", name="task_show")
     */
    public function show(Task $task): Response
    {
        // Symfony will automatically fetch the Task by its id from the URL
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/task/{id}/edit", name="task_edit")
     */
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        // Symfony will automatically fetch the Task by its id from the URL
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Save changes to the database

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/task/{id}/delete", name="task_delete", methods={"POST"})
     */
    public function delete(Task $task, EntityManagerInterface $entityManager): Response
    {
        // Symfony will automatically fetch the Task by its id from the URL
        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('task_index');
    }
}


