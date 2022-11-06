<?php

namespace App\Controller\Api;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TaskApiController extends AbstractController
{
	private EntityManagerInterface $entityManager;
	private $taskRepository;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->taskRepository = $entityManager->getRepository(Task::class);
	}

	#[Route('/aaaaa', name: 'app_task_addd', methods: 'POST')]
	public function create(Request $request): JsonResponse
	{

		$data = $request->getContent();

		$task = new Task();

		$task->setTask('Nowe zadanieee z API')
			->setDescription('opis tego xadaniaaaa')
			->setStatus(1)
			->setCreatedAt(new \DateTimeImmutable());

		$this->entityManager->persist($task);
		$this->entityManager->flush();

		return new JsonResponse('Zadanie zostalo utworzone! ID:'.$task->getId(), Response::HTTP_CREATED);

	}

	#[Route('/api/task/{id}', name: 'app_task_get_one', methods: 'GET')]
	public function getOne(int $id): JsonResponse
	{
		$task = $this->taskRepository->findOneBy(['id' => $id]);

		if ($task == null) {
			return new JsonResponse(['status' => 'Wskazane zadanie nie istnieje'], Response::HTTP_NOT_FOUND);

		} else {
			$data = [
				'id' => $task->getId(),
				'task' => $task->getTask(),
				'description' => $task->getDescription(),
			];

			return new JsonResponse($data, Response::HTTP_OK);
		}
	}

	#[Route('/api/task', name: 'app_task_get_list', methods: 'GET')]
	public function getAll(): JsonResponse
	{

		$tasks = $this->taskRepository->findAll();

		$data = [];

		foreach ($tasks as $task) {
			$data[] = [
				'id' => $task->getId(),
				'task' => $task->getTask(),
				'description' => $task->getDescription(),

			];
		}

		return new JsonResponse($data, Response::HTTP_OK);

	}

	#[Route('/api/task/{id}', name: 'app_task_remove', methods: 'DELETE')]
	public function remove(int $id):JsonResponse
	{

		$task = $this->taskRepository->findOneBy(['id' => $id]);
		if ($task == null) {
			return new JsonResponse(['status' => 'Wskazane zadanie nie istnieje'], Response::HTTP_NOT_FOUND);
		} else {

			$this->entityManager->remove($task);
			$this->entityManager->flush();

			return new JsonResponse(['status' => 'Zadanie zostalo usuniete.'], Response::HTTP_OK);
		}
	}

}
