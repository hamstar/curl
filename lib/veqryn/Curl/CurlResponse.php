<?php

namespace veqryn\Curl;

/**
 * Class CurlResponse
 * Parses the response from a Curl request into an object containing
 * the response body and an associative array of headers
 *
 * @package Curl
 * @author Sean Huber <shuber@huberry.com>
 **/
class CurlResponse {

    /**
     * The body of the response without the headers block
     *
     * @var string
     **/
    public $body = '';

    /**
     * An associative array containing the response's headers
     *
     * @var array
     **/
    public $headers = array();

    /**
     * Accepts the result of a curl request as a string
     *
     * <code>
     * $response = new CurlResponse(curl_exec($curl_handle));
     * echo $response->body;
     * echo $response->headers['Status'];
     * </code>
     *
     * @param string $response
     **/
    public function __construct($response) {
        if (empty($response)) {
            return;
        }
        # Headers regex
        $pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';

        # Extract headers from response
        preg_match_all($pattern, $response, $matches);
        $headers_string = array_pop($matches[0]);
        $headers = explode("\r\n", str_replace("\r\n\r\n", '', $headers_string));

        # Inlude all received headers in the $headers_string
        while (count($matches[0])) {
            $headers_string = array_pop($matches[0]) . $headers_string;
        }

        # Remove all headers from the response body
        $this->body = str_replace($headers_string, '', $response);

        # Extract the version and status from the first header
        $version_and_status = array_shift($headers);
        preg_match_all('#HTTP/(\d\.\d)\s((\d\d\d)\s((.*?)(?=HTTP)|.*))#', $version_and_status, $matches);
        $this->headers['Http-Version'] = array_pop($matches[1]);
        $this->headers['Status-Code'] = array_pop($matches[3]);
        $this->headers['Status'] = array_pop($matches[2]);

        # Convert headers into an associative array
        foreach ($headers as $header) {
            preg_match('#(.*?)\:\s(.*)#', $header, $matches);
            $this->headers[$matches[1]] = $matches[2];
        }
    }

    /**
     * Returns the response body
     *
     * <code>
     * $curl = new Curl();
     * $response = $curl->get('google.com');
     * echo $response;  # => echo $response->body;
     * </code>
     *
     * @return string
     **/
    public function __toString() {
        return $this->body;
    }

}