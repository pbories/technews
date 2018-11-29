<?php

namespace App\Article\Provider;


use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlProvider
{
    /**
     * Retourner les articles de articles.yaml
     * sous forme de tableau.
     * @return array
     */
    public function getArticles() : array
    {
        try {
            return Yaml::parseFile(__DIR__ . '/articles.yaml')['data'];
        } catch (ParseException $exception) {
            printf('Impossible de parser le fichier Yaml: %s', $exception->getMessage());
        }
    }
}
