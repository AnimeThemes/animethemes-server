<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\BaseEnum;
use App\Models\Auth\User;

/**
 * Class WriteRequest.
 */
abstract class WriteRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $user = $this->user('sanctum');

        return $user instanceof User
            && $user->can($this->policyAbility(), $this->arguments())
            && $user->tokenCan($this->tokenAbility());
    }

    /**
     * The policy ability to authorize.
     *
     * @return string
     */
    abstract protected function policyAbility(): string;

    /**
     * The arguments for the policy ability to authorize.
     *
     * @return mixed
     */
    abstract protected function arguments(): mixed;

    /**
     * The token ability to authorize.
     *
     * @return string
     */
    abstract protected function tokenAbility(): string;

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        foreach ($this->enums() as $attribute => $enumClass) {
            $this->convertEnumDescriptionToValue($attribute, $enumClass);
        }
    }

    /**
     * The list of enum attributes to convert.
     *
     * @return array<string, class-string<BaseEnum>>
     */
    protected function enums(): array
    {
        return [];
    }

    /**
     * Convert enum description parameter value to enum value.
     *
     * @param  string  $attribute
     * @param  class-string<BaseEnum>  $enumClass
     * @return void
     */
    protected function convertEnumDescriptionToValue(string $attribute, string $enumClass): void
    {
        $description = $this->input($attribute);
        if (is_string($description)) {
            $enumInstance = $enumClass::fromDescription($description);
            if ($enumInstance !== null) {
                $this->merge([
                    $attribute => $enumInstance->value,
                ]);
            }
        }
    }
}
