<?php

declare(strict_types=1);

namespace App\Http\Requests\Billing;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Enums\Http\Api\Sort\Direction;
use App\Models\Billing\Balance;
use App\Models\Billing\Transaction;
use App\Rules\Billing\TransparencyDateRule;
use DateTime;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class TransparencyRequest.
 */
class TransparencyRequest extends FormRequest
{
    /**
     * The list of valid transparency dates.
     *
     * @var Collection<int, Carbon>
     */
    protected readonly Collection $validDates;

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
     * @return array<string, array>
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
     * @return Collection<int, Carbon>
     */
    protected function initializeValidDates(): Collection
    {
        $balanceDates = Balance::query()
            ->distinct(Balance::ATTRIBUTE_DATE)
            ->pluck(Balance::ATTRIBUTE_DATE);

        $transactionDates = Transaction::query()
            ->distinct(Transaction::ATTRIBUTE_DATE)
            ->pluck(Transaction::ATTRIBUTE_DATE);

        $validDates = $balanceDates->concat($transactionDates);

        $validDates = $validDates->unique(fn (Carbon $date) => $date->format(AllowedDateFormat::YM));

        return $validDates->sortDesc();
    }

    /**
     * Get the list of valid year/month combinations for transparency filtering.
     *
     * @return Collection<int, Carbon>
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
     * @return Collection<int, Balance>
     */
    public function getBalances(): Collection
    {
        $date = $this->getSelectedDate();

        if ($date === null) {
            return Collection::make();
        }

        return Balance::query()
            ->select([
                Balance::ATTRIBUTE_BALANCE,
                Balance::ATTRIBUTE_FREQUENCY,
                Balance::ATTRIBUTE_SERVICE,
                Balance::ATTRIBUTE_USAGE,
            ])
            ->whereMonth(Balance::ATTRIBUTE_DATE, ComparisonOperator::EQ, $date)
            ->whereYear(Balance::ATTRIBUTE_DATE, ComparisonOperator::EQ, $date)
            ->orderBy(Balance::ATTRIBUTE_USAGE, Direction::DESCENDING)
            ->get();
    }

    /**
     * Get Transactions for selected month.
     *
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        $date = $this->getSelectedDate();

        if ($date === null) {
            return Collection::make();
        }

        return Transaction::query()
            ->select([
                Transaction::ATTRIBUTE_AMOUNT,
                Transaction::ATTRIBUTE_DATE,
                Transaction::ATTRIBUTE_DESCRIPTION,
                Transaction::ATTRIBUTE_SERVICE,
            ])
            ->whereMonth(Transaction::ATTRIBUTE_DATE, ComparisonOperator::EQ, $date)
            ->whereYear(Transaction::ATTRIBUTE_DATE, ComparisonOperator::EQ, $date)
            ->orderBy(Transaction::ATTRIBUTE_DATE, Direction::DESCENDING)
            ->get();
    }
}
