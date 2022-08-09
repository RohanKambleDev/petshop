<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Facades\LcobucciJwtFacade as Jwt;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', 'id', 'is_admin', 'is_marketing'
    ];

    /**
     * The attributes that should be cast.
     * casting email_verified_at to datetime format
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Interact with the last_login_at
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->format('Y-m-d h:i:s')
        );
    }

    /**
     * Interact with the last_login_at
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->format('Y-m-d h:i:s')
        );
    }

    /**
     * Interact with the last_login_at
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function lastLoginAt(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Carbon::parse($value)->format('Y-m-d h:i:s')
        );
    }

    /**
     * crete a user
     *
     * @param  mixed $data
     * @return user obj
     */
    public function add($data)
    {
        $data['uuid'] = Str::orderedUuid(); // create UUID
        $data['password'] = bcrypt($data['password']); // hash the password
        return self::create($data);
    }

    /**
     * resetPassword
     *
     * @param  array $data having email & password
     * @return boolean
     */
    public function resetPassword($data)
    {
        $user = self::where('email', $data['email'])->first();
        if ($user === null) {
            return false;
        }
        $user->password = bcrypt($data['password']); // hash the password
        return $user->save();
    }

    /**
     * getUserByEmail
     *
     * @param  string $email
     * @return user obj
     */
    public function getUserByEmail($email)
    {
        if (empty($email)) {
            return null;
        }
        return self::where('email', $email)->first();
    }

    /**
     * getUserByUuid
     *
     * @param  mixed $uuid
     * @return mixed
     */
    public static function getUserByUuid($uuid)
    {
        if (empty($uuid)) {
            return null;
        }

        $user = self::where('uuid', $uuid)->get();
        if ($user->isNotEmpty()) {
            return $user->first();
        }

        return collect();
    }

    /**
     * getUserByEmailAndToken
     *
     * @param  mixed $email
     * @param  mixed $apiToken
     * @return boolean
     */
    public function getUserByEmailAndToken($email, $apiToken)
    {
        if (empty($email)) {
            return null;
        }
        if (empty($apiToken)) {
            return null;
        }

        $uuid = Jwt::getUserUuid($apiToken);
        return self::where('email', $email)->where('uuid', $uuid)->first();
    }

    /**
     * updateField
     *
     * @param  mixed $uuid
     * @param  mixed $column
     * @param  mixed $value
     * @return boolean
     */
    public function updateField($uuid, $column, $value)
    {
        return self::where('uuid', $uuid)->update([$column => $value]);
    }

    /**
     * updateFieldsInBulk
     *
     * @param  mixed $uuid
     * @param  mixed $data
     * @return boolean
     */
    public function updateFieldsInBulk($uuid, $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']); // hash the password
        }
        return self::where('uuid', $uuid)->update($data);
    }

    /**
     * updateDetails
     *
     * @param  User $user
     * @param  array $data
     * @return boolean
     */
    public function updateDetails(self $user, $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']); // hash the password
        }
        return $user->update($data);
    }

    /**
     * deleteRecord
     *
     * @param  mixed $uuid
     * @return boolean
     */
    public function deleteRecord($uuid)
    {
        return self::where('uuid', $uuid)->delete();
    }
}
