<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SupportFaq;

$categories = SupportFaq::active()->select('category')->distinct()->get()->map(function($item) {
    return ['key' => $item->category, 'name' => ucfirst($item->category)];
});

echo "Categories:\n";
echo json_encode($categories, JSON_PRETTY_PRINT) . "\n\n";

$questions = SupportFaq::active()->byCategory('shipping')->ordered()->get(['question', 'answer']);

echo "Shipping Questions:\n";
echo json_encode($questions, JSON_PRETTY_PRINT) . "\n";