<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OmdbApi {

    private $httpClient;
    private $parameterBag;

    /**
     * Récupère par injection de dépenance les services 
     * nécessaires aux traitements
     *
     * @param HttpClientInterface $httpClient
     */
    public function __construct(
        HttpClientInterface $httpClient,
        ParameterBagInterface $parameterBag
    )
    {
        $this->httpClient = $httpClient;
        $this->parameterBag = $parameterBag;
    }

    /**
     * Elle permet d'aller interroger omdbapi afin de récupérer 
     * les infos d'un film par son titre
     *
     * @param string $title
     * @return []
     */
    public function fetch(string $title)
    {
        $response = $this->httpClient->request(
            'GET',      // Methode
            'https://www.omdbapi.com', // url
            [
                'query' => [
                    't' => $title,
                    'apiKey' => $this->parameterBag->get('app.omdb_api_key')
                ]
            ]
        );

        $movie = $response->toArray();

        if (!$movie || !isset($movie['Poster'])) {
            return null;
        } else {
            return $movie;
        }

    }
}