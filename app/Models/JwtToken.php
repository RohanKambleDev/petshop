<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Str;
use App\Services\Auth\LcobucciJWT;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
     * create new entry
     *
     * @param  mixed $data
     * @return model obj
     */
    public function add($token, $user)
    {
        $lcobucciJwt = new LcobucciJWT;
        $parsedToken = $lcobucciJwt->getParsedToken($token);

        $data = [
            'unique_id'   => Str::orderedUuid(), // create UUID
            'user_id'     => $user->uuid,
            'token_title' => $parsedToken->claims()->get('jti'),
            // 'restrictions' => ,
            // 'permissions' => ,
            'expires_at' => $parsedToken->claims()->get('exp')->format('Y-m-d h:i:s'),
            // 'last_used_at' => ,
            // 'refreshed_at' => ,
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
        $lcobucciJwt = new LcobucciJWT;
        $parsedToken = $lcobucciJwt->getParsedToken($token);
        $uuid = $parsedToken->claims()->get('jti');
        return self::where('user_id', $uuid)->delete();
    }
}
