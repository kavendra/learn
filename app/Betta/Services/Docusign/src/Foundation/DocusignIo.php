<?php

namespace Betta\Docusign\Foundation;

abstract class DocusignIo
{
    /**
     * Make a Request
     *
     * @param  string $url
     * @param  string $method
     * @param  array  $headers
     * @param  array  $params
     * @param  array  $data
     * @return Object
     */
    abstract function makeRequest($url, $method = 'GET', $headers = array(), $params = array(), $data = NULL);
}
