<?php

namespace RealPage\JsonApi\Requests;

use Illuminate\Http\Request as IlluminateRequest;
use Illuminate\Contracts\Validation\Validator;
use Mockery;
use Neomerx\JsonApi\Exceptions\ErrorCollection;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use PHPUnit\Framework\TestCase;
use RealPage\JsonApi\Validation\RequestFailedValidation;

class RequestTest extends TestCase
{
    /** @var Mockery\MockInterface */
    protected $illuminateRequest;

    /** @var \RealPage\JsonApi\Requests\Request */
    protected $request;

    public function setUp()
    {
        parent::setUp();

        $this->illuminateRequest = Mockery::mock(IlluminateRequest::class);
        $this->request = new Request($this->illuminateRequest);
    }

    /** @test */
    public function suppliesRequest()
    {
        $this->assertEquals($this->illuminateRequest, $this->request->request());
    }

    /** @test */
    public function suppliesValidator()
    {
        $validator = Mockery::mock(Validator::class);

        $this->request->setValidator($validator);
        $this->assertEquals($validator, $this->request->validator());
    }

    /** @test */
    public function decodesJson()
    {
        $this->illuminateRequest->shouldReceive('getContent')->andReturn('{"Hello": "world"}');

        $this->assertEquals(['Hello' => 'world'], $this->request->json());
    }

    /** @test */
    public function throwsMalformedRequestWhenJsonIsInvalid()
    {
        $this->illuminateRequest->shouldReceive('getContent')->andReturn('{"Hello": "world""}');

        $error = null;
        try {
            $this->request->json();
        } catch (JsonApiException $e) {
            $this->assertEquals(1, $e->getErrors()->count());
            $error = $e->getErrors()->getArrayCopy()[0];
        }

        $this->assertInstanceOf(MalformedRequest::class, $error);
    }

    /** @test */
    public function canPassValidation()
    {
        $validator = Mockery::mock(Validator::class);
        $validator->shouldReceive('isValid')->with($this->request)->andReturn(true);

        $this->request->setValidator($validator);

        $this->request->validate();
    }

    /** @test */
    public function throwsExceptionWhenValidationIsInvalid()
    {
        $errors = new ErrorCollection();
        $validator = Mockery::mock(Validator::class);
        $validator->shouldReceive('isValid')->with($this->request)->andReturn(false);
        $validator->shouldReceive('errors')->andReturn($errors);

        $this->request->setValidator($validator);

        $exception = null;
        try {
            $this->request->validate();
        } catch (RequestFailedValidation $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(RequestFailedValidation::class, $exception);
        $this->assertEquals($errors, $exception->getErrors());
    }
}