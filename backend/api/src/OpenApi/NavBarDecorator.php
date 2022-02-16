<?php
// api/src/OpenApi/JwtDecorator.php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

final class NavBarDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['NavBar'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'administre' => [
                    'type' => 'boolean',
                    'readOnly' => true,
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'Navbar',
            get: new Model\Operation(
                operationId: 'getNavbarItem',
                tags: ["Etudiant"],
                responses: [
                    '200' => [
                        'description' => 'Get navbar information for current user',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/NavBar',
                                ],
                            ],
                        ],
                    ],
                ],
                description: 'Get navbar information for current user',
                summary: 'Get navbar information for current user',
            ),
        );
        $openApi->getPaths()->addPath('/etudiants/navbar', $pathItem);

        return $openApi;
    }
}
