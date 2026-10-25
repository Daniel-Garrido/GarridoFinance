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
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'password',
    ];

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

    // Nombre y apellidos juntos
    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . $this->last_name);
    }

    // Iniciales para el avatar: primera letra del nombre y del primer apellido
    public function getInitialsAttribute(): string
    {
        $initials = collect(explode(' ', $this->full_name))
            ->filter()
            ->take(2)
            ->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');

        return $initials ?: 'GF';
    }

    // URL pública de la foto de perfil, o null si no tiene.
    // asset() usa el host y puerto con los que se abrió la app (no APP_URL),
    // así la foto se ve igual con artisan serve, 127.0.0.1 o Apache
    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : null;
    }

    public function accounts(){
        return $this->hasMany(\App\Models\Account::class);
    }

    public function categories(){
        return $this->hasMany(\App\Models\Category::class);
    }

    public function paymentMethods(){
        return $this->hasMany(\App\Models\PaymentMethod::class);
    }

    public function transactions(){
        return $this->hasMany(\App\Models\Transaction::class);
    }

    public function transfers(){
        return $this->hasMany(\App\Models\Transfer::class);
    }
}
