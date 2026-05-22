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
    fwrite(STDERR, "Usage: php scripts/sync_pdf_catalog_products.php <catalog.pdf>\n");
    exit(1);
}

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

$pdf = file_get_contents($pdfPath);
$pdfText = mb_convert_encoding($pdf, 'UTF-8', 'ISO-8859-1');

$products = [];
$productGalleries = [];
$categorySlugs = [];

preg_match_all('/(\d+)\s+0\s+obj(.*?)endobj/s', $pdfText, $objectMatches, PREG_SET_ORDER);
$objects = [];

foreach ($objectMatches as $match) {
    $objects[(int) $match[1]] = $match[2];
}

preg_match_all('/(\d+)\s+0\s+obj(.*?)stream\r?\n(.*?)\r?\nendstream/s', $pdf, $streamMatches, PREG_SET_ORDER);
$imageObjectPaths = [];
$imageIndex = 1;

foreach ($streamMatches as $match) {
    $objectId = (int) $match[1];
    $header = $match[2];

    if (! str_contains($header, '/Subtype') || ! str_contains($header, '/Image') || ! str_contains($header, '/DCTDecode')) {
        continue;
    }

    $imageObjectPaths[$objectId] = '/catalog-import/pdf-products/product-'.str_pad((string) $imageIndex, 3, '0', STR_PAD_LEFT).'.jpg';
    $imageIndex++;
}

foreach ($objects as $objectId => $body) {
    if (! str_contains($body, '/Type/Page') || str_contains($body, '/Type/Pages')) {
        continue;
    }

    preg_match_all('/\/Image\d+\s+(\d+)\s+0\s+R/', $body, $imageMatches);
    $pageImages = [];

    foreach ($imageMatches[1] as $imageObjectId) {
        $imagePath = $imageObjectPaths[(int) $imageObjectId] ?? null;

        if ($imagePath) {
            $pageImages[] = $imagePath;
        }
    }

    preg_match('/\/Annots\[(.*?)\]/s', $body, $annotsMatch);
    preg_match_all('/(\d+)\s+0\s+R/', $annotsMatch[1] ?? '', $annotMatches);

    $pageCategorySlug = null;
    $pageProductSlugs = [];

    foreach ($annotMatches[1] as $annotObjectId) {
        $annotBody = $objects[(int) $annotObjectId] ?? '';

        if (! preg_match('#/URI\((https?://[^)]*|solar4life\.fr/[^)]*)\)#', $annotBody, $urlMatch)) {
            continue;
        }

        $url = strtok(rtrim($urlMatch[1], '/'), '?');

        if (str_contains($url, '/categorie-produit/')) {
            $pageCategorySlug = basename(parse_url($url, PHP_URL_PATH) ?: '');

            if ($pageCategorySlug !== '') {
                $categorySlugs[$pageCategorySlug] = true;
            }

            continue;
        }

        if (! str_contains($url, '/produit/')) {
            continue;
        }

        $slug = basename(parse_url($url, PHP_URL_PATH) ?: '');

        if ($slug === '') {
            continue;
        }

        $pageProductSlugs[] = $slug;

        if (! isset($products[$slug])) {
            $products[$slug] = [
                'slug' => $slug,
                'name' => Str::of($slug)->replace(['-', '_'], ' ')->title()->toString(),
                'url' => $url,
                'category_slug' => $pageCategorySlug ?: 'applique-solaire-luminaire-solaire',
            ];
            $productGalleries[$slug] = [];
        } elseif ($pageCategorySlug && ($products[$slug]['category_slug'] ?? null) === 'applique-solaire-luminaire-solaire') {
            $products[$slug]['category_slug'] = $pageCategorySlug;
        }
    }

    $pageProductSlugs = array_values(array_unique($pageProductSlugs));
    $pageImages = array_values(array_unique($pageImages));

    if ($pageProductSlugs === [] || $pageImages === []) {
        continue;
    }

    $chunkSize = max(1, (int) ceil(count($pageImages) / count($pageProductSlugs)));
    $imageChunks = array_chunk($pageImages, $chunkSize);

    foreach ($pageProductSlugs as $index => $slug) {
        $productGalleries[$slug] = array_values(array_unique(array_merge(
            $productGalleries[$slug] ?? [],
            $imageChunks[$index] ?? []
        )));
    }
}

if ($products === []) {
    fwrite(STDERR, "No PDF products found.\n");
    exit(1);
}

$categories = [];
foreach (array_unique(array_merge(array_keys($categoryNames), array_keys($categorySlugs))) as $slug) {
    $name = $categoryNames[$slug] ?? Str::of($slug)->replace('-', ' ')->title()->toString();

    $categories[$slug] = Categorie::updateOrCreate(
        ['slug' => $slug],
        [
            'nom' => $name,
            'description' => 'Categorie importee depuis le catalogue PDF Solar4Life.',
        ]
    );
}

$productRows = array_values($products);
$usedSlugs = [];
$updated = 0;

foreach ($productRows as $index => $item) {
    $category = $categories[$item['category_slug']] ?? reset($categories);
    $gallery = array_values(array_unique($productGalleries[$item['slug']] ?? []));
    $mainImage = $gallery[0] ?? '';
    $slug = uniqueSlug($item['slug'], $usedSlugs);
    $usedSlugs[] = $slug;

    $product = Produit::query()->orderBy('id_produit')->skip($index)->first();

    if (! $product) {
        $product = new Produit();
        $product->stock = 10;
        $product->prix = 0;
        $product->description = '';
    }

    $product->forceFill([
        'nom' => $item['name'],
        'slug' => $slug,
        'description' => '',
        'short_description' => '',
        'prix' => $product->prix > 0 ? $product->prix : 199,
        'stock' => max((int) $product->stock, 10),
        'status' => 'active',
        'image' => $mainImage,
        'image_url' => $mainImage,
        'gallery_images' => $gallery,
        'product_url' => $item['url'],
        'id_categorie' => $category->id_categorie,
    ])->save();

    $updated++;
}

$keepIds = Produit::query()
    ->orderBy('id_produit')
    ->limit(count($productRows))
    ->pluck('id_produit');

Produit::query()
    ->whereNotIn('id_produit', $keepIds)
    ->update(['status' => 'inactive']);

Cache::flush();

echo 'Synced '.$updated." real PDF products with their own image galleries\n";
echo 'Inactive old extra products: '.Produit::where('status', 'inactive')->count()."\n";

function uniqueSlug(string $baseSlug, array $usedSlugs): string
{
    $slug = $baseSlug;
    $counter = 2;

    while (in_array($slug, $usedSlugs, true)) {
        $slug = $baseSlug.'-'.$counter;
        $counter++;
    }

    return $slug;
}
