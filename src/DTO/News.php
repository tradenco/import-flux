<?php

namespace App\DTO;

use Symfony\Component\Serializer\Attribute\SerializedName;

class News
{
    public string $title;

    /**
     * @SerializedName("articles")
     * @var Article[]
     */
    public array $articles;
}