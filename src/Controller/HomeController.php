<?php

namespace App\Controller;

use App\Service\YouTubeService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $youtubeService;

    public function __construct(YouTubeService $youtubeService)
    {
        $this->youtubeService = $youtubeService;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            // 'videos' => $this->youtubeService->getChannelVideos("UCWsb-BMpUVlHJwWkPxzoXmA"),           
            // 'videos' => $this->youtubeService->getChannelVideos("UCUYpVkJhLZ_SXSYah6bi9nA"),
            'videos' => $this->youtubeService->getChannelVideos("UCqLpFtaflis5hF0RE6qU7tw"),
        ]);
    }

    #[Route('/youtube', name: 'youtube_channel')]
    public function search(): Response
    {
        // $results = $this->youtubeService->getChannelVideos("UCWsb-BMpUVlHJwWkPxzoXmA");       
        // $results = $this->youtubeService->getChannelVideos("UCUYpVkJhLZ_SXSYah6bi9nA");
        $results = $this->youtubeService->getChannelVideos("UCqLpFtaflis5hF0RE6qU7tw");

        // Ici, vous pouvez formater la réponse comme vous le souhaitez
        dd($results);
        return $this->json($results);
    }
}
