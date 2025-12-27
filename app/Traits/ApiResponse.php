<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * Return a successful JSON response
     */
    protected function successResponse($data = null, string $message = null, int $code = 200)
    {
        $response = [
            'success' => true,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Return an error JSON response
     */
    protected function errorResponse(string $message, int $code = 400, $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a not found response
     */
    protected function notFoundResponse(string $message = 'Resource not found')
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Return a validation error response
     */
    protected function validationErrorResponse($errors, string $message = 'Validation failed')
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Return an unauthorized response
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized access')
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Return a forbidden response
     */
    protected function forbiddenResponse(string $message = 'Forbidden access')
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Return a server error response
     */
    protected function serverErrorResponse(string $message = 'Internal server error')
    {
        return $this->errorResponse($message, 500);
    }

    /**
     * Return a paginated response
     */
    protected function paginatedResponse($data, $pagination = null)
    {
        $response = [
            'success' => true,
            'data' => $data,
        ];

        if ($pagination) {
            $response['pagination'] = [
                'current_page' => $pagination->currentPage(),
                'per_page' => $pagination->perPage(),
                'total' => $pagination->total(),
                'last_page' => $pagination->lastPage(),
                'from' => $pagination->firstItem(),
                'to' => $pagination->lastItem(),
                'has_more_pages' => $pagination->hasMorePages(),
            ];
        }

        return response()->json($response);
    }

    /**
     * Return a created response
     */
    protected function createdResponse($data = null, string $message = 'Resource created successfully')
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Return an updated response
     */
    protected function updatedResponse($data = null, string $message = 'Resource updated successfully')
    {
        return $this->successResponse($data, $message, 200);
    }

    /**
     * Return a deleted response
     */
    protected function deletedResponse(string $message = 'Resource deleted successfully')
    {
        return $this->successResponse(null, $message, 200);
    }
}
