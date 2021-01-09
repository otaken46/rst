<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class OperationLog extends Model
{
    public $timestamps = false;
    protected $table = 'operation_log';
}