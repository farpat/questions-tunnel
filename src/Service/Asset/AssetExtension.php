<?php

namespace App\Service\Asset;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{
    public function __construct(private AssetInterface $asset)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_asset', [$this, 'renderAsset'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string[] $dependencies
     */
    public function renderAsset(string $entry, array $dependencies = []): string
    {
        return $this->asset->renderAsset($entry, $dependencies);
    }
}
