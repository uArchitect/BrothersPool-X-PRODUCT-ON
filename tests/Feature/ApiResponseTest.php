<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Traits\ApiResponse;

class TestController
{
    use ApiResponse;

    public function testSuccessResponse()
    {
        return $this->successResponse(['test' => 'data'], 'Success message');
    }

    public function testErrorResponse()
    {
        return $this->errorResponse('Error message', 400);
    }

    public function testNotFoundResponse()
    {
        return $this->notFoundResponse('Resource not found');
    }

    public function testValidationErrorResponse()
    {
        return $this->validationErrorResponse(['field' => 'Field is required']);
    }

    public function testCreatedResponse()
    {
        return $this->createdResponse(['id' => 123], 'Resource created');
    }

    public function testUpdatedResponse()
    {
        return $this->updatedResponse(['updated' => true], 'Resource updated');
    }

    public function testDeletedResponse()
    {
        return $this->deletedResponse('Resource deleted');
    }
}

class ApiResponseTest extends TestCase
{
    /** @test */
    public function it_returns_successful_json_response()
    {
        $controller = new TestController();
        $response = $controller->testSuccessResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'success' => true,
            'message' => 'Success message',
            'data' => ['test' => 'data']
        ], $response->getData(true));
    }

    /** @test */
    public function it_returns_error_json_response()
    {
        $controller = new TestController();
        $response = $controller->testErrorResponse();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals([
            'success' => false,
            'message' => 'Error message'
        ], $response->getData(true));
    }

    /** @test */
    public function it_returns_not_found_response()
    {
        $controller = new TestController();
        $response = $controller->testNotFoundResponse();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals([
            'success' => false,
            'message' => 'Resource not found'
        ], $response->getData(true));
    }

    /** @test */
    public function it_returns_validation_error_response()
    {
        $controller = new TestController();
        $response = $controller->testValidationErrorResponse();

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => ['field' => 'Field is required']
        ], $response->getData(true));
    }

    /** @test */
    public function it_returns_created_response()
    {
        $controller = new TestController();
        $response = $controller->testCreatedResponse();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals([
            'success' => true,
            'message' => 'Resource created',
            'data' => ['id' => 123]
        ], $response->getData(true));
    }

    /** @test */
    public function it_returns_updated_response()
    {
        $controller = new TestController();
        $response = $controller->testUpdatedResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'success' => true,
            'message' => 'Resource updated',
            'data' => ['updated' => true]
        ], $response->getData(true));
    }

    /** @test */
    public function it_returns_deleted_response()
    {
        $controller = new TestController();
        $response = $controller->testDeletedResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'success' => true,
            'message' => 'Resource deleted'
        ], $response->getData(true));
    }

    /** @test */
    public function it_handles_null_data_in_success_response()
    {
        $controller = new TestController();
        $response = $controller->successResponse(null, 'No data');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'success' => true,
            'message' => 'No data'
        ], $response->getData(true));
    }

    /** @test */
    public function it_handles_null_message_in_success_response()
    {
        $controller = new TestController();
        $response = $controller->successResponse(['data' => 'test']);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'success' => true,
            'data' => ['data' => 'test']
        ], $response->getData(true));
    }
}