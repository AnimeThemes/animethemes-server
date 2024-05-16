<?php

declare(strict_types=1);

namespace App\Models\Document;

use App\Events\Document\Page\PageCreated;
use App\Events\Document\Page\PageDeleted;
use App\Events\Document\Page\PageRestored;
use App\Events\Document\Page\PageUpdated;
use App\Models\BaseModel;
use Database\Factories\Document\PageFactory;
use Laravel\Nova\Actions\Actionable;

/**
 * Class Page.
 *
 * @property string $body
 * @property string $name
 * @property int $page_id
 * @property string $slug
 *
 * @method static PageFactory factory(...$parameters)
 */
class Page extends BaseModel
{
    use Actionable;

    final public const TABLE = 'pages';

    final public const ATTRIBUTE_BODY = 'body';
    final public const ATTRIBUTE_ID = 'page_id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_SLUG = 'slug';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        Page::ATTRIBUTE_BODY,
        Page::ATTRIBUTE_NAME,
        Page::ATTRIBUTE_SLUG,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        Page::ATTRIBUTE_BODY,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => PageCreated::class,
        'deleted' => PageDeleted::class,
        'restored' => PageRestored::class,
        'updated' => PageUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Page::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Page::ATTRIBUTE_ID;

    /**
     * Get the route key for the model.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return Page::ATTRIBUTE_SLUG;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->slug;
    }
}
