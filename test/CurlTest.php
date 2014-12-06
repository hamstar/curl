<?php

use veqryn\Curl\Curl;
use veqryn\Curl\CurlException;

/**
 * Class CurlTest
 * @author Chris Duncan
 */
class CurlTest extends \PHPUnit_Framework_TestCase {

    public function testGetSucceeds() {
        $curl = new Curl();
        $response = $curl->get('www.google.com');
        $this->assertEquals(200, $response->headers['Status-Code']);
        $this->assertContains('google', $response->body);
    }

    public function testGetFails() {
        $curl = new Curl();
        try {
            $response = $curl->get('diaewkaksdljf-invalid-url-dot-com.com');
        } catch (CurlException $e) {
            $this->assertEquals("CURLE_COULDNT_RESOLVE_HOST: Couldn't resolve host 'diaewkaksdljf-invalid-url-dot-com.com'", $e->getMessage());
            return;
        }
        $this->assertTrue(false);
    }
}