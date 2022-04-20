<?php

namespace Tests;

use Actengage\Media\Mediable;
use Illuminate\Foundation\Auth\User as Model;

class User extends Model
{
    use Mediable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];
}