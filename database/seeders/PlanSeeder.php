<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'           => 'Free',
                'price'          => 0.00,
                'billing_period' => 'monthly',
                'features'       => ['3 quizzes / month', 'Basic feedback', 'Community support'],
                'sort_order'     => 1,
            ],
            [
                'name'           => 'Starter',
                'price'          => 9.00,
                'billing_period' => 'monthly',
                'features'       => ['20 quizzes / month', 'AI-personalised feedback', 'Email support'],
                'sort_order'     => 2,
            ],
            [
                'name'           => 'Pro',
                'price'          => 19.00,
                'billing_period' => 'monthly',
                'features'       => ['Unlimited quizzes', 'Priority AI queue', 'Weak-area analytics', 'Email + chat support'],
                'sort_order'     => 3,
            ],
            [
                'name'           => 'Institution',
                'price'          => 49.00,
                'billing_period' => 'monthly',
                'features'       => ['Unlimited everything', 'Bulk teacher onboarding', 'API access', 'Dedicated support'],
                'sort_order'     => 4,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
}
