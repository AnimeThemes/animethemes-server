<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Song\Performance;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership;
use App\Pivots\Wiki\ArtistMember;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Arr;

/**
 * Class LoadMembersAction.
 */
class LoadMembersAction extends Action
{
    /**
     * The default name of the action.
     *
     * @return string|null
     */
    public static function getDefaultName(): ?string
    {
        return 'load-members';
    }

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.fields.performance.load_members.name'));

        $this->action(function (Get $get, Set $set) {
            $artistId = $get(Artist::ATTRIBUTE_ID);
            if ($artistId === null) {
                $set('memberships', []);

                return;
            }

            /** @var Artist $group */
            $group = Artist::query()
                ->with([Artist::RELATION_MEMBERS])
                ->find($artistId);

            $set('memberships', $group->members->map(fn (Artist $member) => [
                Membership::ATTRIBUTE_MEMBER => $member->getKey(),
                Membership::ATTRIBUTE_ALIAS => Arr::get($member->{$group->members()->getPivotAccessor()}, ArtistMember::ATTRIBUTE_ALIAS),
                Membership::ATTRIBUTE_AS => Arr::get($member->{$group->members()->getPivotAccessor()}, ArtistMember::ATTRIBUTE_AS),
            ])->toArray());
        });
    }
}
