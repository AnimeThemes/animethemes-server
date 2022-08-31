<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;

/**
 * Class ResourceLinkFormatRule.
 */
class ResourceLinkFormatRule implements DataAwareRule, Rule
{
    /**
     * The data under validation.
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Create a new rule instance.
     *
     * @param  ResourceSite|null  $site
     */
    public function __construct(protected ?ResourceSite $site = null)
    {
    }

    /**
     * Resolve the site.
     *
     * @return ResourceSite|null
     */
    protected function site(): ?ResourceSite
    {
        if ($this->site !== null) {
            return $this->site;
        }

        $site = Arr::get($this->data, 'site');
        if (is_numeric($site)) {
            return ResourceSite::fromValue(intval($site));
        }

        return null;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $site = $this->site();
        if ($site === null) {
            return true;
        }

        $anime = new AnimeResourceLinkFormatRule($site);
        $artist = new ArtistResourceLinkFormatRule($site);
        $studio = new StudioResourceLinkFormatRule($site);

        return $anime->passes($attribute, $value)
            || $artist->passes($attribute, $value)
            || $studio->passes($attribute, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): string|array
    {
        return __('validation.regex');
    }
}
