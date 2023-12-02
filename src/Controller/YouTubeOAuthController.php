<?php
// src/Controller/YouTubeOAuthController.php

namespace App\Controller;

use Google_Client;
use App\Service\YouTubeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class YouTubeOAuthController extends AbstractController
{
    private $googleClient;

    public function __construct(string $clientId, string $clientSecret, string $redirectUri)
    {
        $client = new Google_Client();
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri($redirectUri);
        $client->setScopes(['https://www.googleapis.com/auth/youtube.readonly']);
        $client->setAccessType('offline');

        $this->googleClient = $client;
    }

    #[Route('/youtube/auth', name: 'youtube_auth')]
    public function auth(Request $request, RequestStack $requestStack)
    {
        if ($request->query->get('code')) {
            $this->googleClient->authenticate($request->query->get('code'));
            $accessToken = $this->googleClient->getAccessToken();
            // Stocker $accessToken dans une session ou une base de données
            $requestStack->getSession()->set('youtube_access_token', $accessToken);
            // Rediriger l'utilisateur vers la page souhaitée après l'authentification
            return new RedirectResponse('/youtube/playlist');
        } else {
            // Rediriger vers Google pour authentification
            $authUrl = $this->googleClient->createAuthUrl();
            return new RedirectResponse($authUrl);
        }
    }


    #[Route('/youtube/playlist', name: 'youtube_playlist')]
    public function showPlaylist(YouTubeService $youtubeService, RequestStack $requestStack): Response
    {
        // Récupérer le jeton d'accès de la session ou de la base de données
        $accessToken = $requestStack->getSession()->get('youtube_access_token');

        // Utiliser le jeton d'accès pour configurer le client Google API
        if ($accessToken) {
            $youtubeService->setAccessToken($accessToken);
        } else {
            // Gérer le cas où l'accès token n'est pas disponible
            // Rediriger vers la route d'authentification par exemple
            return $this->redirectToRoute('youtube_auth');
        }

        // Récupérer les vidéos de la playlist
        $videos = $youtubeService->getPlaylistVideos('PLV5Z9YWBGhPxh4FPyXsU-VLY7PsCOpcpn');

        // Récupérer les info d'une chaine
        // $videos = $youtubeService->getChannelInfo('UCUYpVkJhLZ_SXSYah6bi9nA');
        dd($videos);
        // Rendre une vue avec les vidéos
        return $this->render('home/index.html.twig', [
            'videos' => $videos,
        ]);
    }
}
