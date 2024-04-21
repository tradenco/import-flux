<?php

namespace App\Manager;

use App\DTO\News;
use App\Entity\Article;
use App\Entity\Source;
use App\Repository\SourceRepository;
use Doctrine\ORM\EntityManagerInterface;

class ArticleManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SourceRepository $sourceRepository
    )
    {}

    public function ImportArticles(News $news): void
    {
        $source = new Source();
        $source->setName($news->title);

        foreach ($news->articles as $item) {
            $article = new Article();
            $article->setName($item->title)
                //->setSource($source)
                ->setContent($item->description)
                ->setUrl($item->url);

            $source->addArticle($article);
        }

        $this->sourceRepository->upsertSourceWithArticle($source);
    }

    /**
     * Import global data
     *
     * @param News $news
     * @return void
     */
    public function Import1Articles(News $news): void
    {

        $source = new Source();

        $source->setName($news->title)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($source);

        foreach ($news->articles as $item) {

            $article = new Article();
            $article->setName($item->title)
                //->setSource($source)
                ->setContent($item->description)
                ->setUrl($item->url)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable());

            $source->addArticle($article);

            $this->entityManager->persist($article);
        }

        $this->entityManager->flush();
    }
}