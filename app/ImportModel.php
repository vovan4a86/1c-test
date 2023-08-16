<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Mavsan\LaProtocol\Interfaces\Import;

class ImportModel extends Model implements Import
{
    public function import($fileName): string
    {
        $fileName = File::basename($fileName);

        $data = session('import_' . $fileName, 0);
        session(['import_' . $fileName => ++$data]);

        $xmlFile = file_get_contents($fileName);
        $xmlObject = simplexml_load_string($xmlFile);
        $jsonFormatData = json_encode($xmlObject);
        $result = json_decode($jsonFormatData, true);

        foreach ($result['Классификатор']['Категории'] as $category) {
            $current_category = Category::where('1c_category_id', $category['Ид'])->first();

            if (!$current_category) {
                Category::create(
                    [
                        'parent_id' => 0,
                        '1c_category_id' => $category['Ид'],
                        'name' => $category['Наименование']
                    ]
                );
            }
        }

        foreach ($result['Каталог']['Товары'] as $products) {
            foreach ($products as $product) {
                $current_category = Category::where('1c_category_id', $product['Категория'])->first();
                $current_product = Product::query()->where('1c_product_id', $product['Ид'])->first();
                $data = [
                    'category_id' => $current_category->id,
                    '1c_product_id' => $product['Ид'],
                    'name' => $product['Наименование'],
                    'text' => $product['Код'],
                    'price' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                if (!$current_product) {
                    Product::create($data);
                } else {
                    $current_product->update($data);
                }
            }
        }

//        if ($data < 5) {
//            return self::answerProgress;
//        }

        return self::answerSuccess;
    }

    public function getAnswerDetail(): string
    {
        return 'Loaded!';
    }
}
