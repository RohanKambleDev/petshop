<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
        'password',
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

    public function add($data)
    {
        $data['uuid'] = Str::orderedUuid(); // create UUID
        $data['password'] = bcrypt($data['password']); // hash the password
        return self::create($data);
    }

    public function getToken()
    {
        return Str::orderedUuid(); // temp token
    }

    public function resetPassword($data)
    {
        $user = self::where('email', $data['email'])->first();
        if ($user === null) {
            return false;
        }
        $user->password = bcrypt($data['password']); // hash the password
        return $user->save();
    }
}
