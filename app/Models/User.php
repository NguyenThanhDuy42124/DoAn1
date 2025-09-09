<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'email', 'password', 'role'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class, 'buyer_id');
    }
    public function wishlist()
    {
        return $this->hasOne(Wishlist::class, 'buyer_id');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }


    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    public function isSeller()
    {
        return $this->role === 'seller';
    }
    public function isBuyer()
    {
        return $this->role === 'buyer';
    }

}
