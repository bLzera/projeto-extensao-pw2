<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    protected $fillable = ['buyer_id', 'producer_id', 'stars', 'comment', 'hidden', 'status', 'edited_at'];

    protected function casts(): array
    {
        return [
            'stars'     => 'integer',
            'hidden'    => 'boolean',
            'edited_at' => 'datetime',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function producer(): BelongsTo
    {
        return $this->belongsTo(Producer::class);
    }
}
