<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Solar4LifeProductsSeeder extends Seeder
{
    protected const FALLBACK_PRODUCT_IMAGE = 'https://via.placeholder.com/900x900?text=Solarlight';

    // Imports the scraped catalog if a data file is present.
    public function run(): void
    {
        $products = $this->loadProducts();

        if ($products->isEmpty()) {
            $this->command?->warn('Aucun fichier Solar4Life detecte. Placez vos produits dans database/data/solar4life_products.json');
            $this->call([
                CategorieSeeder::class,
                ProduitSeeder::class,
            ]);

            return;
        }

        $this->resetCatalog();

        $imported = 0;

        $products->each(function (array $item) use (&$imported) {
            $categoryName = trim((string) ($item['category'] ?? $item['categorie'] ?? 'Sans categorie'));
            $category = Category::firstOrCreate(
                ['slug' => $this->makeUniqueCategorySlug($categoryName)],
                [
                    'nom' => $categoryName,
                    'description' => $item['category_description'] ?? null,
                ]
            );

            $productUrl = $this->nullableString($item['product_url'] ?? null);
            $slug = $this->makeProductSlug($item, $productUrl);

            $lookup = $productUrl
                ? ['product_url' => $productUrl]
                : ['slug' => $slug];

            $galleryImages = $this->normalizeGalleryImages($item['gallery_images'] ?? [$item['image_url'] ?? $item['image'] ?? null]);

            $product = Product::updateOrCreate($lookup, [
                'id_categorie' => $category->id_categorie,
                'nom' => trim((string) ($item['name'] ?? $item['nom'] ?? 'Produit Solar4Life')),
                'slug' => $slug,
                'description' => $this->nullableString($item['description'] ?? null) ?: 'Description indisponible.',
                'short_description' => $this->buildShortDescription($item),
                'prix' => $this->normalizePrice($item['price'] ?? 0),
                'old_price' => $this->normalizeNullablePrice($item['old_price'] ?? null),
                'image' => $this->normalizeImage($item['image_url'] ?? $item['image'] ?? null),
                'image_url' => $this->normalizeImage($item['image_url'] ?? $item['image'] ?? null),
                'gallery_images' => $galleryImages,
                'product_url' => $productUrl,
                'stock' => $this->normalizeStock($item['stock'] ?? 0),
                'status' => $this->normalizeStatus($item['status'] ?? null, $item['stock'] ?? 0),
                'specifications' => $this->normalizeSpecifications($item['specifications'] ?? null),
            ]);

            DB::table('produits')
                ->where('id_produit', $product->id_produit)
                ->update([
                    'gallery_images' => json_encode($galleryImages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);

            $imported++;
        });

        $this->command?->info("Solar4Life: {$imported} produits importes ou mis a jour.");
        Cache::flush();
    }

    protected function resetCatalog(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('produits')->truncate();
        DB::table('categories')->truncate();
        Schema::enableForeignKeyConstraints();
    }

    // Reads the import file from JSON or PHP format.
    protected function loadProducts(): Collection
    {
        $jsonPath = database_path('data/solar4life_products.json');
        $phpPath = database_path('data/solar4life_products.php');

        if (File::exists($jsonPath)) {
            $decoded = json_decode(File::get($jsonPath), true);

            return collect(is_array($decoded) ? $decoded : []);
        }

        if (File::exists($phpPath)) {
            $data = require $phpPath;

            return collect(is_array($data) ? $data : []);
        }

        return collect();
    }

    // Builds a short product summary when one is missing.
    protected function buildShortDescription(array $item): string
    {
        $short = $this->nullableString($item['short_description'] ?? null);

        if ($short) {
            return $short;
        }

        return Str::limit(trim(strip_tags((string) ($item['description'] ?? ''))), 180, '');
    }

    // Converts a raw price string into a float.
    protected function normalizePrice(mixed $value): float
    {
        return (float) preg_replace('/[^\d.,-]/', '', str_replace(',', '.', (string) $value));
    }

    // Converts an optional old price into a float.
    protected function normalizeNullablePrice(mixed $value): ?float
    {
        $stringValue = $this->nullableString($value);
        if ($stringValue === null) {
            return null;
        }

        return $this->normalizePrice($stringValue);
    }

    // Normalizes stock values coming from the import file.
    protected function normalizeStock(mixed $value): int
    {
        if (is_numeric($value)) {
            return max(0, (int) $value);
        }

        $normalized = Str::lower((string) $value);

        if (str_contains($normalized, 'rupture') || str_contains($normalized, 'indisponible')) {
            return 0;
        }

        return 10;
    }

    // Chooses a product status based on explicit data or stock.
    protected function normalizeStatus(mixed $status, mixed $stock): string
    {
        $normalizedStatus = Str::lower(trim((string) $status));
        if (in_array($normalizedStatus, ['active', 'inactive', 'draft', 'out_of_stock'], true)) {
            return $normalizedStatus;
        }

        return $this->normalizeStock($stock) > 0 ? 'active' : 'out_of_stock';
    }

    // Makes sure specifications are stored as an array.
    protected function normalizeSpecifications(mixed $specifications): ?array
    {
        if (is_array($specifications)) {
            return $specifications;
        }

        if (is_string($specifications) && $specifications !== '') {
            $decoded = json_decode($specifications, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            return ['details' => $specifications];
        }

        return null;
    }

    // Turns empty strings into null values.
    protected function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function normalizeImage(mixed $value): string
    {
        return $this->nullableString($value) ?: self::FALLBACK_PRODUCT_IMAGE;
    }

    protected function normalizeGalleryImages(mixed $value): array
    {
        $images = is_array($value) ? $value : [$value];

        $normalized = collect($images)
            ->map(fn ($image) => $this->nullableString($image))
            ->filter()
            ->map(fn ($image) => $this->normalizeImage($image))
            ->unique()
            ->values()
            ->all();

        if ($normalized === []) {
            return [self::FALLBACK_PRODUCT_IMAGE];
        }

        return $normalized;
    }

    // Keeps category slugs unique across imports.
    protected function makeUniqueCategorySlug(string $name): string
    {
        $baseSlug = Str::slug($name) ?: 'categorie';
        $slug = $baseSlug;
        $counter = 2;

        while (Category::where('slug', $slug)->where('nom', '!=', $name)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    // Builds a stable product slug and avoids duplicates.
    protected function makeProductSlug(array $item, ?string $productUrl): string
    {
        $candidate = $this->nullableString($item['slug'] ?? null)
            ?: Str::slug((string) ($item['name'] ?? $item['nom'] ?? 'produit-solar4life'));

        $baseSlug = $candidate ?: 'produit-solar4life';
        $slug = $baseSlug;
        $counter = 2;

        while (
            Product::where('slug', $slug)
                ->when($productUrl, fn ($query) => $query->where('product_url', '!=', $productUrl))
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
