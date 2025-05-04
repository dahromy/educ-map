<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     description="User model"
 * )
 */
class User
{
    /**
     * @OA\Property(
     *     property="id",
     *     type="integer",
     *     example=1,
     *     description="User ID"
     * )
     */
    public $id;

    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     example="John Doe",
     *     description="User's name"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="email",
     *     type="string",
     *     format="email",
     *     example="john@example.com",
     *     description="User's email address"
     * )
     */
    public $email;

    /**
     * @OA\Property(
     *     property="roles",
     *     type="array",
     *     description="User roles",
     *     @OA\Items(type="string", enum={"ROLE_USER", "ROLE_ADMIN", "ROLE_ESTABLISHMENT"}, example="ROLE_USER")
     * )
     */
    public $roles;

    /**
     * @OA\Property(
     *     property="associated_establishment",
     *     type="integer",
     *     nullable=true,
     *     example=null,
     *     description="ID of associated establishment (for ROLE_ESTABLISHMENT users)"
     * )
     */
    public $associated_establishment;

    /**
     * @OA\Property(
     *     property="created_at",
     *     type="string",
     *     format="date-time",
     *     example="2023-05-02T12:34:56Z",
     *     description="Creation timestamp"
     * )
     */
    public $created_at;

    /**
     * @OA\Property(
     *     property="updated_at",
     *     type="string",
     *     format="date-time",
     *     example="2023-05-02T12:34:56Z",
     *     description="Last update timestamp"
     * )
     */
    public $updated_at;
}
