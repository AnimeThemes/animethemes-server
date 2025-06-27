<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Scope;

use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\RelationScope;
use App\Http\Api\Scope\ScopeParser;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class ScopeParserTest.
 */
class ScopeParserTest extends TestCase
{
    use WithFaker;

    /**
     * The Scope Parser shall parse global scopes.
     *
     * @return void
     */
    public function test_parse_global_scope(): void
    {
        static::assertInstanceOf(GlobalScope::class, ScopeParser::parse(''));
    }

    /**
     * The Scope Parser shall parse type scopes.
     *
     * @return void
     */
    public function test_parse_type_scope(): void
    {
        static::assertInstanceOf(TypeScope::class, ScopeParser::parse($this->faker->word()));
    }

    /**
     * The Scope Parser shall parse relation scopes.
     *
     * @return void
     */
    public function test_parse_relation_scope(): void
    {
        $relation = collect($this->faker->words())->join('.');

        static::assertInstanceOf(RelationScope::class, ScopeParser::parse($relation));
    }
}
