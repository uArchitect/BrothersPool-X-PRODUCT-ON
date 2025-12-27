<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'position',
        'salary',
        'hire_date',
        'avatar',
        'role',
        'hourly_wage',
        'monthly_salary',
        'experience_years',
        'table_id',
        'is_active'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'is_active' => 'boolean',
        'salary' => 'decimal:2',
        'hourly_wage' => 'decimal:2',
        'monthly_salary' => 'decimal:2',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'waiter_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'seller_id');
    }
}
