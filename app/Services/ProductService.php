<?php

namespace App\Services;

use App\Exports\ProductsExport;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Product::orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh();
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function exportPdf(): mixed
    {
        $products = Product::orderBy('created_at', 'desc')->get();

        return Pdf::loadView('exports.products-pdf', compact('products'))
            ->download('productos.pdf');
    }

    public function exportExcel(): BinaryFileResponse
    {
        return Excel::download(new ProductsExport(), 'productos.xlsx');
    }
}
