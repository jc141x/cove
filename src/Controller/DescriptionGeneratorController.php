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

    #[Route('/descgen', name:'app_description_generator')]
function index(): Response
    {
    if (!isset($_GET['appid'])) {
        return new Response("no appid");
    }
    $appid = $_GET['appid'];
    $IMGBBKEY = $_ENV["IMGBBKEY"];
    if (!$IMGBBKEY) {
        return new Response("imgbb api key not loaded!");
    }

    $url = "https://store.steampowered.com/api/appdetails?appids=$appid";
    $response = $this->client->request(
        'GET',
        $url,
        [
            'headers' => [
                'Accept-Language' => 'en-US,en',
            ],
        ]
    );
    try {
        $response_arr = $response->toArray()[$appid];
    } catch (\Throwable$th) {
        return new Response($th->getMessage());
    }
    if (!isset($response_arr['data'])) {
        return new Response("not found");
    }

    $data = $response_arr['data'];
    $hero_res = $this->client->request(
        'POST',
        "https://api.imgbb.com/1/upload?key=$IMGBBKEY&image=https://cdn.cloudflare.steamstatic.com/steam/apps/$appid/library_hero.jpg"
    );
    try {
        $hero = $hero_res->toArray()['data']['url'];
    } catch (\Throwable$th) {
        $hero = "<FAILED TO GET AUTOMATICALLY>";
    }
    $screen1_res = $this->client->request(
        'POST',
        "https://api.imgbb.com/1/upload?key=$IMGBBKEY&image={$data['screenshots'][0]['path_full']}"
    );
    try {
        $screen1 = $screen1_res->toArray()['data']['url'];
    } catch (\Throwable$th) {
        $screen1 = "<FAILED TO GET AUTOMATICALLY>";
    }
    $screen2_res = $this->client->request(
        'POST',
        "https://api.imgbb.com/1/upload?key=$IMGBBKEY&image={$data['screenshots'][1]['path_full']}"
    );
    try {
        $screen2 = $screen2_res->toArray()['data']['url'];
    } catch (\Throwable$th) {
        $screen2 = "<FAILED TO GET AUTOMATICALLY>";
    }
    $screen3_res = $this->client->request(
        'POST',
        "https://api.imgbb.com/1/upload?key=$IMGBBKEY&image={$data['screenshots'][2]['path_full']}"
    );
    try {
        $screen3 = $screen3_res->toArray()['data']['url'];
    } catch (\Throwable$th) {
        $screen3 = "<FAILED TO GET AUTOMATICALLY>";
    }
    $desc = htmlspecialchars_decode($data["short_description"]);
    // requirements
    $reqs_raw = $data["pc_requirements"]["minimum"];
    $reqs = strip_tags(preg_filter("/<br>/", "\n", $reqs_raw));
    $reqs_arr = explode("\n", $reqs);
    $reqs_arr = preg_grep("/(Processor|Memory|Graphics):.*/", $reqs_arr);
    $reqs = implode("\n", $reqs_arr);

    // localizations
    $lang_raw = $data["supported_languages"];
    $langs = explode("<br>", $lang_raw);
    $maybe_audio = isset($langs[1]) ? "\n" . strip_tags($langs[1]) : "";
    $langs = explode(",", $langs[0]);
    $lang_count = count($langs);
    $lang = strip_tags(implode(",", $langs));

    if ($lang_count > 2) {
        $locale = "MULTi" . $lang_count;
    } elseif ($lang_count == 2) {
        // we turn "English,German" into "ENG/GER"
        $locale = strtoupper(constant("Iso639\Part2\Alpha3TCode::" . trim(strtoupper(str_replace('*', '', strip_tags($langs[0])))))) . "/" . strtoupper(constant("Iso639\Part2\Alpha3TCode::" . trim(strtoupper(str_replace('*', '', strip_tags($langs[1]))))));
    } else {
        $locale = strtoupper(constant("Iso639\Part2\Alpha3TCode::" . strtoupper(str_replace('*', '', strip_tags($langs[0])))));
    }

    $platform = $data["platforms"]["linux"] ? "Native" : "Wine";

    $output = <<<EOD
            [img]{$hero}[/img]
            [size=22]{$data["name"]} - <Version> [{$locale}] [<Emulator/Modif>] [GNU/Linux {$platform}] [johncena141][/size]

            <Changes vs last if any>

            {$desc}

            [size=14][url=https://johncena141.eu.org:8141/johncena141/portal]SETUP AND SUPPORT[/url][/size]
            Game requirements
            {$reqs}

            Other information
            Can be played without extraction, generally no performance impact besides longer loading times.
            Languages: {$lang}{$maybe_audio}

            [img]{$screen1}[/img]
            [img]{$screen2}[/img]
            [img]{$screen3}[/img]
            Donations - Monero: 4ABGQLAeAgiauvay11VRrWXRRtraRCU6oaC6uG9RUnNCHN4eepzWjEB6sHF92sUrSED5b8GyY7Ayh57R1jUdcKZg7is2DW3
            [img]https://i.postimg.cc/tC3VR1vD/jc141v4.png[/img]
            EOD;

    return new Response($output, 200, ['content-type' => 'text/plain']);
}
}
