<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use App\GraphQL\Pagination\PaginationManipulator;
use GraphQL\Language\AST\FieldDefinitionNode;
use GraphQL\Language\AST\InterfaceTypeDefinitionNode;
use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use Nuwave\Lighthouse\Exceptions\DefinitionException;
use Nuwave\Lighthouse\Pagination\PaginationType;
use Nuwave\Lighthouse\Schema\AST\ASTHelper;
use Nuwave\Lighthouse\Schema\AST\DocumentAST;
use Nuwave\Lighthouse\Schema\Directives\RelationDirective;

abstract class RelationCustomDirective extends RelationDirective
{
    public function manipulateFieldDefinition(
        DocumentAST &$documentAST,
        FieldDefinitionNode &$fieldDefinition,
        ObjectTypeDefinitionNode|InterfaceTypeDefinitionNode &$parentType,
    ): void {
        $paginationType = $this->paginationType();

        // We default to not changing the field if no pagination type is set explicitly.
        // This makes sense for relations, as there should not be too many entries.
        if (! $paginationType instanceof PaginationType) {
            return;
        }

        $paginationManipulator = new PaginationManipulator($documentAST);

        $relatedModelName = ASTHelper::modelName($fieldDefinition);
        if (is_string($relatedModelName)) {
            try {
                $modelClass = $this->namespaceModelClass($relatedModelName);
                $paginationManipulator->setModelClass($modelClass);
            } catch (DefinitionException) {
                /** @see \Tests\Integration\Schema\Directives\HasManyDirectiveTest::testDoesNotRequireModelClassForPaginatedHasMany() */
            }
        }

        $paginationManipulator->transformToPaginatedField(
            $paginationType,
            $fieldDefinition,
            $parentType,
            $this->paginationDefaultCount(),
            $this->paginationMaxCount(),
            $this->edgeType($documentAST),
        );
    }

    protected function edgeType(DocumentAST $documentAST): ?ObjectTypeDefinitionNode
    {
        if ($edgeTypeName = $this->directiveArgValue('edgeType')) {
            $edgeType = $documentAST->types[$edgeTypeName] ?? null;
            if (! $edgeType instanceof ObjectTypeDefinitionNode) {
                throw new DefinitionException("The `edgeType` argument of @{$this->name()} on {$this->nodeName()} must reference an existing object type definition.");
            }

            return $edgeType;
        }

        return null;
    }

    protected function paginationMaxCount(): ?int
    {
        return $this->directiveArgValue('maxCount', $this->lighthouseConfig['pagination']['relation']['max_count']);
    }

    protected function paginationDefaultCount(): ?int
    {
        return $this->directiveArgValue('defaultCount', $this->lighthouseConfig['pagination']['relation']['default_count']);
    }
}
