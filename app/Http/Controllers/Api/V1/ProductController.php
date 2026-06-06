<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Traits\ApiResponse;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly ProductService $productService) {}

    public function index(Request $request): JsonResponse
    {
        return $this->paginated($this->productService->paginate($request->query('search')), ProductResource::class);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return $this->success(new ProductResource($product), 'Producto creado correctamente.', 201);
    }

    public function show(Product $product): JsonResponse
    {
        return $this->success(new ProductResource($product));
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $updated = $this->productService->update($product, $request->validated());

        return $this->success(new ProductResource($updated), 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productService->delete($product);

        return $this->success(message: 'Producto eliminado correctamente.');
    }

    public function exportPdf(): mixed
    {
        return $this->productService->exportPdf();
    }

    public function exportExcel(): mixed
    {
        return $this->productService->exportExcel();
    }
}
