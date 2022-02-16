<?php
// api/src/OpenApi/JwtDecorator.php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

final class FeedEventDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['FeedEvent'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'dataOneGrid' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'idEvent' => [
                                'type' => 'integer',
                                'readOnly' => true,
                            ],
                            'nomAssociation' => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                            'nomEvent' => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                            'nbParticipant' => [
                                'type' => 'integer',
                                'readOnly' => true,
                            ],
                            'imageEvent' => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                            'heure' => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                            'jour' => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                            'mois' => [
                                'type' => 'string',
                                'readOnly' => true,
                            ],
                            'presence' => [
                                'type' => 'string',
                                'type' => 'boolean',
                                'readOnly' => true,
                            ]
                        ]
                    ]
                ],
            ],
        ]);
        // $schemas['Credentials'] = new \ArrayObject([
        //     'type' => 'object',
        //     'properties' => [
        //         'idEtudiant' => [
        //             'type' => 'integer',
        //             'example' => 1,
        //         ],
        //     ],
        // ]);

        $pathItem = new Model\PathItem(
            ref: 'Feed event',
            get: new Model\Operation(
                operationId: 'getCredentialsItem',
                tags: ["Etudiant"],
                responses: [
                    '200' => [
                        'description' => 'Get event feed',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/FeedEvent',
                                ],
                            ],
                        ],
                    ],
                ],
                // parameters: ['idEtudiant' => [
                //     'type' => 'integer',
                //     'example' => 1,
                // ]],
                description: 'Get event feed',
                summary: 'Get event feed',
                // requestBody: new Model\RequestBody(
                //     description: 'Get event feed',
                //     content: new \ArrayObject([
                //         'application/json' => [
                //             'schema' => [
                //                 '$ref' => '#/components/schemas/Credentials',
                //             ],
                //         ],
                //     ]),
                // ),
            ),
        );
        $openApi->getPaths()->addPath('/etudiants/feed', $pathItem);

        return $openApi;
    }
}
