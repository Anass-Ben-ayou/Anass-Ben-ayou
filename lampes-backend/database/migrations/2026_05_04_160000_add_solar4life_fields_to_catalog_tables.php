<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'slug')) {
                $table->string('slug')->nullable()->after('nom')->unique();
            }
        });

        Schema::table('produits', function (Blueprint $table) {
            if (! Schema::hasColumn('produits', 'slug')) {
                $table->string('slug')->nullable()->after('nom')->unique();
            }

            if (! Schema::hasColumn('produits', 'short_description')) {
                $table->text('short_description')->nullable()->after('description');
            }

            if (! Schema::hasColumn('produits', 'old_price')) {
                $table->decimal('old_price', 10, 2)->nullable()->after('prix');
            }

            if (! Schema::hasColumn('produits', 'image')) {
                $table->string('image')->nullable()->after('image_url');
            }

            if (! Schema::hasColumn('produits', 'product_url')) {
                $table->string('product_url')->nullable()->after('image')->unique();
            }

            if (! Schema::hasColumn('produits', 'status')) {
                $table->string('status', 30)->default('active')->after('stock');
            }

            if (! Schema::hasColumn('produits', 'specifications')) {
                $table->json('specifications')->nullable()->after('product_url');
            }
        });

        $this->backfillCategories();
        $this->backfillProducts();
    }

    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $columns = [
                'slug',
                'short_description',
                'old_price',
                'image',
                'product_url',
                'status',
                'specifications',
            ];

            $existing = array_values(array_filter($columns, fn ($column) => Schema::hasColumn('produits', $column)));

            if (! empty($existing)) {
                $table->dropColumn($existing);
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }

    protected function backfillCategories(): void
    {
        $categories = DB::table('categories')->select('id_categorie', 'nom', 'slug')->get();
        $usedSlugs = [];

        foreach ($categories as $category) {
            $slug = $category->slug ?: $this->uniqueSlug($category->nom ?: 'categorie', $usedSlugs);
            $usedSlugs[] = $slug;

            DB::table('categories')
                ->where('id_categorie', $category->id_categorie)
                ->update(['slug' => $slug]);
        }
    }

    protected function backfillProducts(): void
    {
        $products = DB::table('produits')
            ->select('id_produit', 'nom', 'description', 'prix', 'stock', 'image_url', 'slug', 'short_description', 'image', 'status')
            ->get();

        $usedSlugs = [];

        foreach ($products as $product) {
            $slug = $product->slug ?: $this->uniqueSlug($product->nom ?: 'produit', $usedSlugs);
            $usedSlugs[] = $slug;

            DB::table('produits')
                ->where('id_produit', $product->id_produit)
                ->update([
                    'slug' => $slug,
                    'short_description' => $product->short_description ?: Str::limit(trim(strip_tags((string) $product->description)), 180, ''),
                    'image' => $product->image ?: $product->image_url,
                    'status' => $product->status ?: ((int) $product->stock > 0 ? 'active' : 'out_of_stock'),
                ]);
        }
    }

    protected function uniqueSlug(string $value, array $usedSlugs): string
    {
        $baseSlug = Str::slug($value) ?: 'item';
        $slug = $baseSlug;
        $counter = 2;

        while (in_array($slug, $usedSlugs, true)) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
};
