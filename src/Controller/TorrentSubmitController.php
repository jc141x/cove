<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Form\TorrentSubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Torrent;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Lib\Torrent as TorrentRW;
use OpenApi\Annotations as OA;

class TorrentSubmitController extends AbstractController
{
    /**
     * @Route("/torrent/new", name="torrent_submit")
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TorrentSubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $tmp_torrent = $form->get('torrent-file')->getData();
            $torrentFile = new TorrentRW($tmp_torrent->getPathname());
            $torrent = new Torrent();
            $torrent->setName($form->get('name')->getData());
            $torrent->setDescription($form->get('description')->getData());
            $torrent->setCategory($form->get('category')->getData());
            $torrent->setUser($this->getUser());
            $torrent->setDate(new \DateTime());
            $torrent->setSize($torrentFile->size(2));
            $torrent->setSeeders(0);
            $torrent->setLeechers(0);
            $torrent->setHash($torrentFile->hash_info());
            $torrent->setMagnet($torrentFile->magnet());
            $announce = $torrentFile->announce();
            $torrent->setTrackers(is_array($announce) ? array_merge(...$announce) : [$announce]);
            $torrent->setFiles(array_keys($torrentFile->content()));

            $entityManager->persist($torrent);
            $entityManager->flush();
            return $this->json([
                'success' => 'Torrent added',
                'torrent' => $torrent->getId(),
            ], 200);
        }

        return $this->renderForm('torrent/new.html.twig', [
            'form' => $form,
        ]);
    }
    /**
     * @Route("/api/v1/torrent", name="torrent_submit_api", methods={"POST"})
     * @OA\Post(
     *    path="/torrent",
     *   tags={"Torrent"},
     *  summary="Not implemented yet",
     *  security={{"basicAuth":{}}},
     *  @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *        mediaType="multipart/form-data",
     *        @OA\Schema(
     *          type="object",
     *          @OA\Property(
     *            property="title",
     *            description="Torrent name",
     *            type="string",
     *          ),
     *        ),
     *     ),
     *   ),
     * @OA\Response(
     *   response=501,
     *  description="Torrent added",
     *   @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *       property="error",
     *       type="string",
     *       example="Not implemented",
     *     ),
     *   @OA\Property(
     *      property="title",
     *      type="string",
     *    ),
     *   ),
     * ),
     * @OA\Response(
     *  response=401,
     * description="Unauthorized",
     *),
     * )
     */
    public function api(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (! $this->getUser()) {
            return $this->json([
                'error' => 'Not logged in',
            ], 401);
        }
        $title = $request->request->get('title');
        return $this->json([
            'error' => 'Not implemented',
            'title' => $title,
        ], 501);
    }
}
