<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;

class ProductsImport implements ToModel, WithChunkReading, ShouldQueue, WithHeadingRow, SkipsOnError, SkipsEmptyRows
{
    use Importable, SkipsErrors;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $brand = Brand::where('name', Arr::get($row, 'brand'))->first();
        if (!$brand) {
            $brandRepository = app()->make("App\Repositories\Interfaces\BrandRepository");
            $params = [
                'name' => Arr::get($row, 'brand'),
            ];
            $brandInsert = $brandRepository->store($params);
            $brandId = $brandInsert->id;
        } else {
            $brandId = $brand->id;
        }
        
        return new Product([
            'name' => Arr::get($row, 'name'),
            'brand_id' => $brandId,
            'description' => Arr::get($row, 'description'),
            'type' => Arr::get($row, 'type'),
            'amount' => Arr::get($row, 'amount'),
            'warranty' => Arr::get($row, 'warranty'),
            'product_attr' => [
                'sku' => Arr::get($row, 'sku'),
                'color' => Arr::get($row, 'color'),
                'price' => Arr::get($row, 'price')
            ]
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'max:100'],
            'description' => ['required'],
            'amount'=> ['required','integer'],
            'warranty'=> ['required', 'max:100'],
            'remark'=> ['required'],
            'type'=> ['required', 'string', Rule::in(['instock', 'preorder'])],
            'sku' => ['required'],
            'color' => ['required'],
            'price' => ['required']
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
