<?php
// api/src/OpenApi/JwtDecorator.php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

final class EvenementsIdInfosDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['EvenementIdInfos'] = new \ArrayObject([
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
                            "identite" => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                            "photoEtudiant" => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                        ]
                    ]
                ],
                "listeEventEtudiant" => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                        'readOnly' => true,
                    ]
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'EvenementIdInfos',
            get: new Model\Operation(
                operationId: 'getEventById',
                // operationId: 'postCredentialsItem',
                tags: ["Evenement"],
                responses: [
                    '200' => [
                        'description' => 'Get events informations by ID',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/EvenementIdInfos',
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
                description: 'Get events informations by ID',
                summary: 'Get events informations by ID',
            ),
        );

        $openApi->getPaths()->addPath('/evenements/{id}/infos', $pathItem);

        return $openApi;
    }
}
