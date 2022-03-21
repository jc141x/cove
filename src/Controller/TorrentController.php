<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TorrentRepository;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/v1/torrent")
 */
class TorrentController extends AbstractController
{
     /**
     * @Route("/{id}", name="view_torrent", requirements={"id"="\d+"})
     * @OA\Get(
     *    tags={"Torrent"},
     *    path="/torrent/{id}",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        @OA\Schema(ref="#/components/schemas/Torrent/properties/id")),
     *    @OA\Response(
     *      response="200",
     *      description="Returns a torrent",
     *      @OA\JsonContent(
     *         ref="#/components/schemas/Torrent"
     *      ),
     * ))
     */
    public function view(int $id, TorrentRepository $torrentRepository): Response
    {
        $torrent = $torrentRepository->find($id);
        if (!$torrent) {
            return $this->json([
                'error' => 'Torrent not found',
            ], 404);
        }

        $response = $this->json(
            $torrent, 200, [],
            ['attributes' => [
                'id',
                'name',
                'size',
                'date' => [
                    'format' => 'Y-m-d',
                ],
                'seeders',
                'leechers',
                'category' => [
                    'id',
                    'name',
                ],
                'user' => [
                    'id',
                    'username',
                ],
                'comments' => [
                    'id',
                    'text',
                    'date' => [
                        'format' => 'Y-m-d',
                    ],
                    'user' => [
                        'id',
                        'username',
                    ],
                ],
                'description',
                'hash',
                'magnet',
                'trackers',
            ]]
        );
        $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );
        return $response;
    }

    /**
     * @Route("/list", name="list_torrent")
     * @OA\Get(
     *    tags={"Torrent"},
     *    path="/torrent/list",
     *    @OA\Response(
     *      response="200",
     *      description="Returns a list of torrents",
     *      @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/Torrent")
     *      ),
     * ))
     */
    public function listJson(TorrentRepository $torrentRepository): Response
    {
        $torrents = $torrentRepository->findAll();
        $response = $this->json(
            $torrents, 200, [],
            ['attributes' => [
                'id',
                'name',
                'size',
                'date' => [
                    'format' => 'Y-m-d',
                ],
                'seeders',
                'leechers',
                'category' => [
                    'id',
                    'name',
                ],
                'user' => [
                    'id',
                    'username',
                ],
                'magnet',
            ]]
        );
        $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );
        return $response;
    }

    /**
     * @Route("/{id}/comment", name="comment", methods={"POST"})
     * @OA\Post(
     *   tags={"Torrent"},
     *   path="/torrent/{id}/comment",
     *   security={{"basicAuth":{}}},
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        @OA\Schema(ref="#/components/schemas/Torrent/properties/id")),
     *   @OA\RequestBody(
     *    @OA\MediaType(
     *      mediaType="application/x-www-form-urlencoded",
     *       @OA\Schema(
     *          type="object",
     *          required={"text"},
     *          @OA\Property(
     *             property="text",
     *             ref="#/components/schemas/Comment/properties/text"
     *          ),
     *       ),
     *    )
     *   ),
     *   @OA\Response(
     *     response="201",
     *     description="Returns a comment",
     *     @OA\JsonContent(
     *        ref="#/components/schemas/Comment"
     *     ),
     *   ),
     *   @OA\Response(
     *     response="400",
     *     description="Bad request"
     *   ),
     *   @OA\Response(
     *     response="404",
     *     description="Torrent not found"
     *   ),
     * )
     */
    public function comment(int $id, Request $request, TorrentRepository $torrentRepository, EntityManagerInterface $entityManager): Response
    {
        $torrent = $torrentRepository->find($id);
        if (!$torrent) {
            return $this->json([
                'error' => 'Torrent not found',
            ], 404);
        }
        $text = $request->request->get('text');
        $comment = new Comment();
        $comment->setText($text);
        $comment->setTorrent($torrent);
        $comment->setUser($this->getUser());
        $comment->setDate(new \DateTime());
        $entityManager->persist($comment);
        $entityManager->flush();
        return $this->json('');
    }
}
