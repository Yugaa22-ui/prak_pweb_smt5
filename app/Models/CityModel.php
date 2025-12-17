<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityModel extends Model
{
    protected $table = 'city';
    protected $primaryKey = 'city_id';

    protected $fillable = [
        'city_code', 'city_name', 'province_id'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
