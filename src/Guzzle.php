<?php
namespace HttpClient;
# Use Guzzle to create requests
use GuzzleHttp\Client as Http;
/**
 *	@description	
 */
class Guzzle implements IHttpClient
{
    public  $timeout    =   10;
    private $options, $ch, $headers, $body, $endpoint;
	/**
	 *	@description	
	 */
	public function __construct($endpoint)
	{
        $this->endpoint =   $endpoint;
	}
	/**
	 *	@description	
	 */
	public function setService(string $service)
	{
        $this->endpoint .= $service;
        return $this;
	}
	/**
	 *	@description	
	 */
	public function addHeader($key, $value)
	{
        $this->headers[$key]    =   $value;
        return $this;
	}
	/**
	 *	@description	
	 */
	public function addAuth($token)
	{
        $this->addHeader('Authorization', 'Bearer '.$token);
        return $this;
	}
	/**
	 *	@description    
	 */
	public function addBody(array $array = null)
	{
        $this->body =   (!empty($this->body))? array_merge($this->body, $array): $array;
        return $this;
	}
	/**
	 *	@description	
	 */
	public function post()
	{
        return $this->createPostLike(__FUNCTION__);
	}
	/**
	 *	@description	
	 */
	public function patch()
	{
        return $this->createPostLike(__FUNCTION__);
	}
	/**
	 *	@description	
	 */
	public function get()
	{
        return $this->createGetLike(__FUNCTION__);
	}
	/**
	 *	@description	
	 */
	public function delete()
	{
        return $this->createGetLike(__FUNCTION__);
	}
	/**
	 *	@description	Creates a post-like request (patch and post)
	 */
	public function createPostLike($type)
	{
        return json_decode($this->init()->request(strtoupper($type), $this->endpoint, [
            'headers' => $this->headers,
            'form_params' => $this->body
        ])->getBody());
	}
	/**
	 *	@description	Creates a get-like request (get, delete)
	 */
	public function createGetLike($type)
	{
        if(!empty($this->body))
            $this->endpoint .=  '?'.http_build_query($this->body);
        
        return json_decode($this->init()->request(strtoupper($type), $this->endpoint, [
            'headers' => $this->headers
        ])->getBody(), 1);
	}
	/**
	 *	@description	Creates a new Guzzle instance
	 */
	public function init()
	{
        return new Http([
            'base_uri' => EA_SKYDESKS_ENDPOINT,
            'timeout'  => $this->timeout
        ]);
	}
}