<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUSES = ['pending', 'diproses', 'selesai', 'dibatalkan'];
    public const CHANNELS = ['dine_in', 'takeaway', 'online', 'frozen'];
    public const PAYMENTS = ['tunai', 'qris'];

    protected $fillable = [
        'invoice_no', 'user_id', 'channel', 'status', 'payment_method',
        'total', 'amount_paid', 'change_amount', 'note', 'completed_at',
    ];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
