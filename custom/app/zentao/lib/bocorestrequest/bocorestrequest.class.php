<?php

/**feature-1245**/
class bocorestrequest {
    private $SSL;
    
	protected $url;
	protected $verb;
	protected $requestBody;
	protected $requestLength;
	protected $username;
	protected $password;
	protected $acceptType;
	protected $responseBody;
	protected $responseInfo;
    private $cacert;
    protected $CA;
    
	
	public function __construct ($url = null, $verb = 'GET', $requestBody = null) {
        $this->cacert = getcwd() . '/cacert.pem'; //CA根证书  
        $this->SSL = substr($url, 0, 8) == "https://" ? true : false;  
        $this->CA = false;
        
		$this->url				= $url;
		$this->verb				= $verb;
		$this->requestBody		= $requestBody;
		$this->requestLength	= 0;
		$this->username			= null;
		$this->password			= null;
		$this->acceptType		= 'application/json';
		$this->responseBody		= null;
		$this->responseInfo		= null;
		
		if ($this->requestBody !== null)
		{
			$this->buildPostBody();
		}
	}
	
	public function flush () {
		$this->requestBody		= null;
		$this->requestLength	= 0;
		$this->verb				= 'GET';
		$this->responseBody		= null;
		$this->responseInfo		= null;
	}
	
	public function execute () {
		$ch = curl_init();
		$this->setAuth($ch);
		
		try
		{
			switch (strtoupper($this->verb))
			{
				case 'GET':
					$this->executeGet($ch);
					break;
				case 'POST':
					$this->executePost($ch);
					break;
				case 'PUT':
					$this->executePut($ch);
					break;
				case 'DELETE':
					$this->executeDelete($ch);
					break;
				default:
					throw new InvalidArgumentException('Current verb (' . $this->verb . ') is an invalid REST verb.');
			}
		}
		catch (InvalidArgumentException $e)
		{
			curl_close($ch);
			throw $e;
		}
		catch (Exception $e)
		{
			curl_close($ch);
			throw $e;
		}
		
	}
	
	public function buildPostBody ($data = null) {
		$data = ($data !== null) ? $data : $this->requestBody;
		
		if (!is_array($data))
		{
			throw new InvalidArgumentException('Invalid data input for postBody.  Array expected');
		}
		
		$data = http_build_query($data, '', '&');
		$this->requestBody = $data;
	}
	
	protected function executeGet ($ch) {		
		$this->doExecute($ch);	
	}
	
	protected function executePost ($ch) {
		if (!is_string($this->requestBody))
		{
			$this->buildPostBody();
		}
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestBody);
		curl_setopt($ch, CURLOPT_POST, 1);
		
		$this->doExecute($ch);	
	}
	
	protected function executePut ($ch) {
		if (!is_string($this->requestBody))
		{
			$this->buildPostBody();
		}
		
		$this->requestLength = strlen($this->requestBody);
		
		$fh = fopen('php://memory', 'rw');
		fwrite($fh, $this->requestBody);
		rewind($fh);
		
		curl_setopt($ch, CURLOPT_INFILE, $fh);
		curl_setopt($ch, CURLOPT_INFILESIZE, $this->requestLength);
		curl_setopt($ch, CURLOPT_PUT, true);
		
		$this->doExecute($ch);
		
		fclose($fh);
	}
	
	protected function executeDelete ($ch){
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		
		$this->doExecute($ch);
	}
	
	protected function doExecute (&$curlHandle){
		$this->setCurlOpts($curlHandle);
		$this->responseBody = curl_exec($curlHandle);
		$this->responseInfo	= curl_getinfo($curlHandle);
		
		curl_close($curlHandle);
	}
	
	protected function setCurlOpts (&$curlHandle){
        if ($this->SSL && $this->CA) {  
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, true);   // 只信任CA颁布的证书  
            curl_setopt($curlHandle, CURLOPT_CAINFO, $this->cacert); // CA根证书（用来验证的网站证书是否是CA颁布）  
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名，并且是否与提供的主机名匹配  
        } else if ($this->SSL && !$this->CA) {  
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书  
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名  
        }
        
		curl_setopt($curlHandle, CURLOPT_TIMEOUT, 10);
		curl_setopt($curlHandle, CURLOPT_URL, $this->url);
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array ('Accept: ' . $this->acceptType));
	}
	
	protected function setAuth (&$curlHandle){
		if ($this->username !== null && $this->password !== null)
		{
			curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
			curl_setopt($curlHandle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		}
	}
	
	public function getAcceptType (){
		return $this->acceptType;
	} 
	
	public function setAcceptType ($acceptType){
		$this->acceptType = $acceptType;
	} 
	
	public function getPassword (){
		return $this->password;
	} 
	
	public function setPassword ($password){
		$this->password = $password;
	} 
	
	public function getResponseBody (){
		return $this->responseBody;
	} 
	
	public function getResponseInfo (){
		return $this->responseInfo;
	} 
	
	public function getUrl (){
		return $this->url;
	} 
	
	public function setUrl ($url){
		$this->url = $url;
	} 
	
	public function getUsername (){
		return $this->username;
	} 
	
	public function setUsername ($username){
		$this->username = $username;
	} 
	
	public function getVerb (){
		return $this->verb;
	} 
	
	public function setVerb ($verb){
		$this->verb = $verb;
	} 
    
    public function getCacert() {
        return $this->cacert;
    }
    
    public function setCacert($cacert) {
        $this->cacert = $cacert;
    }
    
    public function getCA() {
        return $this->CA;
    }
    
    public function setCA($CA) {
        $this->CA = $CA;
    }
    
}
