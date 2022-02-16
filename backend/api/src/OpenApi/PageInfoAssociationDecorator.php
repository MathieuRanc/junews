<?php
// api/src/OpenApi/JwtDecorator.php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

final class PageInfoAssociationDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['PageInfoAssociation'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                "administre" => [
                    'type' => 'boolean',
                    'readOnly' => true,
                ],
                "nomAssociation" => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                "logoAssociation" => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                "descriptionAssociation" => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                "nbParticipants" => [
                    'type' => 'integer',
                    'readOnly' => true,
                ],
                "listeEvenements" => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            "nomEvenement" => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                            "dateEvenement" => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                            "imageEvent" => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                        ]
                    ]
                ],
                "ListeMembres" => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'identite' => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                            'photoEtudiant' => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                        ]
                    ]
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'PageInfoAssociation',
            get: new Model\Operation(
                operationId: 'getAssoById',
                // operationId: 'postCredentialsItem',
                tags: ["Association"],
                responses: [
                    '200' => [
                        'description' => 'Get informations for association by ID',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/PageInfoAssociation',
                                ],
                            ],
                        ],
                    ],
                ],
                parameters: [
                    [
                        'name' => 'id',
                        'description' => 'Association ID',
                        'in' => 'path',
                        'required' => true,
                        'schema' =>
                        [
                            'type' => 'integer',
                            'format' => 'int64'
                        ]
                    ],
                ],
                description: 'Get informations for association by ID',
                summary: 'Get informations for association by ID',
            ),
        );

        $openApi->getPaths()->addPath('/associations/{id}/infos', $pathItem);

        return $openApi;
    }
}
