<?php

namespace App\Controller;

use App\DTO\ApiResponse;
use App\Entity\Serie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DragonController extends AbstractController
{
    #[Route('/dragon/natif', name: 'app_dragon_natif')]
    public function natif(): Response
    {

        // Full PHP natif
        // curl_init() https://www.php.net/manual/fr/function.curl-init.php
        $data = file_get_contents('https://dragonball-api.com/api/characters');
        $characters = json_decode($data);

        return $this->render('dragon/index.html.twig', [
            'characters' => $characters
        ]);
    }

    #[Route('/dragon', name: 'app_dragon')]
    public function list(HttpClientInterface $httpClient, SerializerInterface $serializer): Response
    {

        // Full Symfony
        $response = $httpClient->request('GET', 'https://dragonball-api.com/api/characters');

//        $characters = $response->toArray(); // Permet de transformer la réponse (object) en tableau
        $characters = $serializer->deserialize($response->getContent(), ApiResponse::class, 'json');

        return $this->render('dragon/index.html.twig', [
            'characters' => $characters
        ]);
    }
}
