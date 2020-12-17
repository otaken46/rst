<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityManagerMst extends Model
{
    public $timestamps = false;
    protected $table = 'facility_manager_mst';
    protected $fillable = [
        'facility_id',
        'facility_manager_name',
        'facility_manager_id',
        'password',
        'contact',
        'mail_address',
        'create_date',
        'update_date',
        'delete_date',
    ];
}