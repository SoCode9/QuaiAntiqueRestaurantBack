<?php

namespace App\Controller;

use App\Entity\Food;
use App\Form\FoodForm;
use App\Repository\FoodRepository;
use App\Repository\FoodCategoryRepository;
use App\Repository\CategoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/food', name: 'app_api_food_')]
final class FoodController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $manager,
        private FoodRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}
    #[Route('/new',name: 'new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $food = $this->serializer->deserialize($request->getContent(), Food::class, 'json');
        $food->setCreatedAt(new \DateTimeImmutable());

        $this->manager->persist($food); // Prepare the entity for saving
        $this->manager->flush(); // Save the entity to the database

        $responseData = $this->serializer->serialize($food, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_food_show',
            ['id' => $food->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        ); // pour être conforme aux standards RESTFul, on génère une URL vers le « `_show_` » de l’objet récemment créé au format JSON.

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }


    #[Route('/carte/{category}', name: 'show_carte', methods: ['GET'])]
    public function getAllFood(string $category, CategoryRepository $cRepository, FoodCategoryRepository $fcRepository, FoodRepository $foodRepository): JsonResponse
    {

        $category = $cRepository->findOneBy(['title' => $category]);
        if (!$category) {
            return $this->json(['error' => 'Catégorie non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $idCategory = $category->getId();

        $foodCategoryLinks = $fcRepository->findBy(['categoryId' => $idCategory]);

        $foodIds = array_map(function ($link) {
            return $link->getFoodId()->getId();
        }, $foodCategoryLinks);

        if (empty($foodIds)) {
            return $this->json([], Response::HTTP_OK);
        }

        // Puis on récupère les Food correspondants
        $foods = $foodRepository->findBy(['id' => $foodIds]);

        return new JsonResponse(
            $this->serializer->serialize($foods, 'json', ['groups' => 'carte']),
            Response::HTTP_OK,
            [],
            true
        );
    }


    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $food = $this->repository->findOneBy(['id' => $id]);

        if ($food) {
            $responseData = $this->serializer->serialize($food, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, []);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND, []);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): Response
    {
        $food = $this->repository->findOneBy(['id' => $id]);

        if ($food) {
            $food = $this->serializer->deserialize(
                $request->getContent(),
                Food::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $food]
            );

            $food->setUpdatedAt(new DateTimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $food = $this->repository->findOneBy(['id' => $id]);
        if ($food) {
            $this->manager->remove($food);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
