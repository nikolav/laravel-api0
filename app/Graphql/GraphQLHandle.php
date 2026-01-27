<?php

namespace App\Graphql;

use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\CustomScalarType;
use GraphQL\Utils\BuildSchema;
use GraphQL\Error\DebugFlag;

class GraphQLHandle
{
  private Schema $schema;

  function __construct()
  {
    $sdl = file_get_contents(__DIR__ . '/schema.graphql');

    $queryResolvers    = $this->loadResolvers(__DIR__ . '/resolvers/query/resolvers.php');
    $mutationResolvers = $this->loadResolvers(__DIR__ . '/resolvers/mutation/resolvers.php');

    $this->schema = BuildSchema::build(
      $sdl,
      function (array $typeConfig) use ($queryResolvers, $mutationResolvers) {
        $name = $typeConfig['name'] ?? null;

        if ($name === 'Query') {
          $typeConfig['fields'] =
            $this->attachResolvers($typeConfig['fields'], $queryResolvers);
        }

        if ($name === 'Mutation') {
          $typeConfig['fields'] =
            $this->attachResolvers($typeConfig['fields'], $mutationResolvers);
        }

        // custom scalar
        if ($name === 'JSON') {
          return new CustomScalarType([
            'name'         => 'JSON',
            'serialize'    => fn($value) => $value,
            'parseValue'   => fn($value) => $value,
            'parseLiteral' => function ($valueNode) {
              return property_exists($valueNode, 'value') ? $valueNode->value : null;
            },
          ]);
        }

        return $typeConfig;
      }
    );
  }

  function handle(array $payload): array
  {
    $debug = app()->isLocal()
      ? DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE
      : DebugFlag::NONE;

    return GraphQL::executeQuery(
      $this->schema,
      $payload['query'],
      rootValue: null,
      contextValue: null,
      variableValues: $payload['variables'] ?? null,
      operationName: $payload['operationName'] ?? null
    )->toArray($debug);
  }

  /**
   * @param array|callable $fields
   * @param array<string, callable> $resolvers
   */
  private function attachResolvers($fields, array $resolvers): callable
  {
    return function () use ($fields, $resolvers) {
      $resolvedFields = \is_callable($fields) ? $fields() : $fields;

      foreach ($resolvers as $fieldName => $resolver) {
        if (isset($resolvedFields[$fieldName])) {
          $resolvedFields[$fieldName]['resolve'] = $resolver;
        }
      }

      return $resolvedFields;
    };
  }

  /**
   * Safe loader for resolvers.php files.
   *
   * @param string $path
   * @return array<string, callable>
   */
  private function loadResolvers(string $path): array
  {
    if (!file_exists($path)) {
      return [];
    }

    $resolvers = require $path;

    return \is_array($resolvers) ? $resolvers : [];
  }
}
