<?php

namespace App\Controller;

use App\Entity\Source;
use App\Repository\SourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SourceController extends AbstractController
{
    #[Route('/api/source', name: 'api.sources', methods: ['GET'])]
    public function index(SourceRepository $repository): Response
    {
        $source = $repository->findAll();
        return $this->json($source, Response::HTTP_OK, [], ['groups' => 'news.index']);
    }

    #[Route('/api/source/{id}/articles', name: 'api.source.articles', requirements: ['id' => '\d+'])]
    public function show(Source $source): Response
    {
        return $this->json($source, Response::HTTP_OK, [], ['groups' => ['news.index','news.articles']]);
    }
}
