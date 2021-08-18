<?php
namespace HttpClient;
/**
 *	@description	
 */
class Stream extends \Nubersoft\nApp implements IStream
{
    private $endpoint, $headers, $method, $attributes, $body, $response, $final, $response_headers;
	/**
	 *	@description	
	 */
	public	function __construct($method = 'POST')
	{
        $this->method   =   strtoupper($method);
        return $this;
	}
	/**
	 *	@description	
	 */
	public function setEndpoint(string $endpoint)
	{
        $this->endpoint =   $endpoint;
        return $this;
	}
	/**
	 *	@description	
	 */
	public	function setMethod(string $method)
	{
        $this->method   =   strtoupper($method);
        return $this;
	}
	/**
	 *	@description	
	 */
	public	function doService(string $service)
	{
        $this->endpoint .=   '/'.ltrim($service, '/');
        return $this;
	}
	/**
	 *	@description	
	 */
	public	function setHeader($k, $v)
	{
        $this->headers[]    =   "{$k}: {$v}";
        return $this;
	}
	/**
	 *	@description	
	 */
	public	function setBody(array $attr, $json = false)
	{
        if($json)
            $this->setHeader('Content-Type', 'application/json');
        else
            $this->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        $this->body =   ($json)? json_encode($attr) : http_build_query($attr);
        return $this;
	}
	/**
	 *	@description	
	 */
	public	function transmit()
	{
        $this->final = [
            'http' => [
                'header'  => implode(PHP_EOL, $this->headers),
                'method'  => $this->method,
                'content' => $this->body
            ]
        ];
        
        $this->response   =   file_get_contents($this->endpoint, false, stream_context_create($this->final));
        
        $this->response_headers =   $http_response_header;
        
        return $this;
	}
	/**
	 *	@description	
	 */
	public	function getResults($decode = true, $func = false)
	{
        if(empty($this->response))
            return false;
        
        if(is_callable($func))
            return ($decode)? $func(json_decode($this->response, 1)) : $func($this->response);
        
        return ($decode)? json_decode($this->response, 1) : $this->response;
	}
	/**
	 *	@description	
	 */
	public function getByPost(array $arr = null, array $headers = null)
	{
        $dheaders    =   [
            "Content-Type: application/x-www-form-urlencoded"
        ];
        
        if(is_array($headers)) {
            $dheaders   =   array_unique(array_merge($dheaders, $headers));
        }
        
        if(!empty($arr))
            $arr    =   http_build_query($arr);
        
        $arr = [
            'http' => [
                'header'  => implode(PHP_EOL, $dheaders),
                'method'  => 'POST',
                'content' => $arr
            ]
        ];
        
        return json_decode(file_get_contents($this->endpoint, false, stream_context_create($arr)), 1);
	}
}