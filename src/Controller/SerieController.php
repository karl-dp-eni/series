<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/serie', name: 'serie_')]
final class SerieController extends AbstractController
{
    #[Route('/list/{page}', name: 'list', requirements: ['page' => '\d+'])]
    public function list(SerieRepository $serieRepository, int $page = 1): Response
    {
//        $series = $serieRepository->findAll();
//        $series = $serieRepository->findBy(['status' => 'ended'], ['name' => 'ASC']);
//        $series = $serieRepository->findBy([], ['popularity' => 'DESC']);
//        $series = $serieRepository->findBestSeries();

        $nbSeries = $serieRepository->count();
        $maxPage = ceil($nbSeries / 50);

        if ($page < 1) {
//            $page = 1;
            return $this->redirectToRoute('serie_list');
        } elseif ($page > $maxPage) {
//            $page = $maxPage;
            return $this->redirectToRoute('serie_list', ['page' => $maxPage]);
        }

        $series = $serieRepository->findBestSeriesWithPagination($page);

        return $this->render('serie/list.html.twig', [
            "series" => $series,
            'currentPage' => $page,
            'maxPage' => $maxPage
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
    public function create(
        Request                                    $request, // Bien utiliser le httpFoundation
        EntityManagerInterface                     $entityManager,
        #[Autowire('%serie_poster_dir%')] string   $posterDir,
        #[Autowire('%serie_backdrop_dir%')] string $backdropDir,
    ): Response
    {

        $serie = new Serie();
        $serieForm = $this->createForm(SerieType::class, $serie);

        $serieForm->handleRequest($request);

        if ($serieForm->isSubmitted() && $serieForm->isValid()) {

            // Récupération des images et traitement
            $filePoster = $serieForm->get('poster')->getData();
            $fileBackdrop = $serieForm->get('backdrop')->getData();

            /**
             * @var UploadedFile $filePoster
             * @var UploadedFile $fileBackdrop
             * Permet d'obtenir l'autocompletion des méthodes de UploadedFile
             */
            $newFileNamePoster = $serie->getName() . "-" . uniqid() . "." . $filePoster->guessExtension();
            $newFileNameBackdrop = $serie->getName() . "-backdrop-" . uniqid() . "." . $fileBackdrop->guessExtension();
            $filePoster->move($posterDir, $newFileNamePoster);
            $fileBackdrop->move($backdropDir, $newFileNameBackdrop);
            $serie->setPoster($newFileNamePoster);
            $serie->setBackdrop($newFileNameBackdrop);

            $serie->setDateCreated(new \DateTime());
            $entityManager->persist($serie);
            $entityManager->flush();
            $this->addFlash('success', $serie->getName() . ' created!');
            return $this->redirectToRoute('serie_detail', ['id' => $serie->getId()]);
        }

        return $this->render('serie/create.html.twig', [
            'serieForm' => $serieForm
        ]);
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
            $this->addFlash('success', $serie->getName() . ' deleted !');
        }

        return $this->redirectToRoute('serie_list');
    }

    #[Route('/{id}/update', name: 'update', methods: ['GET', 'POST'])]
    public function update(int                                        $id,
                           SerieRepository                            $serieRepository,
                           Request                                    $request,
                           EntityManagerInterface                     $entityManager,
                           #[Autowire('%serie_poster_dir%')] string   $posterDir,
                           #[Autowire('%serie_backdrop_dir%')] string $backdropDir,
    ): Response
    {
        $serie = $serieRepository->find($id);
        $serieForm = $this->createForm(SerieType::class, $serie);

        $serieForm->handleRequest($request);

        if ($serieForm->isSubmitted() && $serieForm->isValid()) {

            // Récupération de l'image et traitement
            $filePoster = $serieForm->get('poster')->getData();
            $fileBackdrop = $serieForm->get('backdrop')->getData();

            /**
             * @var UploadedFile $filePoster
             * @var UploadedFile $fileBackdrop
             * Permet d'obtenir l'autocompletion des méthodes de UploadedFile
             */
            $newFileNamePoster = $serie->getName() . "-" . uniqid() . "." . $filePoster->guessExtension();
            $newFileNameBackdrop = $serie->getName() . "-backdrop-" . uniqid() . "." . $fileBackdrop->guessExtension();
            $filePoster->move($posterDir, $newFileNamePoster);
            $fileBackdrop->move($backdropDir, $newFileNameBackdrop);
            $serie->setPoster($newFileNamePoster);
            $serie->setBackdrop($newFileNameBackdrop);

            $entityManager->persist($serie);
            $entityManager->flush();
            $this->addFlash('success', $serie->getName() . ' updated !');
            return $this->redirectToRoute('serie_detail', ['id' => $serie->getId()]);
        }

        return $this->render('serie/update.html.twig', [
            'serieForm' => $serieForm,
        ]);
    }
}
