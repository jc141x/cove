<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class DefaultController extends AbstractController
{

    /**
     * @Route("/api", name="default")
    * @OA\Info(title="Cove API", version="1.0")
    * @OA\Server(url="/api/v1")
    * @OA\Server(url="https://rumpowered.org/api/v1")
    * @OA\Server(url="http://185.195.237.203:12908/api/v1")
    * @OA\Server(url="http://0.0.0.0:8000/api/v1/")
    * @OA\Server(url="http://localhost:8000/api/v1/")
    * @OA\SecurityScheme(
    *     type="http",
    *     securityScheme="basicAuth",
    *     scheme="basic",
    * )
     */
    
    
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }
    /**
     * @Route("/", name="home")
     */
    public function underConstruction(): Response
    {
        return $this->render('construction.html.twig');
    }
    /**
     * @Route("/swagger", name="swagger")
     */
    public function swagger(): Response
    {
        $openapi = \OpenApi\Generator::scan(\OpenApi\Util::finder(dirname(__DIR__, 1), 'Lib'));
        return new Response($openapi->toJson(), 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/steamfetch", name="steamfetch")
     */
    public function steamfetch(Request $request): Response
    {
        $appid = $request->query->get('appid');
        if ($appid) {
            $data = json_decode(file_get_contents('https://store.steampowered.com/api/appdetails?appids=' . $appid), TRUE)[$appid]['data'];
        }
        return $this->render('steamfetch.html.twig', [
            'appid' => $appid,
            'data' => $data??null,
        ]);
    }
}
