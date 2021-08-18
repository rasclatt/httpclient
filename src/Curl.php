<?php
namespace HttpClient;
/**
 *	@description	
 */
class Curl
{
    protected $options, $ch, $headers, $body, $endpoint, $response, $errors, $errorCode, $headersReturn;
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
	public function doService(string $service)
	{
        $this->endpoint .= $service;
        return $this;
	}
	/**
	 *	@description	
	 */
	public function addOption($key, $value)
	{
        $this->options[$key]    =   $value;
        return $this;
	}
	/**
	 *	@description	
	 */
	public function addHeader($value)
	{
        $this->headers[]    =   $value;
        return $this;
	}
	/**
	 *	@description	
	 */
	public function addAuth($token)
	{
        $this->addHeader('Authorization: Bearer '.$token);
        return $this;
	}
	/**
	 *	@description	
	 */
	public function addBody(array $array = null)
	{
        $this->body =   $array;
        return $this;
	}
	/**
	 *	@description	
	 */
	public function get()
	{
        return $this->query(__FUNCTION__, false);
	}
	/**
	 *	@description	
	 */
	public function post()
	{
        return $this->query(__FUNCTION__, false);
	}
	/**
	 *	@description	
	 */
	public function delete()
	{
        return $this->query(__FUNCTION__, false);
	}
	/**
	 *	@description	
	 */
	public function patch()
	{
        return $this->query(__FUNCTION__, false);
	}
	/**
	 *	@description	
	 */
	public function query(string $type, $send = 'json', $return = 'json', $poseAs = 'POST')
	{
        $type   =   strtoupper($type);
        $this->options[CURLOPT_URL]  =   $this->endpoint.(((($type == 'GET') || ($poseAs == 'GET')) && !empty($this->body))? '?'.http_build_query($this->body) : '');
        if(!empty($this->headers))
			$this->options[CURLOPT_HTTPHEADER]  =   $this->headers;
        $this->options[CURLOPT_CUSTOMREQUEST]   =   $type;
        $this->options[CURLOPT_RETURNTRANSFER]   =   true;
        $this->options[CURLOPT_FOLLOWLOCATION]  =   1;
		if($poseAs == 'POST' && !empty($this->body))
			$this->setPostFields($send);
        $this->ch =   curl_init();
		curl_setopt_array($this->ch, $this->options);
		$headers	=	[];
		curl_setopt($this->ch, CURLOPT_HEADERFUNCTION,
			function($curl, $header) use (&$headers)
			{
			$len = strlen($header);
			$header = explode(':', $header, 2);
			if (count($header) < 2) // ignore invalid headers
				return $len;
		
				$headers[strtolower(trim($header[0]))][] = trim($header[1]);
			
				return $len;
			}
		);
        $result = curl_exec($this->ch);
		$this->errors	=	curl_error($this->ch);
		$this->errorCode	=	curl_error($this->ch);
		$this->headersReturn	=	$headers;
        curl_close($this->ch);
		if(!empty($this->errors)) {
			throw new \Exception($this->errors, $this->errorCode);
		}
		$this->response = $result;
        return ($return == 'json')? json_decode($this->response, 1) : $this->response;
	}
	/**
	 *	@description	
	 *	@param	
	 */
	public function setPostFields($send)
	{
		$this->options[CURLOPT_POST]    =   (!empty($this->body))? count($this->body) : 0;
		$this->options[CURLOPT_POSTFIELDS]   =   ($send == 'json')? json_encode($this->body) : http_build_query($this->body);
		return $this;
	}
	/**
	 *	@description	
	 *	@param	
	 */
	public function getResponse()
	{
		return $this->response;
	}
	/**
	 *	@description	
	 *	@param	
	 */
	public function getErrors()
	{
		return $this->errors;
	}
	/**
	 *	@description	
	 *	@param	
	 */
	public function getDebug()
	{
		return [
			'options' => $this->options,
			'responseHeaders' => $this->headersReturn
		];
	}
}