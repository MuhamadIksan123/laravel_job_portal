<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobCandidate extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'resume',
        'message',
        'is_hired',
        'candidate_id',
        'company_job_id',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_id', 'id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(CompanyJob::class, 'company_job_id', 'id');
    }
}
