<?php

declare(strict_types=1);

namespace App\GraphQL\Support;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Executor\Values;
use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\AST\FragmentSpreadNode;
use GraphQL\Language\AST\InlineFragmentNode;
use GraphQL\Language\AST\SelectionSetNode;
use GraphQL\Type\Definition\HasFieldsType;
use GraphQL\Type\Definition\NamedType;
use GraphQL\Type\Definition\ResolveInfo as BaseResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\UnionType;
use GraphQL\Type\Definition\WrappingType;
use GraphQL\Type\Introspection;

class ResolveInfo extends BaseResolveInfo
{
    public function __construct(BaseResolveInfo $resolveInfo)
    {
        parent::__construct(
            $resolveInfo->fieldDefinition,
            $resolveInfo->fieldNodes,
            $resolveInfo->parentType,
            $resolveInfo->path,
            $resolveInfo->schema,
            $resolveInfo->fragments,
            $resolveInfo->rootValue,
            $resolveInfo->operation,
            $resolveInfo->variableValues,
            $resolveInfo->unaliasedPath
        );
    }

    public function getFieldSelectionWithAliases(int $depth = 0): array
    {
        $fields = [];

        foreach ($this->fieldNodes as $fieldNode) {
            $selectionSet = $fieldNode->selectionSet;
            if ($selectionSet !== null) {
                $field = $this->parentType->getField($fieldNode->name->value);
                $fieldType = $field->getType();

                $fields = array_merge_recursive(
                    $fields,
                    $this->foldSelectionWithAlias($selectionSet, $depth, $fieldType)
                );
            }
        }

        return $fields;
    }

    /**
     * @return array<string>
     *
     * @throws \Exception
     * @throws Error
     * @throws InvariantViolation
     */
    protected function foldSelectionWithAlias(SelectionSetNode $selectionSet, int $descend, Type $parentType): array
    {
        /** @var array<string, bool> $fields */
        $fields = [];

        if ($parentType instanceof WrappingType) {
            $parentType = $parentType->getInnermostType();
        }

        foreach ($selectionSet->selections as $selection) {
            if ($selection instanceof FieldNode) {
                $fieldName = $selection->name->value;
                $aliasName = $selection->alias->value ?? $fieldName;

                if ($fieldName === Introspection::TYPE_NAME_FIELD_NAME) {
                    continue;
                }
                assert($parentType instanceof HasFieldsType, 'ensured by query validation');

                $aliasInfo = &$fields[$fieldName][$aliasName];

                $fieldDef = $parentType->getField($fieldName);

                $aliasInfo['args'] = Values::getArgumentValues($fieldDef, $selection, $this->variableValues);

                $fieldType = $fieldDef->getType();

                $namedFieldType = $fieldType;
                if ($namedFieldType instanceof WrappingType) {
                    $namedFieldType = $namedFieldType->getInnermostType();
                }

                $aliasInfo['type'] = $namedFieldType;

                if ($descend <= 0) {
                    continue;
                }

                $nestedSelectionSet = $selection->selectionSet;
                if ($nestedSelectionSet === null) {
                    continue;
                }

                if ($namedFieldType instanceof UnionType) {
                    $aliasInfo['unions'] = $this->foldSelectionWithAlias($nestedSelectionSet, $descend, $fieldType);
                    continue;
                }

                $aliasInfo['selectionSet'] = $this->foldSelectionWithAlias($nestedSelectionSet, $descend - 1, $fieldType);
            } elseif ($selection instanceof FragmentSpreadNode) {
                $spreadName = $selection->name->value;
                $fragment = $this->fragments[$spreadName] ?? null;
                if ($fragment === null) {
                    continue;
                }

                $fieldType = $this->schema->getType($fragment->typeCondition->name->value);
                assert($fieldType instanceof Type, 'ensured by query validation');

                $fields = array_merge(
                    $this->foldSelectionWithAlias($fragment->selectionSet, $descend, $fieldType),
                    $fields
                );
            } elseif ($selection instanceof InlineFragmentNode) {
                $typeCondition = $selection->typeCondition;
                $fieldType = $typeCondition === null
                    ? $parentType
                    : $this->schema->getType($typeCondition->name->value);
                assert($fieldType instanceof Type, 'ensured by query validation');

                if ($parentType instanceof UnionType) {
                    assert($fieldType instanceof NamedType, 'ensured by query validation');
                    $fieldTypeInfo = &$fields[$fieldType->name()];
                    $fieldTypeInfo['type'] = $fieldType;
                    $fieldTypeInfo['selectionSet'] = $this->foldSelectionWithAlias($selection->selectionSet, $descend, $fieldType);
                    continue;
                }

                $fields = array_merge(
                    $this->foldSelectionWithAlias($selection->selectionSet, $descend, $fieldType),
                    $fields
                );
            }
        }

        return $fields;
    }
}
