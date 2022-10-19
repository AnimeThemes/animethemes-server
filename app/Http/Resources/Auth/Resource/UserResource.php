<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Resources\BaseResource;
use App\Models\Auth\User;
use App\Models\BaseModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class UserResource.
 *
 * @mixin User
 */
class UserResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'user';

    /**
     * Create a new resource instance.
     *
     * @param  User | MissingValue | null  $user
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(User|MissingValue|null $user, ReadQuery $query)
    {
        parent::__construct($user, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        $result = [];

        if ($this->isAllowedField(BaseResource::ATTRIBUTE_ID)) {
            $result[BaseResource::ATTRIBUTE_ID] = $this->getKey();
        }

        if ($this->isAllowedField(User::ATTRIBUTE_NAME)) {
            $result[User::ATTRIBUTE_NAME] = $this->name;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT)) {
            $result[BaseModel::ATTRIBUTE_CREATED_AT] = $this->created_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT)) {
            $result[BaseModel::ATTRIBUTE_UPDATED_AT] = $this->updated_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT)) {
            $result[BaseModel::ATTRIBUTE_DELETED_AT] = $this->deleted_at;
        }

        return $result;
    }
}
