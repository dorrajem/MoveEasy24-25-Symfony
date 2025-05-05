<?php
// src/Service/AIService.php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AIService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function analyzeReclamation(string $text): array
    {
        // Utilisation de HuggingFace API gratuitement (par exemple avec un modèle de sentiment)
        $response = $this->client->request('POST', 'https://api-inference.huggingface.co/models/distilbert-base-uncased-finetuned-sst-2-english', [
            'headers' => [
                'Authorization' => 'Bearer YOUR_HUGGINGFACE_API_KEY', // Remplacer par ta clé API gratuite
            ],
            'json' => [
                'inputs' => $text,
            ],
        ]);

        $result = $response->toArray();

        // Traitement basique
        $sentiment = $result[0][0]['label'] ?? 'neutral';
        
        // Détection catégorie basique (optionnel simple)
        $predictedCategory = 'Autre';
        if (str_contains(strtolower($text), 'retard')) {
            $predictedCategory = 'Retard';
        } elseif (str_contains(strtolower($text), 'accessibilité')) {
            $predictedCategory = 'Accessibilité';
        } elseif (str_contains(strtolower($text), 'propreté')) {
            $predictedCategory = 'Propreté';
        } elseif (str_contains(strtolower($text), 'confort')) {
            $predictedCategory = 'Confort';
        } elseif (str_contains(strtolower($text), 'sécurité')) {
            $predictedCategory = 'Sécurité';
        }

        return [
            'sentiment' => $sentiment,
            'predictedCategory' => $predictedCategory,
        ];
    }
}
