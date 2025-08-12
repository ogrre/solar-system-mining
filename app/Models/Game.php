<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'solar_system_id',
        'host_user_id',
        'name',
        'description',
        'status',
        'current_players',
        'game_settings',
        'game_state',
        'started_at',
        'ended_at',
        'last_activity_at',
        'is_public',
        'join_code',
    ];

    protected $casts = [
        'game_settings' => 'array',
        'game_state' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    protected $appends = ['status_color'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($game): void {
            if (empty($game->join_code)) {
                $game->join_code = strtoupper(Str::random(8));
            }
        });
    }

    public function solarSystem(): BelongsTo
    {
        return $this->belongsTo(SolarSystem::class);
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }

    public function gamePlayers(): HasMany
    {
        return $this->hasMany(GamePlayer::class);
    }

    public function activePlayers(): HasMany
    {
        return $this->gamePlayers()->where('status', 'joined');
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'game_players')
            ->withPivot(['status', 'player_data', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(GameInvitation::class);
    }

    public function canJoin(User $user): bool
    {
        if ($this->status !== 'waiting') {
            return false;
        }

        if ($this->current_players >= $this->solarSystem->max_players) {
            return false;
        }

        if ($this->gamePlayers()->where('user_id', $user->id)->where('status', 'joined')->exists()) {
            return false;
        }

        return true;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'waiting' => 'blue',
            'active' => 'green',
            'paused' => 'yellow',
            'completed' => 'gray',
            'abandoned' => 'red',
            default => 'gray',
        };
    }
}
