<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\ProductUnit;
use Illuminate\Support\Facades\DB;

class ProductImporter extends Component
{
    use WithFileUploads;

    public $file;
    public $step = 1; 
    public $unmatchedCategories = []; 
    public $unmatchedUnits = [];
    public $categoryMappings = [];
    public $unitMappings = [];
    public $dbCategories = [];
    public $dbUnits = [];
    public $importStats = null;
    public $failedRows = [];

    public function downloadTemplate()
    {
        return response()->streamDownload(function () {
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
            // 🌟 إضافة expiry_date في النهاية
            fputcsv($file, ['name', 'category', 'sku', 'base_unit', 'cost_price', 'selling_price', 'wholesale_unit', 'wholesale_barcode', 'wholesale_price', 'expiry_date']);
            fputcsv($file, ['مثال: لبن كابو 1 كيلو', 'الألبان', '628123', 'حبة', '3800', '4200', 'كرتونة', '628124', '49000', '2026-12-31']);
            fclose($file);
        }, 'products_template.csv');
    }

    private function cleanText($text)
    {
        if (empty($text)) return '';
        $text = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $text);
        $text = str_replace(['ة', 'أ', 'إ', 'آ'], ['ه', 'ا', 'ا', 'ا'], $text);
        return trim($text);
    }

    // تنظيف عناوين الإكسيل من الرموز المخفية (BOM)
    private function cleanHeaders($headers)
    {
        return array_map(function($h) {
            return trim(preg_replace('/[\xEF\xBB\xBF]/', '', strtolower($h)));
        }, $headers);
    }

    public function analyzeFile()
    {
        $this->validate(['file' => 'required|mimes:csv,txt|max:5120']);

        $filePath = $this->file->getRealPath();
        $fileData = array_map('str_getcsv', file($filePath));
        $header = $this->cleanHeaders(array_shift($fileData));

        $csvCategories = [];
        $csvUnits = [];

        foreach ($fileData as $row) {
            if (empty(implode('', $row))) continue;
            
            // الاعتماد على الترتيب لضمان عدم فشل النظام
            $cat = $row[1] ?? 'عام';
            $bUnit = $row[3] ?? 'حبة';
            $wUnit = $row[6] ?? '';

            if (!empty($cat)) $csvCategories[trim($cat)] = true;
            if (!empty($bUnit)) $csvUnits[trim($bUnit)] = true;      
            if (!empty($wUnit)) $csvUnits[trim($wUnit)] = true;      
        }

        $csvCategories = array_keys($csvCategories);
        $csvUnits = array_keys($csvUnits);

        $this->dbCategories = Category::pluck('name', 'id')->toArray();
        $this->dbUnits = Unit::pluck('name', 'id')->toArray();

        $this->unmatchedCategories = array_diff($csvCategories, $this->dbCategories);
        $this->unmatchedUnits = array_diff($csvUnits, $this->dbUnits);

        foreach ($this->unmatchedCategories as $cat) { $this->categoryMappings[$cat] = 'NEW'; }
        foreach ($this->unmatchedUnits as $unit) { $this->unitMappings[$unit] = 'NEW'; }

        if (count($this->unmatchedCategories) > 0 || count($this->unmatchedUnits) > 0) {
            $this->step = 2;
        } else {
            $this->executeImport();
        }
    }

    public function executeImport()
    {
        $filePath = $this->file->getRealPath();
        $fileData = array_map('str_getcsv', file($filePath));
        $header = $this->cleanHeaders(array_shift($fileData));

        $successful = 0;
        $this->failedRows = [];

        $finalCategoryDict = array_flip($this->dbCategories); 
        foreach ($this->categoryMappings as $csvName => $decision) {
            if ($decision === 'NEW') {
                $newCat = Category::create(['name' => $csvName]);
                $finalCategoryDict[$csvName] = $newCat->id;
            } else {
                $finalCategoryDict[$csvName] = $decision; 
            }
        }

        $finalUnitDict = array_flip($this->dbUnits);
        foreach ($this->unitMappings as $csvName => $decision) {
            if ($decision === 'NEW') {
                $newUnit = Unit::create(['name' => $csvName, 'type' => 'quantity', 'conversion_rate' => 1]);
                $finalUnitDict[$csvName] = $newUnit->id;
            } else {
                $finalUnitDict[$csvName] = $decision; 
            }
        }

        foreach ($fileData as $index => $row) {
            if (empty(implode('', $row))) continue;

            try {
                $data = [];
                if (count($header) === count($row)) {
                    $data = array_combine($header, $row);
                }

                // 🌟 السحر هنا: استخدام المتغيرات بمرونة تامة (بحث بالاسم أو بالترتيب)
                $pName = $data['product_name'] ?? $data['name'] ?? $row[0] ?? null;
                $pCat  = $data['category'] ?? $data['category_id'] ?? $row[1] ?? 'عام';
                $pSku  = $data['retail_barcode'] ?? $data['sku'] ?? $row[2] ?? '';
                $pBaseUnit = $data['base_unit'] ?? $data['base_unit_id'] ?? $row[3] ?? 'حبة';
                $pCost = $data['cost_price'] ?? $data['current_cost_price'] ?? $row[4] ?? 0;
                $pSell = $data['retail_price'] ?? $data['selling_price'] ?? $data['current_selling_price'] ?? $row[5] ?? 0;
                $pWholesaleUnit = $data['wholesale_unit'] ?? $row[6] ?? '';
                $pWholesaleSku  = $data['wholesale_barcode'] ?? $row[7] ?? '';
                $pWholesalePrice = $data['wholesale_price'] ?? $row[8] ?? '';
                $pExpiry = $data['expiry_date'] ?? $row[9] ?? null; // 🌟 جلب التاريخ

                if (empty($pName)) {
                    throw new \Exception('اسم المنتج مفقود في هذا السطر.');
                }

                DB::transaction(function () use ($pName, $pCat, $pSku, $pBaseUnit, $pCost, $pSell, $pWholesaleUnit, $pWholesaleSku, $pWholesalePrice, $pExpiry, $finalCategoryDict, $finalUnitDict) {
                    
                    $catId = $finalCategoryDict[trim($pCat)] ?? null;
                    $baseUnitId = $finalUnitDict[trim($pBaseUnit)] ?? null;
                    
                    // تنظيف وتنسيق التاريخ إن وُجد
                    $cleanExpiry = null;
                    if (!empty($pExpiry) && strtotime($pExpiry)) {
                        $cleanExpiry = date('Y-m-d', strtotime(trim($pExpiry)));
                    }

                    $product = Product::updateOrCreate(
                        ['sku' => trim($pSku)],
                        [
                            'name' => trim($pName),
                            'category_id' => $catId,
                            'base_unit_id' => $baseUnitId,
                            'current_cost_price' => (float) $pCost,
                            'current_selling_price' => (float) $pSell,
                            'current_stock' => 0,
                            'expiry_date' => $cleanExpiry, // 🌟 حفظ التاريخ
                            'has_fraction' => true,
                            'is_active' => true,
                        ]
                    );

                    if (!empty($pWholesaleUnit) && !empty($pWholesalePrice)) {
                        $wholesaleUnitId = $finalUnitDict[trim($pWholesaleUnit)] ?? null;

                        ProductUnit::updateOrCreate(
                            ['product_id' => $product->id, 'unit_id' => $wholesaleUnitId],
                            [
                                'barcode' => trim($pWholesaleSku ?: rand(10000000,99999999)),
                                'specific_selling_price' => (float) $pWholesalePrice
                            ]
                        );
                    }
                });

                $successful++;

            } catch (\Exception $e) {
                $this->failedRows[] = [
                    'row_number' => $index + 2,
                    'product_name' => $row[0] ?? 'غير معروف',
                    'error' => $e->getMessage()
                ];
            }
        }

        $this->importStats = ['total' => count($fileData), 'successful' => $successful, 'failed' => count($this->failedRows)];
        $this->step = 3;
    }

    public function resetImporter()
    {
        $this->reset(['file', 'step', 'unmatchedCategories', 'unmatchedUnits', 'categoryMappings', 'unitMappings', 'importStats', 'failedRows']);
    }

    public function render()
    {
        return view('components.product-importer');
    }
}