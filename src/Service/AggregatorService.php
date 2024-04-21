<?php

namespace App\Service;

use App\DTO\Article;
use App\DTO\News;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AggregatorService
{

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly SerializerInterface $serializer,
        private readonly ParameterBagInterface $parameterBag
    ){}

    /**
     * fetch api
     * 
     * @param string $url
     * @return News
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function fetchDataFromApi(string $url): News
    {
        $response = $this->client->request('GET', $url);
        return $this->serializer->deserialize($response->getContent(), News::class, 'json');
    }

    /**
     * fetch rss
     *
     * @param string $url
     * @return News
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function fetchDataFromRss(string $url): News
    {
        $news = new News();
        $response = $this->client->request('GET', $url);
        $crawler = new Crawler($response->getContent());
        $news->articles = $crawler->filter('item')->each(function ($node) {
            $article = new Article();

            $article->title = $node->filter('title')->text();
            $article->description = $node->filter('description')->text();
            $article->url =$node->filter('link')->text();

            return $article;
        });
        return $news;
    }


    /**
     * fetch file content
     *
     * @param string $filename
     * @return News
     */
    public function fetchDataFromFile(string $filename): News
    {
        $filePath = sprintf('%s/%s', $this->parameterBag->get('storage_directory'), $filename);
        $data = file_get_contents($filePath);
        return $this->serializer->deserialize($data, News::class, 'json');
    }
}