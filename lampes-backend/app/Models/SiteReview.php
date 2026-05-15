<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteReview extends Model
{
    use HasFactory;

    protected $table = 'site_reviews';

    protected $primaryKey = 'id_site_review';

    protected $fillable = [
        'customer_name',
        'email',
        'rating',
        'comment',
        'review_date',
        'is_approved',
    ];

    protected $casts = [
        'review_date' => 'date',
        'is_approved' => 'boolean',
        'rating' => 'integer',
    ];
}
