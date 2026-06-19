<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Producer extends Model
{
    protected $fillable = [
        'user_id',
        'farm_name',
        'description',
        'city',
        'phone',
        'whatsapp',
        'contact_email',
        'photo',
    ];

    /**
     * Gera o slug automaticamente quando ainda não há um definido.
     */
    protected static function booted(): void
    {
        static::saving(function (Producer $producer) {
            if (empty($producer->slug)) {
                $producer->slug = static::generateSlug($producer->farm_name);
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

    /**
     * Monta um link wa.me com mensagem pré-preenchida, contextual ao produto
     * quando informado. Retorna null se o produtor não tem WhatsApp cadastrado.
     */
    public function whatsappUrl(?string $productName = null): ?string
    {
        if (! $this->whatsapp) {
            return null;
        }

        $number  = preg_replace('/\D/', '', $this->whatsapp);
        $message = $productName
            ? "Olá! Vi o produto \"{$productName}\" na Feira Digital e tenho interesse. Pode me dar mais informações?"
            : 'Olá! Encontrei sua loja na Feira Digital e gostaria de mais informações.';

        return "https://wa.me/55{$number}?text=" . urlencode($message);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
