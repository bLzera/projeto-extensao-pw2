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
                    ['name' => 'Alface Crespa', 'category' => 'Verduras', 'price' => 3.50, 'unit' => 'unidade', 'description' => 'Alface fresca, colhida no dia.'],
                    ['name' => 'Tomate Cereja', 'category' => 'Legumes', 'price' => 8.00, 'unit' => 'kg', 'description' => 'Tomate cereja orgânico, sabor adocicado.'],
                    ['name' => 'Banana Prata', 'category' => 'Frutas', 'price' => 6.00, 'unit' => 'kg', 'description' => 'Banana prata madura, diretamente do pé.'],
                    ['name' => 'Abóbora Cabotiá', 'category' => 'Legumes', 'price' => 5.50, 'unit' => 'kg', 'description' => 'Abóbora japonesa doce e cremosa.'],
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
                    ['name' => 'Ovos Caipiras', 'category' => 'Ovos', 'price' => 18.00, 'unit' => 'dúzia', 'description' => 'Ovos de galinhas criadas soltas, com gema amarelo intenso.'],
                    ['name' => 'Queijo Colonial', 'category' => 'Laticínios', 'price' => 45.00, 'unit' => 'kg', 'description' => 'Queijo meia-cura feito com leite cru da propriedade.'],
                    ['name' => 'Iogurte Natural', 'category' => 'Laticínios', 'price' => 12.00, 'unit' => 'litro', 'description' => 'Iogurte integral sem adição de açúcar.'],
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
                    ['name' => 'Mel Silvestre', 'category' => 'Mel e derivados', 'price' => 35.00, 'unit' => 'kg', 'description' => 'Mel puro de abelhas nativas, colhido sem aquecimento.'],
                    ['name' => 'Própolis Verde', 'category' => 'Mel e derivados', 'price' => 28.00, 'unit' => 'unidade', 'description' => 'Extrato alcoólico de própolis 11% — 30 mL.'],
                    ['name' => 'Geleia de Maracujá', 'category' => 'Conservas', 'price' => 14.00, 'unit' => 'unidade', 'description' => 'Geleia artesanal de maracujá com pedaços, 250 g.'],
                    ['name' => 'Arroz Agulhinha', 'category' => 'Grãos e cereais', 'price' => 9.00, 'unit' => 'kg', 'description' => 'Arroz branco tipo 1, produzido localmente.'],
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
                    'is_available' => true,
                ]);
            }
        }
    }
}
