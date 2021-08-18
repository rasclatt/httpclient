<?php
namespace HttpClient;
/**
 *	@description	
 */
class Model
{
    protected $headers, $body, $service, $type, $resp_headers;
	/**
	 *	@description	
	 */
	public	function __construct(string $endpoint = null)
	{
        $this->setEndpoint($endpoint);
	}
	/**
	 *	@description	
	 */
	public function setEndpoint(string $endpoint = null)
	{
        $this->endpoint =   $endpoint;
        return $this;
	}
	/**
	 *	@description	
	 */
	public function addHeader(string $k, string $v)
	{
        $this->headers[strtolower($k)]    =   "{$k}: {$v}";
        return $this;
	}
	/**
	 *	@description	
	 */
	public function setService(string $service)
	{
        $this->service  =   $service;
        return $this;
	}
	/**
	 *	@description	
	 */
	public function setBody($body)
	{
        $this->body =   $body;
        return $this;
	}
	/**
	 *	@description	
	 */
	public function setTransmitType($type = 'POST')
	{
        $this->type =   strtoupper($type);
        return $this;
	}
	/**
	 *	@description	
	 */
	public function headerSet(string $name)
	{
        return ($this->headers[strtolower($name)])?? false;
	}
	/**
	 *	@description	
	 */
	public function post($json = true)
	{
        $this->setTransmitType('post');
        $this->body =   ($json)? json_encode($this->body) : http_build_query($this->body);
        if(!$this->headerSet('content-type')) {
            $this->addHeader('Content-Type', 'application/'.(($json)? 'json' : 'x-www-form-urlencoded'));
        }
        $this->addHeader('Content-Length', strlen($this->body));
        return $this->getResponse($json);
        
	}
	/**
	 *	@description	
	 */
	public function get($json = true)
	{
        $this->setTransmitType('get');
        $this->body =   (!empty($this->body) && is_array($this->body))? http_build_query($this->body) : '';
        if(!$this->headerSet('content-type') && $json)
            $this->addHeader('Content-Type', 'application/json');
        $this->addHeader('Content-Length', strlen($this->body));
        return $this->appendService((!empty($this->body))? "?{$this->body}" : '')->getResponse($json);
	}
	/**
	 *	@description	
	 */
	public function appendService(string $str)
	{
        $this->service  .=  $str;
        return $this;
	}
	/**
	 *	@description	
	 */
	protected function getContextStreamOpts(array $moreopts = null)
	{
        $opts   =   [
            'http' => [
                'method'  => $this->type,
                'content' => $this->body,
                'header'  => $this->headers
            ]
        ];
        
        return $opts;
	}
	/**
	 *	@description	
	 */
	public function getResponse($decode = true, $array = true)
	{
        $this->final_endpoint   =   $this->endpoint.$this->service;
        $context = stream_context_create($this->getContextStreamOpts());
        $fetch  =   file_get_contents($this->final_endpoint, false, $context);
        $this->resp_headers =   $http_response_header;
        $this->data = ($decode)? json_decode($fetch, $array) : $fetch;
        
        return $this;
	}
	/**
	 *	@description	
	 */
	public function getResponseHeaders()
	{
        return $this->resp_headers;
	}
	/**
	 *	@description	
	 */
	protected function buildHeaders()
	{
        return implode(PHP_EOL, $this->headers);
	}
	/**
	 *	@description	
	 */
	public function __call($method, $args = false)
	{
        if(preg_match('/^get/', $method)) {
            if(strtolower($method) == 'getall')
                return $this->data;
            
            $key    =   preg_replace('/^get/', '', $method);
            return ($this->data[$key])?? false;
        }
        return false;
	}
	/**
	 *	@description	
	 */
	public function __toString()
	{
        return json_encode($this->data);
	}
}