<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use App\Models\SiteReview;
use App\Support\SanitizesInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiteReviewController extends Controller
{
    // Returns every testimonial for the admin dashboard, including approval state.
    public function adminIndex()
    {
        $reviews = SiteReview::query()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (SiteReview $review) => $this->formatReview($review));

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    // Returns the public testimonials shown on the contact page.
    public function index()
    {
        $siteReviews = SiteReview::query()
            ->where('is_approved', true)
            ->latest('review_date')
            ->latest()
            ->get()
            ->map(fn (SiteReview $review) => $this->formatReview($review));

        $productReviews = Avis::with(['client', 'produit'])
            ->latest('date_avis')
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn (Avis $review) => $this->formatProductReview($review));

        $reviews = collect($siteReviews->all())
            ->merge($productReviews)
            ->sortByDesc('created_at')
            ->values();

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    // Stores a new public testimonial submission.
    public function store(Request $request)
    {
        $request->merge([
            'customer_name' => SanitizesInput::plain($request->input('customer_name'), 255),
            'email' => $request->filled('email') ? SanitizesInput::email($request->input('email')) : null,
            'comment' => SanitizesInput::paragraph($request->input('comment'), 1500),
        ]);

        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'comment' => 'required|string|min:10|max:1500',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $review = SiteReview::create([
            ...$validator->validated(),
            'review_date' => now()->toDateString(),
            'is_approved' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Merci pour votre avis. Il sera publie apres verification.',
            'data' => [
                'id' => $review->id_site_review,
                'customer_name' => $review->customer_name,
                'email' => $review->email,
                'comment' => $review->comment,
                'rating' => $review->rating,
                'date' => $review->review_date?->format('Y-m-d'),
                'is_approved' => $review->is_approved,
            ],
        ], 201);
    }

    protected function formatReview(SiteReview $review): array
    {
        return [
            'id' => $review->id_site_review,
            'customer_name' => SanitizesInput::plain($review->customer_name, 255),
            'email' => $review->email ? SanitizesInput::email($review->email) : null,
            'comment' => SanitizesInput::paragraph($review->comment, 1500),
            'rating' => $review->rating,
            'date' => optional($review->review_date)->format('Y-m-d') ?: $review->created_at?->format('Y-m-d'),
            'is_approved' => $review->is_approved,
            'created_at' => $review->created_at,
        ];
    }

    protected function formatProductReview(Avis $review): array
    {
        $customerName = trim(($review->client?->prenom ?: '').' '.($review->client?->nom ?: ''));

        return [
            'id' => 'product-'.$review->id_avis,
            'source' => 'product',
            'product_id' => $review->id_produit,
            'product_name' => SanitizesInput::plain($review->produit?->nom ?: 'Produit achete', 255),
            'title' => SanitizesInput::plain($review->produit?->nom ?: 'Avis produit', 255),
            'customer_name' => SanitizesInput::plain($customerName ?: ($review->client?->email ?: 'Client SolarLight'), 255),
            'email' => $review->client?->email ? SanitizesInput::email($review->client->email) : null,
            'comment' => SanitizesInput::paragraph($review->commentaire, 1500),
            'rating' => (int) $review->note,
            'date' => optional($review->date_avis)->format('Y-m-d') ?: $review->created_at?->format('Y-m-d'),
            'is_approved' => true,
            'created_at' => $review->date_avis ?: $review->created_at,
        ];
    }
}
