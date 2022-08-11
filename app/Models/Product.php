<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

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
     * category
     * Assuming a product will have only one category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getCategory()
    {
        return $this->belongsTo(Category::class, 'category_uuid', 'uuid');
    }

    /**
     * get All Products with their categories
     *
     * @return array
     */
    public function getAllProducts()
    {
        $products = self::all();
        $productsArr = [];
        if ($products->count()) {
            $i = 0;
            foreach ($products as $product) {
                $productsArr[$i] = $product->toArray();
                $productsArr[$i]['category'] = $product->getCategory->toArray();
                $i++;
            }
        }
        return $productsArr;
    }

    /**
     * add/create product
     *
     * @param  mixed $data
     * @return Product model
     */
    public function add($data)
    {
        $data['uuid'] = Str::orderedUuid(); // create UUID
        $data['metadata'] = json_encode($data['metadata']);
        return self::create($data);
    }

    /**
     * show
     *
     * @param  mixed $uuid
     * @return Product model
     */
    public function show($uuid)
    {
        return self::where('uuid', $uuid)->get();
    }

    /**
     * updateDetails
     *
     * @param  mixed $uuid
     * @param  mixed $data
     * @return boolean
     */
    public function updateDetails($uuid, $data)
    {
        $data['metadata'] = json_encode($data['metadata']);
        return self::where('uuid', $uuid)->update($data);
    }

    /**
     * remove
     *
     * @param  mixed $uuid
     * @return boolean
     */
    public function remove($uuid)
    {
        return self::where('uuid', $uuid)->delete();
    }
}
