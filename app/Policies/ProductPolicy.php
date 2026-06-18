<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

// Regra de negócio: cada produtor só pode modificar seus próprios produtos.
// O operador ?-> protege o caso em que o usuário ainda não tem perfil de produtor.
class ProductPolicy
{
    /**
     * Permite edição apenas ao produtor dono do produto.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->producer?->id === $product->producer_id;
    }

    /**
     * Permite exclusão apenas ao produtor dono do produto.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->producer?->id === $product->producer_id;
    }
}
