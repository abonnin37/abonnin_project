<?php

namespace App\EventListener;

use ApiPlatform\EventListener\DeserializeListener as DecoratedListener;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Util\RequestAttributesExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DeserializeListener
{
    private static array $formats;

    public function __construct(
        private readonly DecoratedListener $decorated,
        private readonly SerializerContextBuilderInterface $serializerContextBuilder,
        private readonly DenormalizerInterface $denormalizer,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->isMethodCacheable() || $request->isMethod(Request::METHOD_DELETE)) {
            return;
        }

        if ($this->getContentType($request) === 'multipart') {
            $this->denormalizeFromRequest($request);
        } else {
            $this->decorated->onKernelRequest($event);
        }
    }

    private function denormalizeFromRequest(Request $request): void
    {
        $attributes = RequestAttributesExtractor::extractAttributes($request);
        if (empty($attributes)) {
            return;
        }

        $context = $this->serializerContextBuilder->createFromRequest($request, false, $attributes);
        $populated = $request->attributes->get('data');
        if ($populated !== null) {
            $context['object_to_populate'] = $populated;
        }

        $dataRequest = $request->request->all();
        // Get the boolean value set right
        if ($attributes['resource_class'] === 'App\Entity\Post') {
            $dataRequest['published'] = isset($dataRequest['published']) && $dataRequest['published'] === 'true';
        }
        $files = $request->files->all();

        // We test if we receive a file with a PUT method, if not, we put the old file in the $files variable
        if ($attributes['resource_class'] === 'App\Entity\Post' && $context['operation_type'] === 'item' && $context['item_operation_name'] === 'put' && empty($files)) {
            $files = ['imageFile' => null];
        }

        $object = $this->denormalizer->denormalize(
            array_merge($dataRequest, $files),
            $attributes['resource_class'],
            null,
            $context
        );

        $request->attributes->set('data', $object);
    }

    private function getContentType(Request $request): ?string
    {
        $mimeType = $request->headers->get('CONTENT_TYPE');

        $canonicalMimeType = null;
        if (false !== $pos = strpos($mimeType, ';')) {
            $canonicalMimeType = trim(substr($mimeType, 0, $pos));
        }

        if (null === static::$formats) {
            static::initializeFormats();
        }

        foreach (static::$formats as $format => $mimeTypes) {
            if (\in_array($mimeType, (array) $mimeTypes)) {
                return $format;
            }
            if (null !== $canonicalMimeType && \in_array($canonicalMimeType, (array) $mimeTypes)) {
                return $format;
            }
        }

        return null;
    }

    /**
     * Initializes HTTP request formats.
     */
    private static function initializeFormats(): void
    {
        static::$formats = [
            'html' => ['text/html', 'application/xhtml+xml'],
            'txt' => ['text/plain'],
            'js' => ['application/javascript', 'application/x-javascript', 'text/javascript'],
            'css' => ['text/css'],
            'json' => ['application/json', 'application/x-json'],
            'jsonld' => ['application/ld+json'],
            'xml' => ['text/xml', 'application/xml', 'application/x-xml'],
            'rdf' => ['application/rdf+xml'],
            'atom' => ['application/atom+xml'],
            'rss' => ['application/rss+xml'],
            'form' => ['application/x-www-form-urlencoded'],
            'multipart' => ['multipart/form-data'],
        ];
    }
}