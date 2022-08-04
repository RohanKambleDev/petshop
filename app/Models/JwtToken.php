<?php

namespace App\Models;

use Carbon\Carbon;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Facades\LcobucciJwtFacade as Jwt;

class JwtToken extends Model
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jwt_tokens';

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
     * The attributes that should be cast.
     * casting email_verified_at to datetime format
     *
     * @var array<string, string>
     */
    // protected $casts = [
    //     'expires_at'   => 'datetime:Y-m-d h:i:s',
    //     'last_used_at' => 'datetime:Y-m-d h:i:s',
    //     'refreshed_at' => 'datetime:Y-m-d h:i:s',
    // ];

    /**
     * Interact with the expires_at
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function expiresAt(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Carbon::parse($value)->format('Y-m-d h:i:s')
        );
    }

    /**
     * Interact with the last_used_at
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function lastUsedAt(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Carbon::parse($value)->format('Y-m-d h:i:s')
        );
    }

    /**
     * Interact with the refreshed_at
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function refreshedAt(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Carbon::parse($value)->format('Y-m-d h:i:s')
        );
    }

    /**
     * create new entry
     *
     * @param  mixed $data
     * @return model obj
     */
    public function add($token, $user)
    {
        $parsedToken = Jwt::getParsedToken($token);

        $data = [
            'unique_id'   => Str::orderedUuid(), // create UUID
            'user_id'     => $user->uuid,
            'token_title' => $parsedToken->claims()->get('jti'),
            // 'restrictions' => ,
            // 'permissions' => ,
            'expires_at'   => $parsedToken->claims()->get('exp'),
            'last_used_at' => Carbon::now(),
            'refreshed_at' => Carbon::now(),
        ];
        return self::create($data);
    }

    public function modify()
    {
    }

    public function getRecordByUser($uuid)
    {
        return self::where('user_id', $uuid)
            ->where('expires_at', '>', Carbon::now()->format('Y-m-d h:i:s'))
            ->get()
            ->last();
    }

    public function removeJwtToken($token)
    {
        $uuid = Jwt::getUserUuid($token);
        return self::where('user_id', $uuid)->delete();
    }
}
