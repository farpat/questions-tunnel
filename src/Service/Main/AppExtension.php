<?php

namespace App\Service\Main;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_active_class', [$this, 'getActiveClass']),
        ];
    }

    public function getActiveClass(Request $request, string $route): string
    {
        return $request->getPathInfo() === $this->urlGenerator->generate($route) ? ' active' : '';
    }
}
