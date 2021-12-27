<?php

namespace App\Service\Asset;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AssetFactory
{
    public function __construct(private ParameterBagInterface $parameterBag)
    {
    }

    public function make(): AssetInterface
    {
        /** @var string $projectDir */
        $projectDir = $this->parameterBag->get('kernel.project_dir');

        return new ViteAsset(
             $projectDir . '/public/assets/manifest.json',
            (int)$this->parameterBag->get('docker.asset_dev_server_port')
        );
    }
}