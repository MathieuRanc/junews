<?php
// api/src/OpenApi/JwtDecorator.php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

final class EvenementInfosDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['EvenementInfos'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                "promotions" => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            "@id" => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                            "id" => [
                                'type' => 'integer',
                                'readOnly' => true,
                            ],
                            "nom" => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                        ]
                    ]
                ],
                "lieux" => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            '@id' => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                            'id' => [
                                'type' => 'integer',
                                'readOnly' => true,
                            ],
                            'nom' => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                        ]
                    ]
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'EvenementInfos',
            get: new Model\Operation(
                operationId: 'getInfosEvenements',
                tags: ["Evenement"],
                responses: [
                    '200' => [
                        'description' => 'Get events informations',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/EvenementInfos',
                                ],
                            ],
                        ],
                    ],
                ],
                description: 'Get events informations',
                summary: 'Get events informations',
            ),
        );

        $openApi->getPaths()->addPath('/evenements/infos', $pathItem);

        return $openApi;
    }
}
