<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'broker_title',
    'email',
    'password',
    'is_admin',
    'phone',
    'whatsapp',
    'creci',
    'photo_path',
    'broker_bio',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_admin' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function brokerProperties(): HasMany
    {
        return $this->hasMany(Property::class, 'broker_user_id');
    }

    protected function brokerDisplayTitle(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->broker_title ?: 'Corretor especialista'
        );
    }

    protected function whatsappUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                $digits = preg_replace('/\D+/', '', (string) $this->whatsapp);

                if (! $digits) {
                    return null;
                }

                if (! Str::startsWith($digits, '55')) {
                    $digits = '55'.$digits;
                }

                return 'https://wa.me/'.$digits;
            }
        );
    }
}
