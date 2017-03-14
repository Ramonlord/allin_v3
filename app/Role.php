<?php

namespace App;

class Role extends BaseModel
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function Users()
    {
        return $this->hasMany(User::class);
    }
}
