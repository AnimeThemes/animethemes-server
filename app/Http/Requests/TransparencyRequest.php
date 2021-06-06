<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Filter\AllowedDateFormat;
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
        $balanceDates = Balance::distinct('date')->pluck('date');
        $transactionDates = Transaction::distinct('date')->pluck('date');

        $validDates = $balanceDates->concat($transactionDates);

        $validDates = $validDates->unique(function (Carbon $date) {
            return $date->format(AllowedDateFormat::WITH_MONTH);
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
        $validDate =  Arr::get($this->validated(),'date');

        if ($validDate === null) {
            return $this->getValidDates()->first();
        }

        return Carbon::instance(DateTime::createFromFormat('!'.AllowedDateFormat::WITH_MONTH, $validDate));
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

        return Balance::whereMonth('date', strval($date->month))
            ->whereYear('date', strval($date->year))
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

        return Transaction::whereMonth('date', strval($date->month))
            ->whereYear('date', strval($date->year))
            ->get();
    }
}
