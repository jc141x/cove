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
            $torrent->setFiles(array_keys($torrentFile->content()));

            $entityManager->persist($torrent);
            $entityManager->flush();
            return $this->redirect('/torrent/show/'.$torrent->getId());
        }

        return $this->renderForm('torrent/new.html.twig', [
            'form' => $form,
        ]);
    }
}
