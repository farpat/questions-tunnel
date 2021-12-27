<?php

namespace App\Service\Asset;


interface AssetInterface
{
    public const DEPENDENCIES = [
        'jquery' => '<script src="https://code.jquery.com/jquery-3.6.0.min.js"
                integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK"
                crossorigin="anonymous" defer></script>'
    ];

    /**
     * AssetInterface constructor.
     * @param string $manifestJsonPath Chemin vers le fichier " manifest.json "
     * @param int $assetDevServerPort Port du serveur de rendu d'asset (utile uniquement en mode développement)
     */
    public function __construct(string $manifestJsonPath, int $assetDevServerPort);

    /**
     * Rend une balise HTML à partir d'une entrée (<link> ou <script>)
     * @param string $entry
     * @param string[] $dependencies Dépendances à charger avant l'entrée
     * @return string
     */
    public function renderAsset(string $entry, array $dependencies = []): string;
}
