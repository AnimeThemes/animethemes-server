<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Song\Performance;

use App\Filament\Resources\Wiki\Song\Performance\Schemas\PerformanceForm;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership;
use App\Pivots\Wiki\ArtistMember;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Arr;

class LoadMembersAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'load-members';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.fields.performance.load_members.name'));

        $this->action(function (Get $get, Set $set): void {
            $artistId = $get(Artist::ATTRIBUTE_ID);
            if ($artistId === null) {
                $set(PerformanceForm::REPEATER_MEMBERSHIPS, []);

                return;
            }

            /** @var Artist $group */
            $group = Artist::query()
                ->with([Artist::RELATION_MEMBERS])
                ->find($artistId);

            $set(PerformanceForm::REPEATER_MEMBERSHIPS, $group->members->map(fn (Artist $member): array => [
                Membership::ATTRIBUTE_MEMBER => $member->getKey(),
                Membership::ATTRIBUTE_ALIAS => Arr::get($member->{$group->members()->getPivotAccessor()}, ArtistMember::ATTRIBUTE_ALIAS),
                Membership::ATTRIBUTE_AS => Arr::get($member->{$group->members()->getPivotAccessor()}, ArtistMember::ATTRIBUTE_AS),
            ])->toArray());
        });
    }
}
