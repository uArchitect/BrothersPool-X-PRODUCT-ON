<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_number',
        'table_type',
        'is_reservable',
        'is_smoking_allowed',
        'features',
        'location_x',
        'location_y',
        'capacity',
        'location',
        'status',
        'employee_id',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_reservable' => 'boolean',
        'is_smoking_allowed' => 'boolean',
        'features' => 'array',
        'location_x' => 'decimal:2',
        'location_y' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
