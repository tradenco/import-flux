<?php

namespace App\Service;

use App\DTO\News;
use App\Manager\ArticleManager;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ImportService
{
    /**
     * @var array<News>
     */
    private array $data;
    public function __construct(
        private readonly AggregatorService $aggregatorService,
        private readonly ArticleManager $articleManager,
    ){}


    /**
     * Init import file
     *
     * @param string $filename
     * @return $this
     */
    public function fromFile(string $filename): static
    {
        $news = $this->aggregatorService->fetchDataFromFile($filename);
        $news->title = 'file';

        $this->data[] = $news;
        return $this;
    }

    /**
     * init import rss
     *
     * @param string $uri
     * @return $this
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function fromRss(string $uri): static
    {
        $news = $this->aggregatorService->fetchDataFromRss($uri);
        $news->title = 'rss';

        $this->data[] = $news;

        return $this;
    }

    /**
     * init import api
     *
     * @param string $uri
     * @return $this
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function fromJson(string $uri): static
    {
        $news = $this->aggregatorService->fetchDataFromApi($uri);
        $news->title = 'api';

        $this->data[] = $news;
        return $this;
    }

    /**
     * Execute import data
     *
     * @return void
     */
    public function execute(): void
    {
        foreach ($this->data as $item) {
            if ($item instanceof News) {
                $this->articleManager->ImportArticles($item);
            }
        }
    }
}