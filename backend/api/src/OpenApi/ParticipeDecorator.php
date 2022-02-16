<?php
// api/src/OpenApi/JwtDecorator.php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

final class ParticipeDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Participe'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                "participe" => [
                    'type' => 'boolean',
                    'readOnly' => true,
                ],
                "message" => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);
        $schemas['ParticipeCredentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'participe' => [
                    'type' => 'boolean',
                    'example' => true,
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'Participe',
            post: new Model\Operation(
                operationId: 'postEtudiantByIdEvenement',
                // operationId: 'postCredentialsItem',
                tags: ["Evenement"],
                responses: [
                    '200' => [
                        'description' => 'Patch student evenement',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Participe',
                                ],
                            ],
                        ],
                    ],
                ],
                parameters: [
                    [
                        'name' => 'id',
                        'description' => 'Evenement ID',
                        'in' => 'path',
                        'required' => true,
                        'schema' =>
                        [
                            'type' => 'integer',
                            'format' => 'int64'
                        ]
                    ],
                ],
                requestBody: new Model\RequestBody(
                    description: 'Change participation for Etudiant by Evenement ID',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/ParticipeCredentials',
                            ],
                        ],
                    ]),
                ),
                description: 'Patch student evenement',
                summary: 'Patch student evenement',
            ),
        );

        $openApi->getPaths()->addPath('/evenements/{id}/participe', $pathItem);

        return $openApi;
    }
}
