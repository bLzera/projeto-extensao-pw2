<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Producer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Monta a URL de uma foto do Unsplash dimensionada para o catálogo.
     */
    private function unsplash(string $id): string
    {
        return "https://images.unsplash.com/photo-{$id}?auto=format&fit=crop&w=800&q=80";
    }

    public function run(): void
    {
        $producers = [
            [
                'user' => [
                    'name'              => 'Ana Souza',
                    'email'             => 'ana@feirafeira.test',
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
                'producer' => [
                    'farm_name'     => 'Sítio Boa Vista',
                    'city'          => 'Rio do Sul',
                    'description'   => 'Produção orgânica de frutas e verduras no vale do Itajaí.',
                    'phone'         => '(47) 99100-0001',
                    'whatsapp'      => '47991000001',
                    'contact_email' => 'ana@feirafeira.test',
                ],
                'products' => [
                    ['name' => 'Alface Crespa', 'category' => 'Verduras', 'price' => 3.50, 'unit' => 'unidade', 'description' => 'Alface fresca, colhida no dia.', 'photo' => $this->unsplash('1622206151226-18ca2c9ab4a1')],
                    ['name' => 'Tomate Cereja', 'category' => 'Legumes', 'price' => 8.00, 'unit' => 'kg', 'description' => 'Tomate cereja orgânico, sabor adocicado.', 'photo' => $this->unsplash('1561136594-7f68413baa99')],
                    ['name' => 'Banana Prata', 'category' => 'Frutas', 'price' => 6.00, 'unit' => 'kg', 'description' => 'Banana prata madura, diretamente do pé.', 'photo' => $this->unsplash('1571771894821-ce9b6c11b08e')],
                    ['name' => 'Abóbora Cabotiá', 'category' => 'Legumes', 'price' => 5.50, 'unit' => 'kg', 'description' => 'Abóbora japonesa doce e cremosa.', 'photo' => $this->unsplash('1506917728037-b6af01a7d403')],
                ],
            ],
            [
                'user' => [
                    'name'              => 'Carlos Meier',
                    'email'             => 'carlos@feirafeira.test',
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
                'producer' => [
                    'farm_name'     => 'Granja Meier',
                    'city'          => 'Taió',
                    'description'   => 'Ovos caipiras e laticínios artesanais produzidos com cuidado.',
                    'phone'         => '(47) 99100-0002',
                    'whatsapp'      => '47991000002',
                    'contact_email' => 'carlos@feirafeira.test',
                ],
                'products' => [
                    ['name' => 'Ovos Caipiras', 'category' => 'Ovos', 'price' => 18.00, 'unit' => 'dúzia', 'description' => 'Ovos de galinhas criadas soltas, com gema amarelo intenso.', 'photo' => $this->unsplash('1582722872445-44dc5f7e3c8f')],
                    ['name' => 'Queijo Colonial', 'category' => 'Laticínios', 'price' => 45.00, 'unit' => 'kg', 'description' => 'Queijo meia-cura feito com leite cru da propriedade.', 'photo' => $this->unsplash('1486297678162-eb2a19b0a32d')],
                    ['name' => 'Iogurte Natural', 'category' => 'Laticínios', 'price' => 12.00, 'unit' => 'litro', 'description' => 'Iogurte integral sem adição de açúcar.', 'photo' => $this->unsplash('1488477181946-6428a0291777')],
                ],
            ],
            [
                'user' => [
                    'name'              => 'Joana Ritter',
                    'email'             => 'joana@feirafeira.test',
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
                'producer' => [
                    'farm_name'     => 'Apiário Flor do Campo',
                    'city'          => 'Ibirama',
                    'description'   => 'Mel puro e produtos apícolas da Serra Catarinense.',
                    'phone'         => '(47) 99100-0003',
                    'whatsapp'      => '47991000003',
                    'contact_email' => 'joana@feirafeira.test',
                ],
                'products' => [
                    ['name' => 'Mel Silvestre', 'category' => 'Mel e derivados', 'price' => 35.00, 'unit' => 'kg', 'description' => 'Mel puro de abelhas nativas, colhido sem aquecimento.', 'photo' => $this->unsplash('1558642452-9d2a7deb7f62')],
                    ['name' => 'Própolis Verde', 'category' => 'Mel e derivados', 'price' => 28.00, 'unit' => 'unidade', 'description' => 'Extrato alcoólico de própolis 11% — 30 mL.', 'photo' => $this->unsplash('1474979266404-7eaacbcd87c5')],
                    ['name' => 'Geleia de Maracujá', 'category' => 'Conservas', 'price' => 14.00, 'unit' => 'unidade', 'description' => 'Geleia artesanal de maracujá com pedaços, 250 g.', 'photo' => $this->unsplash('1472476443507-c7a5948772fc')],
                    ['name' => 'Arroz Agulhinha', 'category' => 'Grãos e cereais', 'price' => 9.00, 'unit' => 'kg', 'description' => 'Arroz branco tipo 1, produzido localmente.', 'photo' => $this->unsplash('1586201375761-83865001e31c')],
                ],
            ],
        ];

        foreach ($producers as $data) {
            $user = User::create($data['user']);
            $producer = Producer::create(array_merge($data['producer'], ['user_id' => $user->id]));

            foreach ($data['products'] as $productData) {
                $category = Category::where('name', $productData['category'])->first();
                Product::create([
                    'producer_id'  => $producer->id,
                    'category_id'  => $category->id,
                    'name'         => $productData['name'],
                    'description'  => $productData['description'],
                    'price'        => $productData['price'],
                    'unit'         => $productData['unit'],
                    'photo'        => $productData['photo'] ?? null,
                    'is_available' => true,
                ]);
            }
        }
    }
}
