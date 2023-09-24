<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        return Product::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductCreateRequest $request
     *
     */
    public function store(ProductCreateRequest $request)
    {
        $product = Product::create($request->validated());

        return $product;
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     *
     */
    public function show(Product $product)
    {
        return $product;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductUpdateRequest $request
     * @param Product $product
     *
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $product->update($request->validated());

        return $product;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     *
     * @return JsonResponse
     */
    public function destroy(Product $product)
    {
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
