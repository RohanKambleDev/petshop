<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

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
        'id'
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
     * product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, 'category_uuid', 'uuid');
    }

    /**
     * get All Categories with 
     * products mapped
     *
     * @return array
     */
    public function getAllCategories()
    {
        $categories = self::all();
        $i = 0;
        foreach ($categories as $category) {
            $categoriesArr[$i] = $category->toArray();
            $categoriesArr[$i]['products'] = $category->getProducts->toArray();
            $i++;
        }
        return $categoriesArr;
    }
}
