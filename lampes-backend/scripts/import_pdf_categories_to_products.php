<?php

use App\Models\Categorie;
use App\Models\Produit;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$pdfPath = $argv[1] ?? null;

if (! $pdfPath || ! is_file($pdfPath)) {
    fwrite(STDERR, "Usage: php scripts/import_pdf_categories_to_products.php <catalog.pdf>\n");
    exit(1);
}

$pdf = file_get_contents($pdfPath);

if ($pdf === false) {
    fwrite(STDERR, "Unable to read PDF: {$pdfPath}\n");
    exit(1);
}

$text = mb_convert_encoding($pdf, 'UTF-8', 'ISO-8859-1');
preg_match_all('#/URI\((https?://[^)]*|solar4life\.fr/[^)]*)\)#', $text, $matches);

$categoryNames = [
    'applique-solaire-luminaire-solaire' => 'Applique solaire',
    'guirlande-solaire-luminaire-solaire' => 'Guirlande solaire',
    'kit-photovoltaique-plugplay' => 'Kit photovoltaique plugplay',
    'piquet-solaire-luminaire-solaire' => 'Piquet solaire',
    'potelet-borne-solaire-luminaire-eclairage' => 'Potelet et borne solaire',
    'projecteur-solaire-luminaire-solaire' => 'Projecteur solaire luminaire',
    'projecteur-solaire-solar' => 'Projecteur solaire',
    'solaire-luminaire-urbain-collectivite-luminaire-solaire' => 'Luminaire urbain solaire',
    'spot-encastrable-solaire-luminaire-solaire' => 'Spot encastrable solaire',
];

$categorySlugs = [];
$productCategorySlugs = [];
$currentCategorySlug = null;

foreach ($matches[1] as $url) {
    if (str_contains($url, '/categorie-produit/')) {
        $slug = trim(parse_url($url, PHP_URL_PATH) ?: '', '/');
        $slug = basename($slug);

        if ($slug !== '') {
            $categorySlugs[$slug] = true;
            $currentCategorySlug = $slug;
        }

        continue;
    }

    if (str_contains($url, '/produit/') && $currentCategorySlug) {
        $productCategorySlugs[] = $currentCategorySlug;
    }
}

foreach (array_keys($categoryNames) as $slug) {
    $categorySlugs[$slug] = true;
}

$categoriesBySlug = [];

foreach (array_keys($categorySlugs) as $slug) {
    $name = $categoryNames[$slug] ?? Str::of($slug)->replace('-', ' ')->title()->toString();

    $categoriesBySlug[$slug] = Categorie::updateOrCreate(
        ['slug' => $slug],
        [
            'nom' => $name,
            'description' => "Categorie importee depuis le catalogue PDF Solar4Life.",
        ]
    );
}

$assignmentSlugs = array_values(array_filter($productCategorySlugs, fn ($slug) => isset($categoriesBySlug[$slug])));
$assignmentSlugs = array_merge($assignmentSlugs, array_keys($categoriesBySlug));

if ($assignmentSlugs === []) {
    fwrite(STDERR, "No PDF categories found.\n");
    exit(1);
}

$products = Produit::query()->orderBy('id_produit')->get();
$updated = 0;

foreach ($products as $index => $product) {
    $slug = $assignmentSlugs[$index % count($assignmentSlugs)];
    $category = $categoriesBySlug[$slug];

    $product->forceFill([
        'id_categorie' => $category->id_categorie,
    ])->save();

    $updated++;
}

Cache::flush();

echo "Imported ".count($categoriesBySlug)." PDF categories\n";
echo "Updated {$updated} products with PDF categories\n";

