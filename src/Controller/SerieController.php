<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/serie', name: 'serie_')]
final class SerieController extends AbstractController
{
    #[Route('', name: 'list')]
    public function list(SerieRepository $serieRepository): Response
    {
//        $series = $serieRepository->findAll();
//        $series = $serieRepository->findBy(['status' => 'ended'], ['name' => 'ASC']);
//        $series = $serieRepository->findBy([], ['popularity' => 'DESC']);
        $series = $serieRepository->findBestSeries();

        return $this->render('serie/list.html.twig', [
            "series" => $series
        ]);
    }

    #[Route('/{id}', name: 'detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(int $id, SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->find($id);

        if (!$serie) {
            throw $this->createNotFoundException('Ooops ! Serie not found !');
        }
        return $this->render('serie/detail.html.twig', [
            'serie' => $serie
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(EntityManagerInterface $entityManager): Response
    {

        $serie = new Serie();

        $entityManager->persist($serie);
        $entityManager->flush();

        $serie->setName("Buffy contre les vampires");
        $entityManager->persist($serie);
        $entityManager->flush();

        $entityManager->remove($serie);
        $entityManager->flush();

        // TODO : créer une nouvelle série avec un formulaire
        return $this->render('serie/create.html.twig');
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET'])]
    public function delete(int                    $id,
                           SerieRepository        $serieRepository,
                           EntityManagerInterface $entityManager): Response
    {
        $serie = $serieRepository->find($id);

        if ($serie) {
            $entityManager->remove($serie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('serie_list');
    }
}
