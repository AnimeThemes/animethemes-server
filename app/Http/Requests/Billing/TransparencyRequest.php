<?php

declare(strict_types=1);

namespace App\Http\Requests\Billing;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Models\Billing\Balance;
use App\Models\Billing\Transaction;
use App\Rules\Billing\TransparencyDateRule;
use Carbon\Carbon;
use DateTime;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class TransparencyRequest.
 */
class TransparencyRequest extends FormRequest
{
    /**
     * The list of valid transparency dates.
     *
     * @var Collection
     */
    protected Collection $validDates;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->validDates = $this->initializeValidDates();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'date' => [
                'nullable',
                'date_format:Y-m',
                new TransparencyDateRule($this->getValidDates()),
            ],
        ];
    }

    /**
     * Initialize list of year/month combinations for transparency filtering.
     *
     * @return Collection
     */
    protected function initializeValidDates(): Collection
    {
        $balanceDates = Balance::query()->distinct('date')->pluck('date');
        $transactionDates = Transaction::query()->distinct('date')->pluck('date');

        $validDates = $balanceDates->concat($transactionDates);

        $validDates = $validDates->unique(function (Carbon $date) {
            return $date->format(AllowedDateFormat::YM);
        });

        return $validDates->sortDesc();
    }

    /**
     * Get the list of valid year/month combinations for transparency filtering.
     *
     * @return Collection
     */
    public function getValidDates(): Collection
    {
        return $this->validDates;
    }

    /**
     * Get the validated year/month combination for the transparency filter.
     *
     * @return Carbon|null
     */
    public function getSelectedDate(): ?Carbon
    {
        $validDate = Arr::get($this->validated(), 'date');

        if ($validDate === null) {
            return $this->getValidDates()->first();
        }

        return Carbon::instance(DateTime::createFromFormat('!'.AllowedDateFormat::YM, $validDate));
    }

    /**
     * Get Balances for selected month.
     *
     * @return Collection
     */
    public function getBalances(): Collection
    {
        $date = $this->getSelectedDate();

        if ($date === null) {
            return Collection::make();
        }

        return Balance::query()
            ->select(['service', 'frequency', 'usage', 'balance'])
            ->whereMonth('date', ComparisonOperator::EQ, $date)
            ->whereYear('date', ComparisonOperator::EQ, $date)
            ->orderBy('usage', 'desc')
            ->get();
    }

    /**
     * Get Transactions for selected month.
     *
     * @return Collection
     */
    public function getTransactions(): Collection
    {
        $date = $this->getSelectedDate();

        if ($date === null) {
            return Collection::make();
        }

        return Transaction::query()
            ->select(['date', 'service', 'amount', 'description'])
            ->whereMonth('date', ComparisonOperator::EQ, $date)
            ->whereYear('date', ComparisonOperator::EQ, $date)
            ->orderBy('date', 'desc')
            ->get();
    }
}