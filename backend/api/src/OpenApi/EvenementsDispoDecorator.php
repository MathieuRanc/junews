<?php
// api/src/OpenApi/JwtDecorator.php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

final class EvenementDispoDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['EvenementDispo'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                "disponibilite" => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                        'readOnly' => true,
                    ]
                ],
            ],
        ]);
        $schemas['DispoCredentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'dateDebutEvent' => [
                    'type' => 'string',
                    'example' => '2021-07-02T10:00:00.000Z',
                ],
                'dateFinEvent' => [
                    'type' => 'string',
                    'example' => '2021-07-02T12:00:00.000Z',
                ],
                'typeEvent' => [
                    'type' => 'string',
                    'example' => 'Journee',
                ],
                'promotionEvent' => [
                    'type' => 'array',
                    'example' => [1, 2, 3],
                    'items' => [
                        'id' => [
                            'type' => 'integer',
                            'example' => 1,
                        ],
                    ]
                ]
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'EvenementDispo',
            post: new Model\Operation(
                operationId: 'postInfosEvenementsDispo',
                tags: ["Evenement"],
                responses: [
                    '200' => [
                        'description' => 'Get dispo for evenements',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/EvenementDispo',
                                ],
                            ],
                        ],
                    ],
                ],
                requestBody: new Model\RequestBody(
                    description: 'Get dispo for evenements',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/DispoCredentials',
                            ],
                        ],
                    ]),
                ),
                description: 'Get dispo for evenements',
                summary: 'Get dispo for evenements',
            ),
        );
        // $dateDebutEvent = (new \DateTime($parameters['dateDebutEvent']));
        // $dateFinEvent = (new \DateTime($parameters['dateFinEvent']));
        // $typeEvent = ($parameters['typeEvent']);
        // $idPromotions = $parameters['promotionEvent'];

        $openApi->getPaths()->addPath('/evenements/dispo', $pathItem);

        return $openApi;
    }
}
