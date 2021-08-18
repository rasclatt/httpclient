<?php
namespace HttpClient;
/**
 *	@description	
 */
interface IStream
{
    public function doService(string $service);
    
    public function setMethod(string $method);
    
    public function transmit();
    
    public function getResults();
    
    public function setBody(array $attr, $json = false);
    
    public function setHeader($k, $v);
    
    public function setEndpoint(string $endpoint);
}