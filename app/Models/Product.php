<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'producer_id',
        'category_id',
        'name',
        'description',
        'price',
        'unit',
        'photo',
        'is_available',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Gera o slug automaticamente quando ainda não há um definido.
     */
    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = static::generateSlug($product->name);
            }
        });
    }

    protected static function generateSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function producer(): BelongsTo
    {
        return $this->belongsTo(Producer::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    /**
     * URL da foto: aceita tanto uploads no storage quanto links externos
     * (ex.: imagens do Unsplash usadas pelo seed de demonstração).
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::get(function () {
            if (empty($this->photo)) {
                return null;
            }

            return Str::startsWith($this->photo, ['http://', 'https://'])
                ? $this->photo
                : Storage::url($this->photo);
        });
    }
}
