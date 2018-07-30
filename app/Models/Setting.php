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
class Setting extends Model
{
    /**
     * @return Query
     */
    public $timestamps = false;
    protected $fillable = ['level1', 'level2'];

}
