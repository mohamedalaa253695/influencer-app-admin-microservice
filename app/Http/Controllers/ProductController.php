<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Jobs\ProductCreated;
use App\Jobs\ProductDeleted;
use App\Jobs\ProductUpdated;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use InfluencerMicroservices\UserService;
use App\Http\Requests\ProductCreateRequest;
use Symfony\Component\HttpFoundation\Response;

class ProductController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->userService->allows('view', 'products');
        $products = Product::paginate(15);
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductCreateRequest $request)
    {
        $this->userService->allows('edit', 'products');

        $product = Product::create($request->only('title', 'description', 'image', 'price'));

        ProductCreated::dispatch($product->toArray())->onQueue('checkout_queue');

        return response($product, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $this->userService->allows('view', 'products');

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $this->userService->allows('edit', 'products');

        $product->update($request->only('title', 'description', 'image', 'price'));

        ProductUpdated::dispatch($product->toArray())->onQueue('checkout_queue');

        return response($product, Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $this->userService->allows('edit', 'products');

        Product::destroy($product->id);

        ProductDeleted::dispatch($product->id)->onQueue('checkout_queue');

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
