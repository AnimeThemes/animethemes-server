<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class TwoFactorAuthenticationEnabledFilter extends Filter
{

    const ENABLED = 'enabled';
    const DISABLED = 'disabled';

    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Get the displayable name of the filter.
     *
     * @return string
     */
    public function name()
    {
        return __('nova.2fa_enabled');
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        switch ($value) {
            case self::ENABLED:
                return $query->whereRaw('exists (select * from two_factor_authentications where users.id = two_factor_authentications.authenticatable_id and two_factor_authentications.enabled_at is not null)');
            case self::DISABLED:
                return $query->whereRaw('not exists (select * from two_factor_authentications where users.id = two_factor_authentications.authenticatable_id and two_factor_authentications.enabled_at is not null)');
            default:
                return $query;
        }
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            __('nova.enabled') => self::ENABLED,
            __('nova.disabled') => self::DISABLED
        ];
    }
}
