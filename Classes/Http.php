<?php

namespace App;

class Http { 
        
    /**
     * Sends an HTTP request to the given URI
     *
     * @param  mixed $url The request URL
     * @param  mixed $params An array of request arguments
     *               - method
     *               - timeout
     *               - headers 
     *               - body
     *               - ssl
     * @return Response The request's response
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