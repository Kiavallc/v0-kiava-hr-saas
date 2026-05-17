<?php

namespace Database\Seeders;

use App\Models\StripeProduct;
use App\Models\StripePrice;
use Illuminate\Database\Seeder;

class StripeProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Starter Plan',
                'stripe_id' => 'prod_starter_test',
                'description' => 'Up to 50 employees',
                'prices' => [
                    ['amount' => 4900, 'currency' => 'usd', 'billing_period' => 'monthly'],
                    ['amount' => 49000, 'currency' => 'usd', 'billing_period' => 'yearly'],
                ]
            ],
            [
                'name' => 'Professional Plan',
                'stripe_id' => 'prod_professional_test',
                'description' => 'Up to 500 employees',
                'prices' => [
                    ['amount' => 9900, 'currency' => 'usd', 'billing_period' => 'monthly'],
                    ['amount' => 99000, 'currency' => 'usd', 'billing_period' => 'yearly'],
                ]
            ],
            [
                'name' => 'Enterprise Plan',
                'stripe_id' => 'prod_enterprise_test',
                'description' => 'Unlimited employees',
                'prices' => [
                    ['amount' => 29900, 'currency' => 'usd', 'billing_period' => 'monthly'],
                    ['amount' => 299000, 'currency' => 'usd', 'billing_period' => 'yearly'],
                ]
            ],
        ];

        foreach ($products as $productData) {
            $product = StripeProduct::create([
                'name' => $productData['name'],
                'stripe_id' => $productData['stripe_id'],
                'description' => $productData['description'],
                'is_active' => true,
            ]);

            foreach ($productData['prices'] as $priceData) {
                StripePrice::create([
                    'stripe_product_id' => $product->id,
                    'amount' => $priceData['amount'],
                    'currency' => $priceData['currency'],
                    'billing_period' => $priceData['billing_period'],
                    'stripe_id' => 'price_' . strtolower(str_replace(' ', '_', $productData['name'])) . '_' . $priceData['billing_period'],
                    'trial_days' => 14,
                    'is_active' => true,
                ]);
            }
        }
    }
}
