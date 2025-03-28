<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(
        private readonly OpenApiFactoryInterface $decorated
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        // On parcourt toutes les routes de l'api
        foreach ($openApi->getPaths()->getPaths() as $key => $path) {
            // On récupère les informations sur la méthode GET et on test si l'information du Summary est hidden
            if ($path->getGet() && $path->getGet()->getSummary() === 'hidden') {
                // si c'est vrai on retire la route de la liste
                $openApi->getPaths()->addPath($key, $path->withGet(null));
            }
        }

        $schemas = $openApi->getComponents()->getSecuritySchemes();
        $schemas['bearerAuth'] = new \ArrayObject([
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT'
        ]);

        $schemas = $openApi->getComponents()->getSchemas();
        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'bonnin.a.k@gmail.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'password',
                ],
            ],
        ]);

        $schemas['Token'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        // Add custom operation login
        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postApiLogin',
                tags: ['Auth'],
                responses: [
                    '200' => [
                        'description' => 'Token JWT',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token'
                                ]
                            ]
                        ]
                    ]
                ],
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials'
                            ]
                        ]
                    ]),
                ),
            )
        );

        $openApi->getPaths()->addPath('/api/login', $pathItem);

        // Add custom operation logout
        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postApiLogout',
                tags: ['Auth'],
                responses: [
                    '204' => []
                ],
            )
        );

        $openApi->getPaths()->addPath('/api/logout', $pathItem);

        // Delete id param for /api/me request
        $meOperation = $openApi->getPaths()->getPath('/api/me')->getGet()->withParameters([]);
        $mePathItem = $openApi->getPaths()->getPath('/api/me')->withGet($meOperation);
        $openApi->getPaths()->addPath('/api/me', $mePathItem);

        return $openApi;
    }
}