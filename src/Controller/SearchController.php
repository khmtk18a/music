<?php

namespace App\Controller;

use Schranz\Search\SEAL\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/', name: 'song_search')]
    public function index(): Response
    {
        return $this->render('search/index.html.twig');
    }

    #[Route('/add_song', name: 'create_song')]
    public function addSong(EngineInterface $engine): Response
    {
        $engine->saveDocument('song', [
            'id' => 3,
            'name' => 'Khúc dạ hành',
            'artist' => 'Ging',
            'url' => '10'
        ]);

        return new Response('OK');
    }
}
