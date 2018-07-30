<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use MongoDB\Driver\Query;

/**
 * Class Product
 * @package App\Models
 * @property integer  id
 * @property string   machine_name
 * @property string   title
 * @property float    initial_price
 * @property float    renew_price
 * @property integer  renew_period
 * @property string   currency
 * @property integer  sort
 * @property string   description
 * @property boolean  available
 * @property DateTime created_at
 * @property DateTime updated_at
 */
class Product extends Model {
    /**
     * @return Query
     */
    protected $fillable = ['title', 'description', 'initial_price', 'renew_price','renew_period', 'sort'];

    public static function findAvailable() {
        return self::where( [ 'available' => true ] );
    }
}
