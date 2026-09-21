<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_BUYER = 'buyer';

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'oauth_provider',
        'oauth_provider_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isBuyer(): bool
    {
        return $this->role === self::ROLE_BUYER;
    }
}
