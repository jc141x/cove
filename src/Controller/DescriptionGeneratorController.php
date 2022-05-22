<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DescriptionGeneratorController extends AbstractController
{

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/descgen', name: 'app_description_generator')]
    public function index(): Response
    {
        if (!isset($_GET['appid'])) {
            return new Response("no appid");
        }
        $appid = $_GET['appid'];
        $IMGBBKEY=$_ENV["IMGBBKEY"];
        if(!$IMGBBKEY) return new Response("imgbb api key not loaded!");
        $url = "https://store.steampowered.com/api/appdetails?appids=$appid";
        $response = $this->client->request(
            'GET',
            $url,
            [
                'headers' => [
                    'Accept-Language' => 'en-US,en'
                ]
            ]
        );
        $response_arr = $response->toArray()[$appid];
        if (!isset($response_arr['data'])) return new Response("not found");
        $data = $response_arr['data'];
        $hero_res = $this->client->request(
            'POST',
            "https://api.imgbb.com/1/upload?key=$IMGBBKEY&image=https://cdn.cloudflare.steamstatic.com/steam/apps/$appid/library_hero.jpg"
        );
        try {
            $hero = $hero_res->toArray()['data']['url'];
        } catch (\Throwable $th) {
            $hero = "<FAILED TO GET AUTOMATICALLY>";
        }
        $screen1_res = $this->client->request(
            'POST',
            "https://api.imgbb.com/1/upload?key=$IMGBBKEY&image={$data['screenshots'][0]['path_full']}"
        );
        try {
            $screen1 = $screen1_res->toArray()['data']['url'];
        } catch (\Throwable $th) {
            $screen1 = "<FAILED TO GET AUTOMATICALLY>";
        }
        $screen2_res = $this->client->request(
            'POST',
            "https://api.imgbb.com/1/upload?key=$IMGBBKEY&image={$data['screenshots'][1]['path_full']}"
        );
        try {
            $screen2 = $screen2_res->toArray()['data']['url'];
        } catch (\Throwable $th) {
            $screen2 = "<FAILED TO GET AUTOMATICALLY>";
        }
        $screen3_res = $this->client->request(
            'POST',
            "https://api.imgbb.com/1/upload?key=$IMGBBKEY&image={$data['screenshots'][2]['path_full']}"
        );
        try {
            $screen3 = $screen3_res->toArray()['data']['url'];
        } catch (\Throwable $th) {
            $screen3 = "<FAILED TO GET AUTOMATICALLY>";
        }

        $reqs_raw = $data["pc_requirements"]["minimum"];
        $reqs = strip_tags(preg_filter("/<br>/", "\n", $reqs_raw));
        $reqs_arr = explode("\n",$reqs);
        $reqs_arr = preg_grep("/(Processor|Memory|Graphics|Storage):.*/", $reqs_arr);
        $reqs = implode("\n", $reqs_arr);
        $output = <<<EOD
            [center][img]{$hero}[/img][/center]
            [center][size=34][b]{$data["name"]}[/b][/size]
            [size=24]<Game version> [<Localization>] [<Emu/Modification. eg. Goldberg>] [GNU/Linux <Wine/Yuzu/Native>] [johncena141][/size]
            [size=22][url=https://johncena141.eu.org:8141/johncena141/portal]SETUP AND SUPPORT INFORMATION[/url][/size][/center]
            
            [b]System requirements[/b]
            {$reqs}
            
            [b]Description[/b]
            {$data["short_description"]}
            
            [center][img]{$screen1}[/img]
            [img]{$screen2}[/img]
            [img]{$screen3}[/img]
            Donations - Monero: 4ABGQLAeAgiauvay11VRrWXRRtraRCU6oaC6uG9RUnNCHN4eepzWjEB6sHF92sUrSED5b8GyY7Ayh57R1jUdcKZg7is2DW3
            
            [img]https://i.postimg.cc/447fH7YN/45345.png[/img][/center]
            EOD;
        
        return new Response($output, 200, ['content-type' => 'text/plain']);
    }
}
