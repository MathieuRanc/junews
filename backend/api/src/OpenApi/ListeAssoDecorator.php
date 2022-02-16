<?php
// api/src/OpenApi/JwtDecorator.php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

final class ListeAssoDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['ListeAsso'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'associations' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'nomAssociation' => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                            'nombreMembre' => [
                                'type' => 'integer',
                                'readOnly' => true,
                            ],
                            'logo' => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                        ]
                    ]
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'Association',
            get: new Model\Operation(
                operationId: 'getListeAsso',
                tags: ["Association"],
                responses: [
                    '200' => [
                        'description' => 'Get associations informations for current user',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/ListeAsso',
                                ],
                            ],
                        ],
                    ],
                ],
                description: 'Get associations informations for current user',
                summary: 'Get associations informations for current user',
            ),
        );
        $openApi->getPaths()->addPath('/associations/infos', $pathItem);

        return $openApi;
    }
}
