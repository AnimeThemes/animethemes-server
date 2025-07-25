<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types;

use App\GraphQL\Attributes\Hidden;
use Exception;
use GraphQL\Language\AST\EnumTypeDefinitionNode;
use GraphQL\Type\Definition\Deprecated;
use GraphQL\Type\Definition\Description;
use GraphQL\Type\Definition\EnumType as BaseEnumType;
use GraphQL\Utils\PhpDoc;
use ReflectionClass;
use ReflectionClassConstant;
use UnitEnum;

class EnumType extends BaseEnumType
{
    public const MULTIPLE_DESCRIPTIONS_DISALLOWED = 'Using more than 1 Description attribute is not supported.';
    public const MULTIPLE_DEPRECATIONS_DISALLOWED = 'Using more than 1 Deprecated attribute is not supported.';
    public const MULTIPLE_HIDDEN_DISALLOWED = 'Using more than 1 Hidden attribute is not supported.';

    /**
     * The enum class.
     *
     * @var class-string<UnitEnum>
     */
    protected string $enumClass;

    public function __construct(
        string $enumClass,
        ?string $name = null,
        ?string $description = null,
        ?EnumTypeDefinitionNode $astNode = null,
        ?array $extensionASTNodes = null
    ) {
        $this->enumClass = $enumClass;
        $reflection = new \ReflectionEnum($enumClass);

        $enumDefinitions = [];
        foreach ($enumClass::cases() as $case) {
            if ($this->hidden($reflection->getCase($case->name))) {
                continue;
            }
            $enumDefinitions[$case->name] = [
                'value' => $case->value,
                'description' => $this->extractDescription($reflection->getCase($case->name)),
                'deprecationReason' => $this->deprecationReason($reflection->getCase($case->name)),
            ];
        }

        parent::__construct([
            'name' => $name ?? class_basename($enumClass),
            'values' => $enumDefinitions,
            'description' => $description ?? $this->extractDescription($reflection),
            'astNode' => $astNode,
            'extensionASTNodes' => $extensionASTNodes,
        ]);
    }

    /**
     * @param  mixed  $value
     */
    public function serialize($value): string
    {
        return $value;
    }

    /**
     * @param  ReflectionClassConstant|ReflectionClass<UnitEnum>  $reflection
     *
     * @throws Exception
     */
    protected function extractDescription(ReflectionClassConstant|ReflectionClass $reflection): ?string
    {
        $attributes = $reflection->getAttributes(Description::class);

        if (count($attributes) === 1) {
            return $attributes[0]->newInstance()->description;
        }

        if (count($attributes) > 1) {
            throw new Exception(self::MULTIPLE_DESCRIPTIONS_DISALLOWED);
        }

        $comment = $reflection->getDocComment();
        $unpadded = PhpDoc::unpad($comment);

        return PhpDoc::unwrap($unpadded);
    }

    /**
     * @param  ReflectionClassConstant  $reflection
     *
     * @throws Exception
     */
    protected function deprecationReason(ReflectionClassConstant $reflection): ?string
    {
        $attributes = $reflection->getAttributes(Deprecated::class);

        if (count($attributes) === 1) {
            return $attributes[0]->newInstance()->reason;
        }

        if (count($attributes) > 1) {
            throw new Exception(self::MULTIPLE_DEPRECATIONS_DISALLOWED);
        }

        return null;
    }

    /**
     * Determine whether the enum case should be hidden.
     *
     * @throws Exception
     */
    protected function hidden(ReflectionClassConstant $reflection): bool
    {
        $attributes = $reflection->getAttributes(Hidden::class);

        if (count($attributes) === 1) {
            return $attributes[0]->newInstance()->hidden;
        }

        if (count($attributes) > 1) {
            throw new Exception(self::MULTIPLE_HIDDEN_DISALLOWED);
        }

        return false;
    }
}
