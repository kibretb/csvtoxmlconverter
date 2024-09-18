<?php

namespace App\Http\Controllers;

use Spatie\ArrayToXml\ArrayToXml;
use Storage;

class CsvtoXmlconvertorController extends Controller
{
    //
    public function convertCsvToXml()
    {
        $csvContent = Storage::disk('public')->get('product-data.csv');

        $csvData = array_map('str_getcsv', explode("\n", $csvContent));

        $products = $this->organizeProducts($csvData);

        $xmlArray = [
            'product' => array_values($products),
        ];

        $xml = ArrayToXml::convert($xmlArray, [
            'rootElementName' => 'catalog',
            '_attributes' => ['xmlns' => 'http://www.demandware.com/xml/impex/catalog/2006-10-31', 'catalog-id' => 'TestCatalog'],
        ], true, 'UTF-8');

        return response($xml, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="products.xml"');
    }

    private function organizeProducts(array $data): array
    {
        $products = [];

        foreach ($data as $row) {

            if (count($row) !== 7) {
                continue; 
            }

            [$productId, $brand, $displayName, $variantId, $color, $size, $isDefault] = $row;

            // Ensure the main product entry exists
            if (! isset($products[$productId])) {
                $products[$productId] = [
                    '_attributes' => ['product-id' => $productId],
                    'display-name' => [
                        '_attributes' => ['xml:lang' => 'x-default'],
                        '_value' => $displayName,
                    ],
                    'brand' => $brand,
                    'variations' => [
                        'variants' => [],
                    ],
                ];
            }

            $products[$productId]['variations']['variants']['variant'][] = [
                '_attributes' => $isDefault === 'Y'
                    ? ['product-id' => $variantId, 'default' => 'true']
                    : ['product-id' => $variantId],
            ];

            // Add variant-specific details if not already added
            if (! isset($products[$variantId])) {
                $products[$variantId] = [
                    '_attributes' => ['product-id' => $variantId],
                    'custom-attributes' => [
                        'custom-attribute' => [
                            ['_attributes' => ['attribute-id' => 'colour'], '_value' => $color],
                            ['_attributes' => ['attribute-id' => 'size'], '_value' => $size],
                        ],
                    ],
                ];
            }
        }

        return $products;
    }
}
