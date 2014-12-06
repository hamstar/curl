<?php

use veqryn\Curl\Curl;
use veqryn\Curl\CurlResponse;

/**
 * Class CurlResponseTest
 * @author Chris Duncan
 */
class CurlResponseTest extends \PHPUnit_Framework_TestCase {

    /** @var CurlResponse */
    protected $responseGoogle;
    protected $responseMultipleHeader;

    public function __construct() {
        parent::__construct();
        $this->responseMultipleHeader = new CurlResponse(file_get_contents(realpath(__DIR__ . '/data/multiple_response_header.txt')));
        $curl = new Curl();
        $this->responseGoogle = $curl->get('www.google.com');
    }

    public function testSeparateHeadersFromBody() {
        $this->assertTrue(is_array($this->responseGoogle->headers) && !empty($this->responseGoogle->headers));
        $this->assertTrue(is_string($this->responseGoogle->body) && !empty($this->responseGoogle->body));
        $this->assertContains('<!doctype', $this->responseGoogle->body);
        $this->assertNotContains('<!doctype', $this->responseGoogle->headers);
    }

    public function testSetStatusHeaders() {
        $this->assertEquals(200, $this->responseGoogle->headers['Status-Code']);
        $this->assertEquals('200 OK', $this->responseGoogle->headers['Status']);
    }

    public function testToStringEqualToBody() {
        $this->assertEquals($this->responseGoogle->body, ('' . $this->responseGoogle));
    }

    public function testMultipleResponseHeadersFromBody() {
        $this->assertTrue(is_array($this->responseMultipleHeader->headers) && !empty($this->responseMultipleHeader->headers));
        $this->assertTrue(is_string($this->responseMultipleHeader->body) && !empty($this->responseMultipleHeader->body));
        $this->assertContains('{example: 1}', $this->responseMultipleHeader->body);
        $this->assertEquals(200, $this->responseMultipleHeader->headers['Status-Code']);
        $this->assertEquals('200 OK', $this->responseMultipleHeader->headers['Status']);
    }
}