<?php

declare(strict_types=1);

namespace App\Http\Api\Scope;

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
    public function testParseGlobalScope()
    {
        static::assertInstanceOf(GlobalScope::class, ScopeParser::parse(''));
    }

    /**
     * The Scope Parser shall parse type scopes.
     *
     * @return void
     */
    public function testParseTypeScope()
    {
        static::assertInstanceOf(TypeScope::class, ScopeParser::parse($this->faker->word()));
    }

    /**
     * The Scope Parser shall parse relation scopes.
     *
     * @return void
     */
    public function testParseRelationScope()
    {
        $relation = collect($this->faker->words())->join('.');

        static::assertInstanceOf(RelationScope::class, ScopeParser::parse($relation));
    }
}
