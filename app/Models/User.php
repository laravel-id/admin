<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Filament\Clusters\Settings\Enums\AvatarProvider;
use App\Models\Traits\HasLocalTime;
use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $local_created_at
 * @property string $local_updated_at
 * @property BelongsToMany $branches
 * @property Role $roles
 * @property string $email
 * @property bool $is_root
 * @property int $id
 */
class User extends Authenticatable implements FilamentUser, HasAvatar, HasTenants
{
    use HasFactory, Notifiable;
    use HasLocalTime;
    use HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // for root user without roles
        if ($this->roles->isEmpty()) {
            return true;
        }

        return $this->roles->contains('is_admin', true);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (empty($this->avatar)) {
            $provider = config('user.avatar_provider', AvatarProvider::UIAvatars->value);

            if ($provider !== AvatarProvider::UIAvatars->value) {
                $avatar = AvatarProvider::tryFrom($provider);

                if (! is_null($avatar)) {
                    return $avatar->getImageUrl($this);
                }
            }

            // null means using UI-avatars
            // handled by Filament
            return null;
        }

        return Storage::disk('public')->url($this->avatar);
    }

    public function getTenants(Panel $panel): array|Collection
    {
        return $this->branches;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->branches()
            ->wherePivot('branch_id', $tenant->id)
            ->exists();
    }

    public function isRoot(): Attribute
    {
        return new Attribute(
            get: fn (): bool => $this->roles->isEmpty(),
        );
    }

    public function roles(): MorphToMany
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }
}
