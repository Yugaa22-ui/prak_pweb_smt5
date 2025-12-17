<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictModel extends Model
{
    protected $table = 'district';
    protected $primaryKey = 'district_id';

    protected $fillable = [
        'district_code', 'district_name', 'city_id'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
