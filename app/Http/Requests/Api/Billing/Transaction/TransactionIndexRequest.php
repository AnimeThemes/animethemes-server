<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Billing\Transaction;

use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Query;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Billing\Collection\TransactionCollection;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class TransactionIndexRequest.
 */
class TransactionIndexRequest extends IndexRequest
{
    /**
     * Get the underlying resource collection.
     *
     * @return BaseCollection
     */
    protected function getCollection(): BaseCollection
    {
        return TransactionCollection::make(new MissingValue(), Query::make());
    }

    /**
     * Get the search validation rules.
     *
     * @return array
     */
    protected function getSearchRules(): array
    {
        return [
            SearchParser::$param => [
                'prohibited',
            ],
        ];
    }
}
