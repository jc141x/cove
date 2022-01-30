<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TorrentRepository;

class TorrentController extends AbstractController
{
    /**
     * @Route("/torrent/view/{id}", name="view_torrent")
     */
    public function view(int $id, TorrentRepository $torrentRepository): Response
    {
        $torrent = $torrentRepository->find($id);
        if (!$torrent) {
            throw $this->createNotFoundException('Torrent not found');
        }
        return $this->render('torrent/view.html.twig', [
            'torrent' => $torrent,
        ]);
    }
    /**
     * @Route("/torrent/list", name="list_torrent")
     */
    public function list(TorrentRepository $torrentRepository): Response
    {
        $torrents = $torrentRepository->findAll();
        return $this->render('torrent/list.html.twig', [
            'torrents' => $torrents,
        ]);
    }
}
