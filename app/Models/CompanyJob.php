<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyJob extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'type',
        'location',
        'skill_level',
        'salary',
        'thumbnail',
        'about',
        'is_open',
        'company_id',
        'category_id'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function responsibilities(): HasMany
    {
        return $this->hasMany(JobResponsibility::class);
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(JobQualification::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(JobCandidate::class);
    }
}
