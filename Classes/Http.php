<?php
/**
 * Http class
 */

namespace App;

/**
 * This class allows to make easily perform HTTP requests
 * The request is built with the request method, which then returns a Response object containing the HTTP response
 * 
 * @see Request
 * @see Response
 */
class Http { 
        
    /**
     * Sends an HTTP request to the given URI
     *
     * @param  string $url The request URL
     * @param  array $params An array of request arguments
     *               - method
     *               - timeout
     *               - headers 
     *               - body
     *               - ssl
     * 
     * @return Response|null The request's response or null if the request is invalid
     */
    public function request($url, $params = []): ?Response
    {
        if(!is_array($params))
            return null;

        if(filter_var($url, FILTER_VALIDATE_URL) === false)
            return null;

        $request = new Request();

        $request->setUrl($url)
                ->setMethod($params['method'] ?? 'GET')
                ->setBody($params['body'] ?? [])
                ->setTimeout($params['timeout'] ?? 60)
                ->addHeaders($params['headers'] ?? '');

        return $request->run();
    }

    /**
     * Parses a string or an array of headers into a key/value array
     * If it is a string, each header must be separated by a "\n"
     * 
     * @param  mixed An array or a string containing the headers
     * 
     * @return array A key/value array of headers
     */
    public static function parseHeaders($headers)
    {
        $return = [];

        if(is_array($headers)) {
            foreach($headers as $key => $value) {
                $return[strtolower($key)] = $value;
            }
        }
        else {
            foreach(explode("\n", $headers) as $i => $h) {
                $h = explode(':', $h, 2);

                if (count($h) > 1) {
                    $key = strtolower($h[0]);

                    if (!isset($return[$key]))
                        $return[$key] = trim($h[1]);
                    else
                        $return[$key] = array_merge([$return[$key]], [trim($h[1])]);
                }
                else if (count($h) == 1 && trim($h[0]) != '') {
                    $return[] = trim($h[0]);
                }
            }
        }

        return $return;
    }
}