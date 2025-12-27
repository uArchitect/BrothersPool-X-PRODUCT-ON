<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'table_id',
        'employee_id',
        'start_date',
        'end_date',
        'color',
        'status',
        'party_size',
        'special_requests',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function reservationItems()
    {
        return $this->hasMany(\App\Models\ReservationItem::class);
    }
} 