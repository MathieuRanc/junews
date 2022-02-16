<?php
// api/src/OpenApi/JwtDecorator.php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

use function PHPSTORM_META\type;

final class EventDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Event'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                "presence" => [
                    'type' => 'boolean',
                    'readOnly' => true,
                ],
                "nomEvenement" => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                "descriptionEvenement" => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                "imageEvent" => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                "lieuEvent" => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                "dateEvenement" => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                "nomAssociation" => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                "nbEtudiant" => [
                    'type' => 'integer',
                    'readOnly' => true,
                ],
                "listeParticipant" => [
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
                'listeEventEtudiant' => [
                    'type' => 'array',
                    'readOnly' => true,
                    'items' => [
                        'type' => 'string',
                        'readOnly' => true,
                    ]
                ]
            ],
        ]);
        $schemas['EventCredentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'evenement' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'Event',
            get: new Model\Operation(
                operationId: 'getEventById',
                tags: ["Evenement"],
                responses: [
                    '200' => [
                        'description' => 'Get event by ID for current user',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Event',
                                ],
                            ],
                        ],
                    ],
                ],
                parameters: [
                    [
                        'name' => 'id',
                        'description' => 'Event ID',
                        'in' => 'path',
                        'required' => true,
                        'schema' =>
                        [
                            'type' => 'integer',
                            'format' => 'int64'
                        ]
                    ],
                ],
                description: 'Get event by ID for current user',
                summary: 'Get event by ID for current user',
            ),
        );
        $openApi->getPaths()->addPath('/evenements/{id}', $pathItem);

        return $openApi;
    }
}
