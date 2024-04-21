<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    #[Route('/api/articles', name: 'api.article')]
    public function index(ArticleRepository $repository): Response
    {
        $articles = $repository->findAll();
        return $this->json($articles, Response::HTTP_OK, [], ['groups' => ['news.articles', 'article.detail', 'article.list']]);
    }
}
