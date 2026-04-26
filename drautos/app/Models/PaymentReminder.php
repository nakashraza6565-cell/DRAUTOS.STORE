<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'party_type', 'party_id', 'reference_number', 'amount',
        'due_date', 'status', 'paid_amount', 'notes', 'whatsapp_sent', 'whatsapp_sent_at'
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'float',
        'paid_amount' => 'float',
        'whatsapp_sent' => 'boolean',
        'whatsapp_sent_at' => 'datetime',
    ];

    /**
     * Get the party (customer or supplier).
     */
    public function party()
    {
        return $this->morphTo();
    }

    /**
     * Check if payment is overdue
     */
    public function isOverdue()
    {
        return $this->due_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Get remaining amount
     */
    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->paid_amount;
    }

    /**
     * Scope for today's reminders
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today())
                     ->whereIn('status', ['pending', 'partially_paid']);
    }

    /**
     * Scope for overdue reminders
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', today())
                     ->whereIn('status', ['pending', 'partially_paid']);
    }
}
