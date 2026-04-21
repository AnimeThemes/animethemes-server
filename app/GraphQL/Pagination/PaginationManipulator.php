<?php

declare(strict_types=1);

namespace App\GraphQL\Pagination;

use GraphQL\Language\AST\FieldDefinitionNode;
use GraphQL\Language\AST\InterfaceTypeDefinitionNode;
use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use GraphQL\Language\Parser;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Pagination\PaginationManipulator as BasePaginationManipulator;
use Nuwave\Lighthouse\Pagination\PaginationType;
use Nuwave\Lighthouse\Schema\AST\ASTHelper;

class PaginationManipulator extends BasePaginationManipulator
{
    protected function registerConnection(
        FieldDefinitionNode &$fieldDefinition,
        ObjectTypeDefinitionNode|InterfaceTypeDefinitionNode &$parentType,
        PaginationType $paginationType,
        ?int $defaultCount = null,
        ?int $maxCount = null,
        ?ObjectTypeDefinitionNode $edgeType = null,
    ): void {
        $pageInfoNode = $this->pageInfo();
        if (! isset($this->documentAST->types[$pageInfoNode->getName()->value])) {
            $this->documentAST->setTypeDefinition($pageInfoNode);
        }

        $fieldTypeName = ASTHelper::getUnderlyingTypeName($fieldDefinition);

        if ($edgeType instanceof ObjectTypeDefinitionNode) {
            $connectionEdgeName = $edgeType->name->value;
            $connectionTypeName = Str::remove('Edge', "{$connectionEdgeName}Connection");
        } else {
            $connectionEdgeName = "{$fieldTypeName}Edge";
            $connectionTypeName = "{$fieldTypeName}Connection";
        }

        $connectionFieldClass = addslashes(ConnectionType::class);
        $connectionType = Parser::objectTypeDefinition(/** @lang GraphQL */ <<<GRAPHQL
            "A paginated list of {$fieldTypeName} edges."
            type {$connectionTypeName} {
                "Pagination information about the list of edges."
                {$paginationType->infoFieldName()}: PageInfo! @field(resolver: "{$connectionFieldClass}@pageInfoResolver")

                "A list of {$fieldTypeName} resources. Use this if you don't care about pivot fields."
                nodes: [{$fieldTypeName}!]! @field(resolver: "{$connectionFieldClass}@nodesResolver")

                "A list of {$fieldTypeName} edges."
                edges: [{$connectionEdgeName}!]! @field(resolver: "{$connectionFieldClass}@edgeResolver")
            }
GRAPHQL
        );
        $this->addPaginationWrapperType($connectionType);

        $connectionEdge = $edgeType
            ?? $this->documentAST->types[$connectionEdgeName]
            ?? Parser::objectTypeDefinition(/** @lang GraphQL */ <<<GRAPHQL
                "An edge that contains a node of type {$fieldTypeName} and a cursor."
                type {$connectionEdgeName} {
                    "The {$fieldTypeName} node."
                    node: {$fieldTypeName}!

                    "A unique cursor that can be used for pagination."
                    cursor: String!
                }
GRAPHQL
            );
        $this->documentAST->setTypeDefinition($connectionEdge);

        $fieldDefinition->arguments[] = Parser::inputValueDefinition(
            self::countArgument($defaultCount, $maxCount),
        );
        $fieldDefinition->arguments[] = Parser::inputValueDefinition(/** @lang GraphQL */ <<<'GRAPHQL'
"A cursor after which elements are returned."
after: String
GRAPHQL
        );

        $fieldDefinition->type = $this->paginationResultType($connectionTypeName);
        $parentType->fields = ASTHelper::mergeUniqueNodeList($parentType->fields, [$fieldDefinition], true);
    }

    protected function registerSimplePaginator(
        FieldDefinitionNode &$fieldDefinition,
        ObjectTypeDefinitionNode|InterfaceTypeDefinitionNode &$parentType,
        PaginationType $paginationType,
        ?int $defaultCount = null,
        ?int $maxCount = null,
    ): void {
        $simplePaginatorInfoNode = $this->simplePaginatorInfo();
        if (! isset($this->documentAST->types[$simplePaginatorInfoNode->getName()->value])) {
            $this->documentAST->setTypeDefinition($simplePaginatorInfoNode);
        }

        $fieldDefinition->arguments[] = Parser::inputValueDefinition(
            self::countArgument($defaultCount, $maxCount),
        );
        $fieldDefinition->arguments[] = Parser::inputValueDefinition(/** @lang GraphQL */ <<<'GRAPHQL'
            "The offset from which items are returned."
            page: Int
        GRAPHQL);

        $parentType->fields = ASTHelper::mergeUniqueNodeList($parentType->fields, [$fieldDefinition], true);
    }
}
