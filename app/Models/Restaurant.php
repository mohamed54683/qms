<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'brand',
        'type', // keeping for backward compatibility
        'status',
        'address',
        'city',
        'location', // keeping for backward compatibility
        'latitude',
        'longitude',
        'phone',
        'email',
        'manager',
        'manager_id', // keeping for backward compatibility
        'restaurant_manager_id',
        'area_manager_id',
        'opening_hours',
        'seating_capacity',
        'staff_count',
        'notes',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function restaurantManager()
    {
        return $this->belongsTo(User::class, 'restaurant_manager_id');
    }

    public function areaManager()
    {
        return $this->belongsTo(User::class, 'area_manager_id');
    }

    /**
     * Calculate distance between restaurant and given coordinates using Haversine formula
     * @param float $lat User's latitude
     * @param float $lng User's longitude
     * @return float Distance in meters
     */
    public function calculateDistance($lat, $lng)
    {
        if (!$this->latitude || !$this->longitude) {
            return null; // Cannot calculate distance without restaurant coordinates
        }

        $earthRadius = 6371000; // Earth's radius in meters

        $dLat = deg2rad($lat - $this->latitude);
        $dLng = deg2rad($lng - $this->longitude);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($this->latitude)) * cos(deg2rad($lat)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in meters
    }

    /**
     * Check if given coordinates are within specified distance of restaurant
     * @param float $lat User's latitude
     * @param float $lng User's longitude
     * @param int $maxDistance Maximum allowed distance in meters (default: 100)
     * @return bool
     */
    public function isWithinRange($lat, $lng, $maxDistance = 100)
    {
        $distance = $this->calculateDistance($lat, $lng);
        
        if ($distance === null) {
            return false; // Cannot validate without restaurant coordinates
        }

        return $distance <= $maxDistance;
    }
}
