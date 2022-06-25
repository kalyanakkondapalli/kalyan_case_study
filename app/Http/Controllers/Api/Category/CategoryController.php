<?php

namespace App\Http\Controllers\Api\Category;

use Exception;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryRequest;

class CategoryController extends Controller
{

    private Category $category;

    /**
     * @param  Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * @DELETE /categories/{id}
     * Remove category by id
     * @param  int  $id
     * @return JsonResponse|mixed
     */
    public function delete(int $id): mixed
    {
        try {
            $this->category->delete($id);
            return $this->successResponse(['message' => 'Category removed successfully']);
        } catch (Exception $e) {
            return $this->errorMessage($e);
        }
    }

    /**
     * @GET /categories
     * Get all categories
     *
     * @return JsonResponse|mixed
     */
    public function index(): mixed
    {
        try {
            return $this->successResponse([
                'message' => 'Category fetched successfully',
                'data'    => $this->category->with('products')->paginate(),
            ]);
        } catch (Exception $e) {
            return $this->errorMessage($e);
        }
    }

    /**
     * @GET /categories/{id}
     * Fetch single category details
     * @param  int  $id
     * @return JsonResponse|mixed
     */
    public function show(int $id): mixed
    {
        try {
            return $this->successResponse([
                'message' => "Category details fetched successfully",
                'data'    => $this->category->with('products')->findOrFail($id),
            ]);
        } catch (Exception $e) {
            return $this->errorMessage($e);
        }
    }

    /**
     * @POST /categories
     * Add new category
     * @param  CategoryRequest  $request
     * @return JsonResponse|mixed
     */
    public function store(CategoryRequest $request): mixed
    {
        try {
            return $this->successResponse([
                'message' => "Category created successfully",
                'data'    => $this->category->create($request->all()),
            ]);
        } catch (Exception $e) {
            return $this->errorMessage($e);
        }
    }

    /**
     * @PUT /categories/{id}
     * Update given category
     * @param  int  $id
     * @param  CategoryRequest  $request
     * @return JsonResponse|mixed
     */
    public function update(int $id, CategoryRequest $request): mixed
    {        
        try {
            $category = $this->category->with('products')->findOrFail($id);

            if(empty($category)) {
                return $this->errorResponse([
                    'message' => "Category not exist with the requested id.",
                ]);
            }


            return $this->successResponse([
                'message' => "Category updated successfully",
                'data'    => $category->update($request->all()),
            ]);
        } catch (Exception $e) {
            return $this->errorMessage($e);
        }
    }
}
