<?php

use App\Models\Produit;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Cache;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$pdfPath = $argv[1] ?? null;

if (! $pdfPath || ! is_file($pdfPath)) {
    fwrite(STDERR, "Usage: php scripts/import_pdf_images_to_products.php <catalog.pdf>\n");
    exit(1);
}

$pdf = file_get_contents($pdfPath);

if ($pdf === false) {
    fwrite(STDERR, "Unable to read PDF: {$pdfPath}\n");
    exit(1);
}

$outputDir = public_path('catalog-import/pdf-products');

if (! is_dir($outputDir) && ! mkdir($outputDir, 0775, true) && ! is_dir($outputDir)) {
    fwrite(STDERR, "Unable to create image directory: {$outputDir}\n");
    exit(1);
}

preg_match_all('/(\d+)\s+0\s+obj(.*?)stream\r?\n(.*?)\r?\nendstream/s', $pdf, $matches, PREG_SET_ORDER);

$images = [];

foreach ($matches as $match) {
    $header = $match[2];

    if (! str_contains($header, '/Subtype') || ! str_contains($header, '/Image') || ! str_contains($header, '/DCTDecode')) {
        continue;
    }

    $stream = $match[3];
    $start = strpos($stream, "\xFF\xD8");
    $end = strrpos($stream, "\xFF\xD9");

    if ($start === false || $end === false) {
        continue;
    }

    $images[] = substr($stream, $start, $end - $start + 2);
}

if ($images === []) {
    fwrite(STDERR, "No JPEG product images found in PDF.\n");
    exit(1);
}

$paths = [];

foreach ($images as $index => $imageData) {
    $number = str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
    $relativePath = "/catalog-import/pdf-products/product-{$number}.jpg";
    $absolutePath = public_path(ltrim($relativePath, '/'));

    file_put_contents($absolutePath, $imageData);
    $paths[] = $relativePath;
}

$products = Produit::query()->orderBy('id_produit')->get();
$updated = 0;

foreach ($products as $index => $product) {
    if (! isset($paths[$index])) {
        break;
    }

    $imagePath = $paths[$index];
    $galleryPaths = [$imagePath];

    $extraImageIndex = $index + $products->count();
    if (isset($paths[$extraImageIndex])) {
        $galleryPaths[] = $paths[$extraImageIndex];
    } elseif (count($paths) > 1) {
        $galleryPaths[] = $paths[($index + 1) % count($paths)];
    }

    $product->forceFill([
        'image' => $imagePath,
        'image_url' => $imagePath,
        'gallery_images' => array_values(array_unique($galleryPaths)),
    ])->save();

    $updated++;
}

Cache::flush();

echo "Extracted ".count($paths)." images to {$outputDir}\n";
echo "Updated {$updated} products with PDF image paths\n";
