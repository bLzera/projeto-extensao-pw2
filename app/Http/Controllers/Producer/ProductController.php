<?php

namespace App\Http\Controllers\Producer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Producer\StoreProductRequest;
use App\Http\Requests\Producer\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('dashboard.products.form', [
            'product'    => null,
            'categories' => $categories,
            'action'     => route('producer.products.store'),
            'method'     => 'POST',
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $producer = auth()->user()->producer;

        $path = $request->file('photo')->storeAs(
            'products',
            Str::uuid() . '.' . $request->file('photo')->extension(),
            'public'
        );

        $producer->products()->create([
            ...$request->safe()->except('photo'),
            'photo' => $path,
        ]);

        return redirect()->route('dashboard')->with('success', 'Produto cadastrado com sucesso!');
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        $categories = Category::orderBy('name')->get();

        return view('dashboard.products.form', [
            'product'    => $product,
            'categories' => $categories,
            'action'     => route('producer.products.update', $product),
            'method'     => 'PUT',
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        $data = $request->safe()->except('photo');

        if ($request->hasFile('photo')) {
            Storage::disk('public')->delete($product->photo);
            $data['photo'] = $request->file('photo')->storeAs(
                'products',
                Str::uuid() . '.' . $request->file('photo')->extension(),
                'public'
            );
        }

        $product->update($data);

        return redirect()->route('dashboard')->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        Storage::disk('public')->delete($product->photo);
        $product->delete();

        return redirect()->route('dashboard')->with('success', 'Produto removido com sucesso!');
    }

    public function toggleAvailability(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $product->update(['is_available' => !$product->is_available]);

        if($request->expectsJson()){
            return response()->json([
                'is_available' => $product->is_available,
                'available_count' => $product->producer->products()->where('is_available', true)->count(),
                'message' => $product->is_available ? 'Produto disponível' : 'Produto indisponível',
            ]);
        }        

        return redirect()->route('dashboard');
    }

    public function toggleFeatured(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $product);

        $producer = auth()->user()->producer;

        // Regra de negócio: no máximo 3 produtos em destaque por produtor.
        if (! $product->is_featured) {
            $featuredCount = $producer->products()->where('is_featured', true)->count();
            if ($featuredCount >= 3) {
                if($request->expectsJson()){
                    return response()->json([
                        'is_featured' => $product->is_featured,
                        'message' => 'Você já tem 3 produtos em destaque. Remova um antes de adicionar o outro.',
                    ]);
                };

                return redirect()->route('dashboard')
                    ->with('error', 'Você já tem 3 produtos em destaque. Remova um antes de adicionar outro.');
            }
        }

        $product->update(['is_featured' => ! $product->is_featured]);

        if($request->expectsJson()){
            return response()->json([
                'is_featured' => $product->is_featured,
                'message' => $product->is_featured ? 'Produto destacado' : 'Produto não destacado',
            ]);
        }

        return redirect()->route('dashboard');
    }
}
