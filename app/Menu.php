<?php

namespace App;

class Menu extends BaseModel
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function getStatusAttribute()
    {
        return $this->attributes['status'] ? 'active' : 'inactive';
    }
}
