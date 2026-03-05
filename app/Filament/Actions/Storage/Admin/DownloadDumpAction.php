<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Admin;

use App\Actions\Http\Admin\Dump\DumpDownloadAction as DownloadAction;
use App\Filament\Actions\BaseAction;
use App\Models\Admin\Dump;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadDumpAction extends BaseAction
{
    public static function getDefaultName(): ?string
    {
        return 'download-dump';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(Heroicon::OutlinedArrowDownTray);

        $this->label(__('filament.actions.base.download'));

        $this->visible(fn (Dump $record) => Gate::allows('view', $record));

        $this->authorize('view');

        $this->action(fn (Dump $record): StreamedResponse => new DownloadAction($record)->download());
    }
}
