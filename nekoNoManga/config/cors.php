<?php
// config/cors.php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    */

    // Les routes auxquelles appliquer le CORS
    'paths' => [
        'api/*',
        'login',
        'logout',
        'sanctum/csrf-cookie',
    ],

    // Méthodes HTTP autorisées
    'allowed_methods' => ['*'],

    // Origines autorisées (ajoute ici l’URL où tourne ton front)
    'allowed_origins' => ['http://localhost:5175'],

    // En-têtes autorisés
    'allowed_headers' => ['*'],

    // En-têtes exposés côté client (facultatif)
    'exposed_headers' => [],

    // Combien de secondes garder en cache la pré-vol
    'max_age' => 0,

    // Si tu gères ou non les cookies / credentials
    'supports_credentials' => false,

];
