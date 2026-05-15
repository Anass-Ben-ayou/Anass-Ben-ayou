<?php

namespace Database\Seeders;

use App\Models\SiteReview;
use Illuminate\Database\Seeder;

class SiteReviewSeeder extends Seeder
{
    // Seeds a few public testimonials for the contact page.
    public function run(): void
    {
        SiteReview::query()->delete();

        $reviews = [
            [
                'customer_name' => 'Nadia B.',
                'rating' => 5,
                'comment' => 'Tres belle experience. Les lampes sont elegantes, faciles a installer et rendent super bien sur la terrasse.',
                'review_date' => now()->subDays(12)->toDateString(),
                'is_approved' => true,
            ],
            [
                'customer_name' => 'Youssef A.',
                'rating' => 4,
                'comment' => 'Livraison rapide et produits conformes aux photos. J aime beaucoup la finition et l ambiance lumineuse.',
                'review_date' => now()->subDays(25)->toDateString(),
                'is_approved' => true,
            ],
            [
                'customer_name' => 'Salma E.',
                'rating' => 5,
                'comment' => 'Une boutique propre, moderne et rassurante. Le service client a ete reactif du debut a la fin.',
                'review_date' => now()->subDays(38)->toDateString(),
                'is_approved' => true,
            ],
        ];

        foreach ($reviews as $review) {
            SiteReview::create($review);
        }
    }
}
