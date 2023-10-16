<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovementHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['product_id', 'warehouse_id', 'old_count', 'new_count'];

    /**
     * Связь "belongsTo" с моделью Warehouse
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Связь "belongsTo" с моделью Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
