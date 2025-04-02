<?php
// api/src/Serializer/MediaObjectNormalizer.php

namespace App\Serializer;

use App\Entity\Image;
use App\Entity\Post;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Vich\UploaderBundle\Storage\StorageInterface;

final class MediaObjectNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'MEDIA_OBJECT_NORMALIZER_ALREADY_CALLED';
    private const URL_CACHE = 'MEDIA_OBJECT_URL_CACHE';

    public function __construct(
        private readonly StorageInterface $storage
    ) {
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return $this->normalizer->normalize($object, $format, $context);
        }

        $context[self::ALREADY_CALLED] = true;

        if ($object instanceof Image) {
            $cacheKey = 'image_' . $object->getId();
            if (!isset($context[self::URL_CACHE][$cacheKey])) {
                $context[self::URL_CACHE][$cacheKey] = $this->storage->resolveUri($object, 'imageFile');
            }
            $object->setContentUrl($context[self::URL_CACHE][$cacheKey]);
        }
        if ($object instanceof Post) {
            $cacheKey = 'post_' . $object->getId();
            if (!isset($context[self::URL_CACHE][$cacheKey])) {
                $context[self::URL_CACHE][$cacheKey] = $this->storage->resolveUri($object, 'imageFile');
            }
            $object->setImageUrl($context[self::URL_CACHE][$cacheKey]);
        }

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof Image || $data instanceof Post;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Image::class => true,
            Post::class => true,
        ];
    }
}