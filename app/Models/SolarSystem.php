<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SolarSystem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'difficulty',
        'available_resources',
        'max_players',
        'min_players',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'available_resources' => 'array',
        'is_active' => 'boolean',
    ];

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    public function activeGames(): HasMany
    {
        return $this->games()->whereIn('status', ['waiting', 'active', 'paused']);
    }

    public function getAvailableGamesCount(): int
    {
        return $this->games()->where('status', 'waiting')->where('is_public', true)->count();
    }

    public function getTotalPlayersCount(): int
    {
        return $this->games()
            ->whereIn('status', ['active', 'paused'])
            ->sum('current_players');
    }

    public function getDifficultyColorAttribute(): string
    {
        return match($this->difficulty) {
            'easy' => 'green',
            'medium' => 'yellow',
            'hard' => 'orange',
            'extreme' => 'red',
            default => 'gray',
        };
    }
}
