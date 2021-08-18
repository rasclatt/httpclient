<?php
namespace HttpClient;
/**
 *	@description	
 */
interface IHttpClient
{
	/**
	 *	@description	
	 */
	public function setService(string $service);
	/**
	 *	@description	
	 */
	public function addHeader($key, $value);
	/**
	 *	@description	
	 */
	public function addAuth($token);
	/**
	 *	@description	
	 */
	public function addBody(array $array = null);
	/**
	 *	@description	
	 */
	public function post();
	/**
	 *	@description	
	 */
	public function patch();
	/**
	 *	@description	
	 */
	public function get();
	/**
	 *	@description	
	 */
	public function delete();
}