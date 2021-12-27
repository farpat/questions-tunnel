<?php

namespace App\Serializer\Normalizer;

use App\Entity\Question;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class QuestionNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param Question $object
     * @param ?string $format
     * @param array $context
     * @return array
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        return [
            'label'       => $object->getLabel(),
            'suggestions' => $object->getSuggestions()
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof \App\Entity\Question && $format === 'json';
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
