<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;
// use Psr\Log\LoggerInterface;
use ArrayObject;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

// priority at "-1" to overwrite the one from the JWT library
#[AsDecorator(decorates: 'api_platform.openapi.factory', priority: -1)]
class OpenApiFactory implements OpenApiFactoryInterface {
    public function __construct(
        private OpenApiFactoryInterface $decorated,
        // private LoggerInterface $logger,
    ) {}

    public function __invoke(array $context = []): OpenApi {
        $openApi = ($this->decorated)($context);

        // Auth schemas

        $schemas = $openApi->getComponents()->getSchemas();

        // employee credentials
        $schemas['AuthInputEmployee'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'user1234',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'password1234',
                ],
            ],
        ]);
        // client credentials
        $schemas['AuthInputClient'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'example' => 'user1234@example.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'password1234',
                ],
            ],
        ]);

        // $schemas['RefreshInput'] = new ArrayObject([
        //     'type' => 'object',
        //     'properties' => [
        //         'refresh_token' => [
        //             'type' => 'string',
        //             'example' => 'f4a5...',
        //         ],
        //     ],
        // ]);

        // authentication response for employees
        $schemas['AuthOutputEmployee'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                // 'refresh_token' => [
                //     'type' => 'string',
                //     'readOnly' => true,
                // ],
                // 'user' => [
                //     'type' => 'object',
                //     'properties' => [
                //         'id' => ['type' => 'integer'],
                //         'firstName' => ['type' => 'string'],
                //         'lastName' => ['type' => 'string'],
                //         'email' => ['type' => 'string'],
                //     ]
                // ],
                'employee' => [
                    'type' => 'object',
                    'readOnly' => true,
                    'properties' => [
                        'email' => ['type' => 'string'],
                        'firstName' => ['type' => 'string'],
                        'lastName' => ['type' => 'string'],
                        'roles' => [
                            'type' => 'array',
                            'readOnly' => true,
                            'items' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ]);
        // authentication response for employees
        $schemas['AuthOutputClient'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        // Auth paths
        $paths = $openApi->getPaths();

        // login endpoint
        $loginPathEmployee = new PathItem(
            ref: 'JWT token employee',
            post: new Operation(
                operationId: 'postLoginItemEmployee',
                tags: ['Auth'],
                responses: [
                    '200' => [
                        'description' => 'Authentication successful',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/AuthOutputEmployee',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Create a user JWT token for employees',
                requestBody: new RequestBody(
                    description: 'The credentials of the employee',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/AuthInputEmployee',
                            ],
                        ],
                    ]),
                ),
            ),
        );
        $paths->addPath('/api/auth/employee', $loginPathEmployee);

        $loginPathClient = new PathItem(
            ref: 'JWT token client',
            post: new Operation(
                operationId: 'postLoginItemClient',
                tags: ['Auth'],
                responses: [
                    '200' => [
                        'description' => 'Authentication successful',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/AuthOutputClient',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Create a user JWT token for clients',
                requestBody: new RequestBody(
                    description: 'The credentials of the client',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/AuthInputClient',
                            ],
                        ],
                    ]),
                ),
            ),
        );
        $paths->addPath('/api/auth/client', $loginPathClient);

        // refresh token endpoint
        // $refreshPath = new PathItem(
        //     ref: 'refresh JWT token',
        //     post: new Operation(
        //         operationId: 'postRefreshTokenItem',
        //         tags: ['Auth'],
        //         responses: [
        //             '200' => [
        //                 'description' => 'Token refresh successful',
        //                 'content' => [
        //                     'application/json' => [
        //                         'schema' => [
        //                             '$ref' => '#/components/schemas/AuthOutput',
        //                         ],
        //                     ],
        //                 ],
        //             ],
        //         ],
        //         summary: 'Refresh the JWT token',
        //         requestBody: new RequestBody(
        //             description: 'The credentials of the user',
        //             content: new ArrayObject([
        //                 'application/json' => [
        //                     'schema' => [
        //                         '$ref' => '#/components/schemas/RefreshInput',
        //                     ],
        //                 ],
        //             ]),
        //         ),
        //     ),
        // );
        // $paths->addPath('/api/auth/refresh', $refreshPath);

        return $openApi;
    }
}
