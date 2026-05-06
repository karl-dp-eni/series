<?php

namespace App\Controller\Api;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/serie', name: 'api_serie_')]
final class SerieController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(SerieRepository $serieRepository): Response
    {
        $series = $serieRepository->findAll();

        return $this->json($series, Response::HTTP_OK, [], ['groups' => 'serie-read']);
    }

    #[Route('/{id}/like', name: 'update_like', methods: ['PATCH'])] // PATCH = modifie une partie de la ressource
    public function update(
        SerieRepository        $serieRepository,
        EntityManagerInterface $entityManager,
        Request                $request,
        int                    $id): Response
    {
        $serie = $serieRepository->find($id);
        $json = $request->getContent();

        $data = json_decode($json, true);

        $serie->setNbLike($serie->getNbLike() + $data['like']);

        $entityManager->persist($serie);
        $entityManager->flush();

        return $this->json($serie, Response::HTTP_OK, [], ['groups' => 'serie-like']);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(SerieRepository $serieRepository, int $id): Response
    {
        $serie = $serieRepository->find($id);
        if (!$serie) {
            return $this->json(['error' => 'Serie not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($serie, Response::HTTP_OK, [], ['groups' => 'serie-read']);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request                $request,
        SerializerInterface    $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface     $validator
    ): Response
    {
        $data = $request->getContent(); // renvoi le body de la requête
        $serie = $serializer->deserialize($data, Serie::class, 'json');

        $errors = $validator->validate($serie);

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }
        $entityManager->persist($serie);
        $entityManager->flush();

        return $this->json($serie, Response::HTTP_CREATED, [], ['groups' => 'serie-read']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        SerieRepository        $serieRepository,
        EntityManagerInterface $entityManager,
        int                    $id
    ): Response
    {
        $serie = $serieRepository->find($id);

        $entityManager->remove($serie);
        $entityManager->flush();

        return $this->json(['message' => 'Serie deleted'], Response::HTTP_NO_CONTENT);
    }
}
