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
     *         @OA\Property(property="id", ref="#/components/schemas/Torrent/properties/id"),
     *         @OA\Property(property="name", ref="#/components/schemas/Torrent/properties/name"),
     *         @OA\Property(property="size", ref="#/components/schemas/Torrent/properties/size"),
     *         @OA\Property(property="date", ref="#/components/schemas/Torrent/properties/date"),
     *         @OA\Property(property="seeders", ref="#/components/schemas/Torrent/properties/seeders"),
     *         @OA\Property(property="leechers", ref="#/components/schemas/Torrent/properties/leechers"),
     *         @OA\Property(property="category", ref="#/components/schemas/Category"),
     *         @OA\Property(property="user", ref="#/components/schemas/User"),
     *         @OA\Property(property="comments", type="array", @OA\Items(ref="#/components/schemas/Comment")),
     *         @OA\Property(property="description", ref="#/components/schemas/Torrent/properties/description"),
     *         @OA\Property(property="files", type="array", @OA\Items(ref="#/components/schemas/Torrent/properties/files")),
     *         @OA\Property(property="hash", ref="#/components/schemas/Torrent/properties/hash"),
     *         @OA\Property(property="magnet", ref="#/components/schemas/Torrent/properties/magnet"),
     *         @OA\Property(property="trackers", ref="#/components/schemas/Torrent/properties/trackers"),
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
     * @Route("/{id}/comment", name="comment")
     */
    public function comment(int $id, Request $request, TorrentRepository $torrentRepository, EntityManagerInterface $entityManager): Response
    {
        $torrent = $torrentRepository->find($id);
        if (!$torrent) {
            return $this->json([
                'error' => 'Torrent not found',
            ], 404);
        }
        $text = $request->query->get('content');
        dump($request->query->all());
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
