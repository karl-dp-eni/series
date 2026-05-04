<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/season', name: 'season_')]
final class SeasonController extends AbstractController
{
    #[Route('/create', name: 'create')]
    #[Route('/{id}/create', name: 'create_with_serie')]
    public function create(
        EntityManagerInterface $entityManager,
        Request                $request,
        SerieRepository        $serieRepository,
        int                    $id = null,
    ): Response
    {
        $season = new Season();

        if ($id) {
            $serie = $serieRepository->find($id);
            $season->setNumber(count($serie->getSeasons()) + 1);
            $season->setSerie($serie);
        }
        $seasonForm = $this->createForm(SeasonType::class, $season);

        $seasonForm->handleRequest($request);

        if ($seasonForm->isSubmitted() && $seasonForm->isValid()) {
            $entityManager->persist($season);
            $entityManager->flush();
            $this->addFlash('success', 'Season successfully added!');

            return $this->redirectToRoute('serie_detail', ['id' => $season->getSerie()->getId()]);
        }

        return $this->render('season/create.html.twig', [
            'seasonForm' => $seasonForm
        ]);
    }
}
