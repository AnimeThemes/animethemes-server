<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\ArtistMember;

use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\ArtistMemberSchema;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistMemberResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Member;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class ArtistMemberShowTest.
 */
class ArtistMemberShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Artist Member Show Endpoint shall return an error if the artist member does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $artist = Artist::factory()->createOne();
        $member = Artist::factory()->createOne();

        $response = $this->get(route('api.artistmember.show', ['artist' => $artist, 'member' => $member]));

        $response->assertNotFound();
    }

    /**
     * By default, the Artist Member Show Endpoint shall return an Artist Member Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $artistMember = ArtistMember::factory()
            ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
            ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
            ->createOne();

        $response = $this->get(route('api.artistmember.show', ['artist' => $artistMember->artist, 'member' => $artistMember->member]));

        $artistMember->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistMemberResource($artistMember, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Member Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new ArtistMemberSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $artistMember = ArtistMember::factory()
            ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
            ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
            ->createOne();

        $response = $this->get(route('api.artistmember.show', ['artist' => $artistMember->artist, 'member' => $artistMember->member] + $parameters));

        $artistMember->unsetRelations()->load($includedPaths->all());

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistMemberResource($artistMember, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Member Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new ArtistMemberSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                ArtistMemberResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $artistMember = ArtistMember::factory()
            ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
            ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
            ->createOne();

        $response = $this->get(route('api.artistmember.show', ['artist' => $artistMember->artist, 'member' => $artistMember->member] + $parameters));

        $artistMember->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistMemberResource($artistMember, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
