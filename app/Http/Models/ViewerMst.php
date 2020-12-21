<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ViewerMst extends Model
{
    public $timestamps = false;
    protected $table = 'viewer_mst';
    protected $fillable = [
        'facility_id',
        'viewer_name',
        'viewer_id',
        'password',
        'mail_address',
        'create_date',
        'update_date',
        'delete_date',
    ];
}