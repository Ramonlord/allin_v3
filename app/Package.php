<?php

namespace App;

/**
 * @property mixed name
 * @property array|int cost
 * @property array|string cost_per
 * @property array|string plan
 * @property array|bool status
 * @property array|bool featured
 * @property array|int pricing_order
 */
class Package extends BaseModel
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('status', 1)->orderBy('pricing_order');
    }

    public function getStatusAttribute()
    {
        return $this->attributes['status'] ? 'active' : 'inactive';
    }

    public function getFeaturedAttribute()
    {
        return $this->attributes['featured'] ? 'featured' : 'normal';
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class)->withPivot('spec')->withTimestamps();
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
