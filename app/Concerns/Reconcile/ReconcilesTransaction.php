<?php

namespace App\Concerns\Reconcile;

use App\Billing\Transaction\TransactionsFactory;
use App\Enums\Billing\Service;
use App\Models\Billing\Transaction;
use Illuminate\Support\Collection;

trait ReconcilesTransaction
{
    use ReconcilesContent;

    /**
     * The billing service.
     *
     * @var \App\Enums\Billing\Service
     */
    protected $service;

    /**
     * Set the billing service.
     *
     * @param Service $service
     * @return void
     */
    protected function setService(Service $service)
    {
        $this->service = $service;
    }

    /**
     * Initialize collection of models from source.
     *
     * @return \Illuminate\Support\Collection
     */
    public function initializeContentFromSource()
    {
        $collector = TransactionsFactory::create($this->service);

        if ($collector === null) {
            return Collection::make();
        }

        return $collector->getTransactions();
    }

    /**
     * The list of columns to pluck for create and delete steps.
     *
     * @return array
     */
    public function getCreateDeleteColumns()
    {
        return [
            'transaction_id',
            'external_id',
        ];
    }

    /**
     * Initialize collection of models from db.
     *
     * @param array $columns
     * @return \Illuminate\Support\Collection
     */
    public function initializeContentFromDb(array $columns = ['*'])
    {
        return Transaction::where('service', optional($this->service)->value)->get($columns);
    }

    /**
     * Create models that exist in source but not in the database.
     *
     * @param Collection $source_content
     * @param Collection $db_content
     * @return void
     */
    public function createContentOnlyInSource(Collection $source_content, Collection $db_content)
    {
        $create_transactions = $source_content->diffUsing($db_content, function (Transaction $a, Transaction $b) {
            return $a->external_id <=> $b->external_id;
        });

        foreach ($create_transactions as $create_transaction) {
            $create_result = $create_transaction->save();
            if ($create_result) {
                $this->created++;
                $this->handleCreated($create_transaction);
            } else {
                $this->created_failed++;
                $this->handleFailedCreation($create_transaction);
            }
        }
    }

    /**
     * Create models that exist in source but not in the database.
     *
     * @param Collection $source_content
     * @param Collection $db_content
     * @return void
     */
    public function deleteContentOnlyInDb(Collection $source_content, Collection $db_content)
    {
        $delete_transactions = $db_content->diffUsing($source_content, function (Transaction $a, Transaction $b) {
            return $a->external_id <=> $b->external_id;
        });

        foreach ($delete_transactions as $delete_transaction) {
            $delete_result = $delete_transaction->delete();
            if ($delete_result) {
                $this->deleted++;
                $this->handleDeleted($delete_transaction);
            } else {
                $this->deleted_failed++;
                $this->handleFailedDeletion($delete_transaction);
            }
        }
    }

    /**
     * The list of columns to pluck for update step.
     *
     * @return array
     */
    public function getUpdateColumns()
    {
        return [
            'transaction_id',
            'external_id',
            'description',
            'amount',
        ];
    }

    /**
     * Create models that have been changed in the source.
     *
     * @param Collection $source_content
     * @param Collection $db_content
     * @return void
     */
    public function updateContentModifiedInSource(Collection $source_content, Collection $db_content)
    {
        $updated_transactions = $db_content->diffUsing($source_content, function (Transaction $a, Transaction $b) {
            return [$a->external_id, $a->description, $a->amount] <=> [$b->external_id, $b->description, $b->amount];
        });

        foreach ($updated_transactions as $updated_transaction) {
            $source_transaction = $source_content->firstWhere('external_id', $updated_transaction->external_id);
            if (! is_null($source_transaction)) {
                $update_result = $updated_transaction->update($source_transaction->toArray());
                if ($update_result) {
                    $this->updated++;
                    $this->handleUpdated($updated_transaction);
                } else {
                    $this->updated_failed++;
                    $this->handleFailedUpdate($updated_transaction);
                }
            }
        }
    }
}
