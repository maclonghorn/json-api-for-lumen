<?php

namespace RealPage\JsonApi;

use Illuminate\Http\Request;

class MediaTypeGuardTest extends \PHPUnit\Framework\TestCase
{
    protected $contentType;

    /** @var MediaTypeGuard */
    protected $guard;

    public function setUp()
    {
        $this->contentType = 'application/vnd.api+json';
        $this->guard       = new MediaTypeGuard($this->contentType);
    }

    public function testIsBuiltWithDependencies()
    {
        $this->assertEquals($this->contentType, $this->guard->getContentType());
    }

    public function testExistingContentTypeValidation()
    {
        $noContentTypeRequest    = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->setMethods(['header'])->getMock();
        $badContentTypeRequest   = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->setMethods(['header'])->getMock();
        $validContentTypeRequest = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->setMethods(['header'])->getMock();
        $noContentTypeRequest->expects($this->any())->method('header')->with('Accept')->willReturn('');
        $badContentTypeRequest->expects($this->any())->method('header')->with('Accept')->willReturn('application/json');
        $validContentTypeRequest->expects($this->any())->method('header')->with('Accept')->willReturn('application/vnd.api+json');

        $this->assertTrue($this->guard->validateExistingContentType($noContentTypeRequest));
        $this->assertFalse($this->guard->validateExistingContentType($badContentTypeRequest));
        $this->assertTrue($this->guard->validateExistingContentType($validContentTypeRequest));
    }

    public function testRecognizesIfRequestMustHaveContentTypeHeader()
    {
        $getRequest = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->setMethods(['method'])->getMock();
        $postRequest  = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->setMethods(['method'])->getMock();
        $patchRequest  = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->setMethods(['method'])->getMock();
        $getRequest->expects($this->any())->method('method')->willReturn('GET');
        $postRequest->expects($this->any())->method('method')->willReturn('POST');
        $patchRequest->expects($this->any())->method('method')->willReturn('POST');

        $this->assertFalse($this->guard->clientRequestMustHaveContentTypeHeader($getRequest));
        $this->assertTrue($this->guard->clientRequestMustHaveContentTypeHeader($postRequest));
        $this->assertTrue($this->guard->clientRequestMustHaveContentTypeHeader($patchRequest));
    }

    public function testContentTypeIsValid()
    {
        $validContentType                 = 'application/vnd.api+json';
        $invalidContentType               = 'application/json';
        $invalidContentTypeWithParameters = 'application/vnd.api+json; extras=bad';

        $this->assertTrue($this->guard->contentTypeIsValid($validContentType));
        $this->assertFalse($this->guard->contentTypeIsValid($invalidContentType));
        $this->assertFalse($this->guard->contentTypeIsValid($invalidContentTypeWithParameters));
    }

    public function testCanTellCorrectContentTypeWithData()
    {
        $validContentType    = 'application/vnd.api+json';
        $invalidContentType  = 'application/json';
        $getValidRequest   = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->setMethods([
            'header',
            'method',
        ])->getMock();
        $getInvalidRequest = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->setMethods([
            'header',
            'method',
        ])->getMock();
        $getWithoutRequest  = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->setMethods([
            'header',
            'method',
        ])->getMock();
        $postValidRequest    = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->setMethods([
            'header',
            'method',
        ])->getMock();
        $postInvalidRequest  = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->setMethods([
            'header',
            'method',
        ])->getMock();
        $postWithoutRequest  = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->setMethods([
            'header',
            'method',
        ])->getMock();
        $getValidRequest->expects($this->any())->method('method')->willReturn('GET');
        $getValidRequest->expects($this->any())->method('header')->willReturn($validContentType);
        $getInvalidRequest->expects($this->any())->method('method')->willReturn('GET');
        $getInvalidRequest->expects($this->any())->method('header')->willReturn($invalidContentType);
        $getWithoutRequest->expects($this->any())->method('method')->willReturn('GET');
        $getWithoutRequest->expects($this->any())->method('header')->willReturn(null);
        $postValidRequest->expects($this->any())->method('method')->willReturn('POST');
        $postValidRequest->expects($this->any())->method('header')->willReturn($validContentType);
        $postInvalidRequest->expects($this->any())->method('method')->willReturn('POST');
        $postInvalidRequest->expects($this->any())->method('header')->willReturn($invalidContentType);
        $postWithoutRequest->expects($this->any())->method('method')->willReturn('POST');
        $postWithoutRequest->expects($this->any())->method('header')->willReturn(null);

        $this->assertTrue($this->guard->hasCorrectHeadersForData($getValidRequest));
        $this->assertTrue($this->guard->hasCorrectHeadersForData($getInvalidRequest));
        $this->assertTrue($this->guard->hasCorrectHeadersForData($getWithoutRequest));
        $this->assertTrue($this->guard->hasCorrectHeadersForData($postValidRequest));
        $this->assertFalse($this->guard->hasCorrectHeadersForData($postInvalidRequest));
        $this->assertFalse($this->guard->hasCorrectHeadersForData($postWithoutRequest));
    }

    public function testDeterminesCorrectlySetAcceptHeader()
    {
        $validWildcardAcceptRequest   = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->setMethods(['header'])->getMock();
        $validStandardAcceptRequest   = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->setMethods(['header'])->getMock();
        $invalidStandardAcceptRequest = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->setMethods(['header'])->getMock();
        $invalidAcceptRequest         = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->setMethods(['header'])->getMock();
        $validWildcardAcceptRequest->expects($this->any())->method('header')->with('Accept')->willReturn('*/*');
        $validStandardAcceptRequest->expects($this->any())->method('header')->with('Accept')->willReturn('application/vnd.api+json, application/json');
        $invalidStandardAcceptRequest->expects($this->any())->method('header')->with('Accept')->willReturn('application/vnd.api+json; test=true, application/json');
        $invalidAcceptRequest->expects($this->any())->method('header')->with('Accept')->willReturn('application/json');

        $this->assertTrue($this->guard->hasCorrectlySetAcceptHeader($validWildcardAcceptRequest));
        $this->assertTrue($this->guard->hasCorrectlySetAcceptHeader($validStandardAcceptRequest));
        $this->assertFalse($this->guard->hasCorrectlySetAcceptHeader($invalidStandardAcceptRequest));
        $this->assertFalse($this->guard->hasCorrectlySetAcceptHeader($invalidAcceptRequest));
    }
}
