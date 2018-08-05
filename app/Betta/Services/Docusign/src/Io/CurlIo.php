<?php

namespace Betta\Docusign\Io;

use Betta\Docusign\Foundation\DocusignIo;
use Betta\Docusign\Exceptions\IoException;

class CurlIo extends DocusignIo
{

    /**
     * SSL-verify the Peers
     *
     * @var boolean
     */
    protected $verifyPeers = false;


    /**
     * Interface method to make GET requests
     *
     * @param  string $url
     * @param  array  $headers
     * @param  array  $params
     * @param  mixed  $data
     * @return Object
     */
    public function get($url, $headers = array(), $params = array(), $data = NULL)
    {
        return $this->makeRequest($url, 'GET', $headers, $params, $data );
    }


    /**
     * Interface method to make POST requests
     *
     * @param  string $url
     * @param  array  $headers
     * @param  array  $params
     * @param  mixed  $data
     * @return Object
     */
    public function post($url, $headers = array(), $params = array(), $data = NULL)
    {
        return $this->makeRequest($url, 'POST', $headers, $params, $data );
    }


    /**
     * Interface method to make PUT requests
     *
     * @param  string $url
     * @param  array  $headers
     * @param  array  $params
     * @param  mixed  $data
     * @return Object
     */
    public function put($url, $headers = array(), $params = array(), $data = NULL)
    {
        return $this->makeRequest($url, 'PUT', $headers, $params, $data );
    }


    /**
     * Interface method to make DELETE requests
     *
     * @param  string $url
     * @param  array  $headers
     * @param  array  $params
     * @param  mixed  $data
     * @return Object
     */
    public function delete($url, $headers = array(), $params = array(), $data = NULL)
    {
        return $this->makeRequest($url, 'DELETE', $headers, $params, $data );
    }



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
    public function makeRequest($url, $method = 'GET', $headers = array(), $params = array(), $data = NULL)
    {
        # Init response
        $response;

        # Before we proceed, make sure we are good
        $method = $this->validateMethod($method);

        # set the URL
        $curl = $this->getCurlUrl($url, $params);

        # set the Basic Options
        $this->setSharedCurlOptions($curl, $headers);

        # set the Method Options
        $this->setMethodCurlOptions($curl, $method, $data);

        # Make the CuRL call
        try{
            # fetch result
            $result = curl_exec( $curl );

            # if there is a hint of error, throw an exceptions
            if( curl_error($curl) != '' ){
                throw new IoException(curl_error($curl));
            }

            # decode to Object
            $jsonResult = json_decode($result);

            # of non-empty
            $response   = (!is_null($jsonResult)) ? $jsonResult : $result;

            } catch(Exception $e) {
            # Otherwise,
            # Throw new Exception
            throw new IoException($e);
        }

        # Close connection
        curl_close($curl);

        # validate and return
        return $this->validateResponse( $response );
    }


    /**
     * Prepare CuRL URL
     *
     * @param  string $url
     * @param  array  $params
     * @return string
     */
    protected function getCurlUrl( $url, $params = array())
    {
        if ( sizeof($params) > 0 ){
            $curl = curl_init($url . '?' . http_build_query( $params, NULL, '&') );
        } else {
            $curl = curl_init($url);
        }

        return $curl;
    }


    /**
     * Set Shared CURL options
     *
     * @param resource $curl
     * @param array    $headers
     * @return Void
     */
    protected function setSharedCurlOptions($curl, $headers = array())
    {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        # return the transfer as a string
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        # SSL-verify peers
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->verifyPeers);
    }


    /**
     * Set HTTP-specific CUrl options
     *
     * @param resource $curl
     * @param string   $method
     * @param mixed    $data
     */
    protected function setMethodCurlOptions($curl, $method, $data = null)
    {
        switch ( strtoupper($method) ){
            case 'POST':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;

            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;

            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;

            default:
                break;
        }
    }


    /**
     * Validate Response
     *
     * @param  mixed $response
     * @return Object if good, null if does not exists, exception on error
     */
    protected function validateResponse( $response )
    {
        if ( is_array($response) AND array_key_exists('errorCode', $response) ){
            # there is an error in response
            throw new IoException($response['message'], $response['errorCode']);
        }

        if( is_object($response) AND get_class($response) === 'stdClass' AND property_exists( $response, 'errorCode') ){
            # the request came throw, but resulted in shall we say, being is stupid in DS' eyes
            throw new IoException($response->message, $response->errorCode);
        }

        return $response;
    }


    /**
     * Make sure the method is good before doind anything
     *
     * @param  string $method
     * @return string|Exception
     */
    private function validateMethod( $method = null)
    {
        if (!in_array(strtoupper($method), ['GET','POST','PUT','DELETE'])){
            throw new IoException('Method not supported', 500);
        }
        return $method;
    }
}
