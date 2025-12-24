<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserController extends ApiController
{
    /**
     * @OA\Delete(
     *     security={{"bearerAuth":{}}},
     *     path="/api/v1/user/{id}",
     *     tags={"User"},
     *     description="Delete a user",
     *     summary="Delete a user",
     *     @OA\Parameter(
     *          in="path",
     *          name="id",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     *
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                throw new \Exception('The user does not exist.', 404);
            }
            $user->delete();
            return $this->result();
        } catch (\Exception $e) {
            return $this->result('', $e->getMessage(), $e->getCode());
        }
    }


	/**
     * @OA\Get(
     *     security={{"bearerAuth":{}}},
     *     path="/api/v1/user/getAll",
     *     tags={"User"},
     *     description="Get all user",
     *     summary="Get all user",
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     *
     */
	public function getAll(Request $request)
	{
	    try {
	        $columns = ['id', 'name', 'email', 'username', 'phone', 'created_at'];

	        $query = User::with('roles')->whereDoesntHave('roles', function ($q) {
		        $q->where('name', 'Staff');
		    });

	        if (!empty($request->input('search.value'))) {
	            $search = $request->input('search.value');

	            $query->where(function ($q) use ($search) {
	                $q->where('name', 'LIKE', "%{$search}%")
	                  ->orWhere('email', 'LIKE', "%{$search}%");
	            });
	        }

	        $orderColumnIndex = $request->input('order.0.column');
	        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
	        $orderDir = $request->input('order.0.dir') ?? 'asc';

	        $query->orderBy($orderColumn, $orderDir);

	        // Use pagination
	        $perPage = $request->input('length', 10); // default to 10 per page
	        $page = floor($request->input('start', 0) / $perPage) + 1;

	        $products = $query->paginate($perPage, ['*'], 'page', $page);

	        return response()->json([
	            'success' => true,
	            'message' => 'Users retrieved successfully.',
	            'data' => $products->items(),
	            'total' => $products->total(),
	            'current_page' => $products->currentPage(),
	            'last_page' => $products->lastPage(),
	            'per_page' => $products->perPage(),
	        ]);
	    } catch (\Exception $e) {
	        return response()->json([
	            'success' => false,
	            'message' => $e->getMessage(),
	            'data' => [],
	            'total' => 0,
	            'current_page' => 1,
	            'last_page' => 1,
	            'per_page' => 10,
	        ], 500);
	    }
	}
}
