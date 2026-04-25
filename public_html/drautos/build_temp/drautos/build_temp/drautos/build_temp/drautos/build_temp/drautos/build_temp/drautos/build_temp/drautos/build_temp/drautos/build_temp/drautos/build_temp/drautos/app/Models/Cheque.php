<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'cheque_number', 'amount', 'cheque_date', 'clearing_date',
        'party_type', 'party_id', 'bank_name', 'bank_branch', 'status',
        'actual_clearing_date', 'delay_days', 'notes', 'reference_number', 'created_by'
    ];

    protected $casts = [
        'amount' => 'float',
        'cheque_date' => 'date',
        'clearing_date' => 'date',
        'actual_clearing_date' => 'date',
        'delay_days' => 'integer',
    ];

    /**
     * Get the party (customer or supplier).
     */
    public function party()
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Calculate delay days
     */
    public function calculateDelay()
    {
        if ($this->actual_clearing_date && $this->clearing_date) {
            $this->delay_days = $this->clearing_date->diffInDays($this->actual_clearing_date);
            $this->save();
        }
    }

    /**
     * Scope for pending cheques
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for due today
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('clearing_date', today())
                     ->where('status', 'pending');
    }

    /**
     * Scope for overdue cheques
     */
    public function scopeOverdue($query)
    {
        return $query->where('clearing_date', '<', today())
                     ->where('status', 'pending');
    }
}
