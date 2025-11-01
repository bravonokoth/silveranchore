<?php
// app/Models/Notification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'type', 'notifiable_type', 'notifiable_id', 'data', 'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // Accessors
    public function getMessageAttribute()   { return $this->data['message'] ?? null; }
    public function getEmailAttribute()     { return $this->data['email'] ?? null; }
    public function getSessionIdAttribute() { return $this->data['session_id'] ?? null; }
    public function getOrderIdAttribute()   { return $this->data['order_id'] ?? null; }
    public function getIsGuestAttribute()   { return is_null($this->notifiable_id); }

    public function notifiable()
    {
        return $this->morphTo();
    }
}