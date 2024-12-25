<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobResponsibility extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'company_job_id'
    ];
}
