<?php

namespace App\Service\Asset;


class ViteAsset implements AssetInterface
{
    private array $manifestJson = [];

    public function __construct(private string $manifestJsonPath, private int $assetDevServerPort)
    {
    }

    /**
     * @throws AssetException
     */
    public function renderAsset(string $entry, array $dependencies = []): string
    {
        $html = '';
        foreach ($dependencies as $dependency) {
            $html .= AssetInterface::DEPENDENCIES[$dependency] ?? '';

            if ($dependency === 'react') {
                $base = "http://localhost:{$this->assetDevServerPort}/assets";

                $html .= <<<HTML
    <script type="module">
        import RefreshRuntime from "{$base}/@react-refresh"
        RefreshRuntime.injectIntoGlobalHook(window)
        window.\$RefreshReg\$ = () => {}
        window.\$RefreshSig\$ = () => (type) => type
        window.__vite_plugin_react_preamble_installed__ = true
    </script>
HTML;
            }
        }

        if (file_exists($this->manifestJsonPath)) {
            if (empty($this->manifestJson)) {
                /** @var string $content */
                $content = file_get_contents($this->manifestJsonPath);
                $this->manifestJson = json_decode($content, true);
            }

            [
                'script'      => $script,
                'cssFiles'    => $cssFiles,
                'importFiles' => $importFiles
            ] = $this->getData($entry);


            $html .= $this->renderProductionImports($importFiles);
            $html .= $this->renderProductionScript($script);
            $html .= $this->renderProductionStyles($cssFiles);

            return $html;
        }

        return $this->renderDevScript($entry) . $html;
    }

    /**
     * @return array{script: string, cssFiles: string[], importFiles: string[]} $asset
     * @throws AssetException
     */
    private function getData(string $asset): array
    {
        $key = 'js/' . $asset;

        if (!array_key_exists($key, $this->manifestJson)) {
            throw new AssetException("L'entrée << $key >> n'existe pas !");
        }

        $data = $this->manifestJson[$key];

        $imports = array_map(
            callback: fn(string $importKey) => $this->manifestJson[$importKey]['file'],
            array: $data['dynamicImports']
        );

        return [
            'script'      => $data['file'],
            'cssFiles'    => $data['css'] ?? [],
            'importFiles' => $imports
        ];
    }

    /**
     * @param string[] $files
     * @return string
     */
    private function renderProductionImports(array $files): string
    {
        $html = '';
        foreach ($files as $file) {
            $html .= "<link rel=\"modulepreload\" href=\"/assets/{$file}\"/>";
        }
        return $html;
    }

    private function renderProductionScript(string $file): string
    {
        return "<script src=\"/assets/{$file}\" type=\"module\" defer></script>";
    }

    /**
     * @param string[] $files
     * @return string
     */
    private function renderProductionStyles(array $files): string
    {
        $html = '';
        foreach ($files as $file) {
            $html .= "<link rel=\"stylesheet\" href=\"/assets/{$file}\" media=\"screen\"/>";
        }
        return $html;
    }

    private function renderDevScript(string $file): string
    {
        $base = "http://localhost:{$this->assetDevServerPort}/assets";

        return <<<HTML
<script type="module" src="{$base}/@vite/client"></script>
<script type="module" src="{$base}/js/{$file}" defer></script>
HTML;
    }
}
