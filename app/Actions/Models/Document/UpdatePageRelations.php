<?php

declare(strict_types=1);

namespace App\Actions\Models\Document;

use App\Models\Document\Page;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdatePageRelations
{
    public function handle(Page $page): void
    {
        try {
            DB::beginTransaction();

            $page->previous?->next()?->associate($page)?->saveQuietly();
            $page->next?->previous()?->associate($page)?->saveQuietly();

            $oldPreviousId = $page->getOriginal(Page::ATTRIBUTE_PREVIOUS);
            if ($oldPreviousId && $oldPreviousId !== $page->previous?->getKey()) {
                Page::query()->find($oldPreviousId)->next()->disassociate()->saveQuietly();
            }

            $oldNextId = $page->getOriginal(Page::ATTRIBUTE_NEXT);
            if ($oldNextId && $oldNextId !== $page->next?->getKey()) {
                Page::query()->find($oldNextId)->previous()->disassociate()->saveQuietly();
            }

            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}
