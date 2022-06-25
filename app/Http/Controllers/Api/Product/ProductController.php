<?php

namespace App\Http\Controllers\Api\Product;

use Exception;
use App\Models\Product;
use App\Traits\FileUpload;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductCreateRequest;
use App\Http\Requests\Product\ProductUpdateRequest;

class ProductController extends Controller
{
    use FileUpload;

    private Product $product;

    /**
     * @param  Product  $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @DELETE /products/{id}
     * Remove product by id
     * @param  int  $id
     * @return JsonResponse|mixed
     */
    public function delete(int $id): mixed
    {
        try {
            $product = $this->getById($id);

            $product->delete($id);
            return $this->successResponse(['message' => 'Product removed successfully']);
        } catch (Exception $e) {
            return $this->errorMessage($e);
        }
    }

    /**
     * @GET /products
     * Get all products
     *
     * @return JsonResponse|mixed
     */
    public function index(): mixed
    {
        try {
            return $this->successResponse([
                'message' => 'Products fetched successfully',
                'data'    => $this->product->with('category')->paginate(),
            ]);
        } catch (Exception $e) {
            return $this->errorMessage($e);
        }
    }

    /**
     * @GET /products/{id}
     * Fetch single product details
     * @param  int  $id
     * @return JsonResponse|mixed
     */
    public function show(int $id): mixed
    {
        try {
            return $this->successResponse([
                'message' => "Product details fetched successfully",
                'data'    => $this->getById($id, ['category']),
            ]);
        } catch (Exception $e) {
            return $this->errorMessage($e);
        }
    }

    /**
     * @POST /products
     * Add new product
     * @param  ProductCreateRequest  $request
     * @return JsonResponse|mixed
     */
    public function store(ProductCreateRequest $request): mixed
    {
        try {
            $path = $this->upload($request->file('product_image'));
            $data = $request->except('product_image');

            return $this->successResponse([
                'message' => "Product created successfully",
                'data'    => $this->product->create(array_merge($data, ['product_image' => $path])),
            ]);
        } catch (Exception $e) {
            return $this->errorMessage($e);
        }
    }

    /**
     * @PUT /products/{id}
     * Update given product
     * @param  int  $id
     * @param  ProductUpdateRequest  $request
     * @return JsonResponse|mixed
     */
    public function update(ProductUpdateRequest $request, int $id): mixed
    {
        $product = $this->getById($id);

        if(empty($category)) {
            return $this->errorResponse([
                'message' => "Product not exist with the requested id.",
            ]);
        }

        try {
            $data = $request->except('product_image');

            if ($request->file('product_image')) {
                $path = $this->upload($request->file('product_image'));
                $data = array_merge($data, ['product_image' => $path]);
            }
            return $this->successResponse([
                'message' => "Product updated successfully",
                'data'    => tap($product)->update($data),
            ]);
        } catch (Exception $e) {
            return $this->errorMessage($e);
        }
    }

    public function getById(int $id, array $relations = []): mixed
    {
        return $this->product->with($relations)->findOrFail($id);
    }
}
