<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return ProductResource::collection(Product::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductCreateRequest $request
     *
     * @return ProductResource
     */
    public function store(ProductCreateRequest $request): ProductResource
    {
        $this->authorize('create', Product::class);

        $product = auth()->user()->products()->create($request->validated());

        return ProductResource::make($product);
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     *
     * @return ProductResource
     */
    public function show(Product $product): ProductResource
    {
        return ProductResource::make($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductUpdateRequest $request
     * @param Product $product
     *
     * @return ProductResource
     */
    public function update(ProductUpdateRequest $request, Product $product): ProductResource
    {
        $this->authorize('update', $product);

        $product->update($request->validated());

        return ProductResource::make($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     *
     * @return JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        try {
            $product->delete();

            return response()->json([
                'message' => __('Product deleted successfully.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => __($e->getMessage()),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
