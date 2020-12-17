<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityMst extends Model
{
    public $timestamps = false;
    protected $table = 'facility_mst';
    protected $fillable = [
        'facility_id',
        'facility_name',
        'create_date',
        'update_date',
        'delete_date',
    ];
}