<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Restaurant;
use App\Form\PictureType;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use DateTimeImmutable;
use Cocur\Slugify\Slugify;

#[Route('/api/picture', name: 'app_api_picture_')]
final class PictureController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private PictureRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    #[Route(name: 'new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $title = $request->request->get('title');
        $restaurantId = $request->request->get('restaurant');
        $file = $request->files->get('file');

        if (!$file) {
            return new JsonResponse(['error' => 'Aucun fichier envoyé'], Response::HTTP_BAD_REQUEST);
        }

        // Gestion du nom du fichier
        $originalExtension = $file->getClientOriginalExtension();
        $safeFilename = (new Slugify())->slugify(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $originalExtension;

        // Déplacement dans public/images/
        $file->move($this->getParameter('upload_directory'), $newFilename);

        // Création de l'entité
        $picture = new Picture();
        $picture->setTitle($title);
        $picture->setSlug($safeFilename);
        $picture->setCreatedAt(new \DateTimeImmutable());
        $picture->setSlug('/images/' . $newFilename); // <-- ce sera la valeur en BDD

        // Restaurant
        $restaurant = $this->manager->getRepository(Restaurant::class)->find($restaurantId);
        if (!$restaurant) {
            return new JsonResponse(['error' => 'Restaurant introuvable'], Response::HTTP_BAD_REQUEST);
        }
        $picture->setRestaurant($restaurant);

        $this->manager->persist($picture);
        $this->manager->flush();

        return new JsonResponse([
            'id' => $picture->getId(),
            'title' => $picture->getTitle(),
            'slug' => $picture->getSlug()
        ], 201);
    }


    #[Route('/restaurant/{id}', name: 'by_restaurant', methods: ['GET'])]
    public function getPicturesByRestaurant(int $id, PictureRepository $pictureRepository): JsonResponse
    {
        $pictures = $pictureRepository->findBy(['restaurant' => $id]);

        return new JsonResponse(
            $this->serializer->serialize($pictures, 'json', ['groups' => ['picture:read']]),
            Response::HTTP_OK,
            [],
            true // <-- très important : on indique que c'est déjà du JSON
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $picture = $this->repository->findOneBy(['id' => $id]);

        if ($picture) {
            return new JsonResponse(
                $this->serializer->serialize($picture, 'json', ['groups' => 'picture:read']),
                Response::HTTP_OK,
                [],
                true
            );
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND, []);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): Response
    {
        $picture = $this->repository->findOneBy(['id' => $id]);

        if ($picture) {
            $picture = $this->serializer->deserialize(
                $request->getContent(),
                Picture::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $picture] //cette option permet de mettre à jour l'objet (pas supprimer et recréer un)
            );


            $picture->setUpdatedAt(new DateTimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $picture = $this->repository->findOneBy(['id' => $id]);
        if ($picture) {
            $this->manager->remove($picture);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
