<?php
namespace Enersales;

use GuzzleHttp\Client;

class EnersalesClient {
	
	private $env;
	private $enersalesDomain = 'enersales.com';
	private $api = 'v1';
	private $endpoint;
	private $credentials;
	private $signature;
	private $client;
	private $auth;
	
	//URI-s:
	private $urlAuthLogin = "auth/login";
	private $urlAuthRefresh = "auth/refresh";
	private $urlFileUpload = "files/upload";
	private $urlDealCreate = "deals";
	private $urlDealUpdate = "deals";
	private $urlPersonCreate = "persons";
	private $urlPersonUpdate = "persons";
	private $urlOrganizationCreate = "organizations";
	private $urlOrganizationUpdate = "organizations";
	private $urlProductCreate = "products";
	private $urlProductUpdate = "products";
	private $urlDealSearch = "deals";
	private $urlPersonSearch = "persons";
	private $urlOrganizationSearch = "organizations";
	private $urlProductSearch = "products";
    private $urlGetDataSchema = "data-schemes";
    private $urlGetForm = "forms/get";

    private $fileChunkLength = 2097152;

    public function __construct(array $args){
		if( isset($args['env']) ){
			$this->env = $args['env'];
		}
		
		if( isset($args['api']) ){
			$this->api = $args['api'];
		}
		
		$this->instance = $args['instance'];
		$this->access_key = $args['access_key'];
		$this->secret_key = $args['secret_key'];
		$this->nonce = time().generateRandomString();
		$this->signature = md5($this->instance.$this->access_key.$this->nonce.$this->secret_key);
		$this->endpoint = "https://".(!empty($this->env) ? $this->env."-" : "").$this->instance.".".$this->enersalesDomain."/api/".$this->api."/";
	
		$this->client = new Client([
			'base_uri'=> $this->endpoint
		]);
		
		$this->authenticate();
	}
	
	public function authenticate(){
		$form_params = [
			'access_key'=>$this->access_key,
			'nonce'=>$this->nonce,
			'signature'=>$this->signature,
		];
		$options = [
			'form_params'=>$form_params
		];
		$response = $this->client->post($this->urlAuthLogin, $options);
        $responseRaw = $response->getBody()->getContents();
		$responseBody = json_decode($responseRaw);
		
		if(empty($responseBody)){
			throw new \Exception($responseRaw);
		}
		$this->auth = $responseBody;
		
		return $responseBody;
	
	}
	
	public function refreshToken(){
		
		if(empty($this->auth)){
			throw new \Exception('No authentication data still available');
		}
		
		$form_params = [
			'refresh_token'=> $this->auth->refresh_token,
		];
		$options = [
			'form_params'=>$form_params
		];
		$response = $this->client->post($this->urlAuthRefresh, $options);
		
		$response = json_decode($response->getBody()->getContents());
		
		if(isset($response->error) && $response->error > 0){
			throw new \Exception($response->messages[0]);
		}
		
		$this->auth->access_token = $response->access_token;
		$this->auth->expiration = $response->expiration;
		
		return $response;
	}
	
	public function getToken(){
		return $this->auth;
	}
	
	public function isAccessTokenExpired(){
		$expiration = json_decode(base64_decode($this->getToken()->access_token))->expiration;
		if($expiration > time()){
			return false;
		}
		return true;
	}
	
	public function isRefreshTokenExpired(){
		$expiration = json_decode(base64_decode($this->getToken()->refresh_token))->expiration;
		if($expiration > time()){
			return false;
		}
		return true;
	}
	
	public function request($method, $uri, $options){
		if(empty($this->auth)){
			throw new \Exception('No authentication data still available');
		}
		$options['headers']['Authorization'] = 'Bearer '.$this->auth->access_token;
		return $this->client->request($method, $uri, $options);
	}
	
	public function fileUpload(array $data){
		$partLength = $this->fileChunkLength;
		$file_size = filesize($data['file']);
		$parts = ceil($file_size / $partLength);
		
		if($parts > 1){
			return $this->chunkUpload($data);
		}
		
		return $this->simpleUpload($data);
	}
	
	public function chunkUpload(array $data){
		$queryString = [];
		foreach($data as $key=>$value){
			switch($key){
				case "deal_id":
				case "person_id":
				case "organization_id":
					$queryString[$key] = $value;
				break;
			}
		}
		
		$partLength = $this->fileChunkLength;
		$f = fopen($data['file'], 'r');
		$file_size = filesize($data['file']);
		$parts = ceil($file_size / $partLength);
		
		for($i=0; $i < $parts; $i++){
			
			$partContent = fread($f, $partLength);
			$multipart = [
				[
					'name'=>'dzuuid',
					'contents'=> '2d088312-2e2a-48b7-a3cb-8e12ffdaf330',
				],
				[
					'name'=>'dzchunkindex',
					'contents'=> $i,
				],
				[
					'name'=>'dztotalfilesize',
					'contents'=> $file_size,
				],
				[
					'name'=>'dzchunksize',
					'contents'=> $partLength,
				],
				[
					'name'=>'dztotalchunkcount',
					'contents'=> $parts,
				],
				[
					'name'=>'dzchunkbyteoffset',
					'contents'=> 0,
				],
				[
					'name'=>'file',
					'filename'=> basename($data['file']),
					'contents'=> $partContent,
				]
			];
			
			$options = [
				'query' => $queryString,
				'multipart'=> $multipart
			];
			
			$response = $this->request('POST', $this->urlFileUpload, $options);
			$response = json_decode($response->getBody()->getContents());
			
		}
		
		fclose($f);
		
		return $response;
	}
	
	public function simpleUpload(array $data){
		$queryString = [];
		foreach($data as $key=>$value){
			switch($key){
				case "deal_id":
				case "person_id":
				case "organization_id":
					$queryString[$key] = $value;
				break;
			}
		}
		
		$multipart = [
			[
				'name'=>'file',
				'filename'=> basename($data['file']),
				'contents'=> fopen($data['file'], 'r'),
			]
		];
		
		$options = [
			'query' => $queryString,
			'multipart'=> $multipart
		];
		
		$response = $this->request('POST', $this->urlFileUpload, $options);
		$response = json_decode($response->getBody()->getContents());
		
		return $response;
	}
	
	public function create(string $entityType, array $data){
		
		$url = "";
		switch(strtolower($entityType)){
			case "deals":
				$url = $this->urlDealCreate;
			break;
            case "persons":
                $url = $this->urlPersonCreate;
            break;
            case "organizations":
                $url = $this->urlOrganizationCreate;
            break;
            case "products":
                $url = $this->urlProductCreate;
            break;
		}
		
		$options = [
			'form_params'=>$data
		];
		$response = $this->request('POST', $url, $options);
        $responseRaw = $response->getBody()->getContents();
		$responseBody = json_decode($responseRaw);
		
		if(empty($responseBody)){
			throw new \Exception($responseRaw);
		}
		
		return $responseBody;
	}

	public function update(string $entityType, $id, array $data){
        $url = "";
        switch(strtolower($entityType)){
            case "deals":
                $url = $this->urlDealUpdate;
                break;
            case "persons":
                $url = $this->urlPersonUpdate;
                break;
            case "organizations":
                $url = $this->urlOrganizationUpdate;
                break;
            case "products":
                $url = $this->urlProductUpdate;
                break;
        }
        $url = $url."/".$id;

        $options = [
            'form_params'=>$data
        ];
        $response = $this->request('PATCH', $url, $options);
        $responseRaw = $response->getBody()->getContents();
        $responseBody = json_decode($responseRaw);

        if(empty($responseBody)){
            throw new \Exception($responseRaw);
        }

        return $responseBody;
	}

    public function getDataScheme(array $data){
        $url = $this->urlGetDataSchema;
        $options = [
            'query'=> $data
        ];
        $response = $this->request('GET', $url, $options);
        $responseRaw = $response->getBody()->getContents();
        $responseBody = json_decode($responseRaw);

        if(empty($responseBody)){
            throw new \Exception($responseRaw);
        }

        return $responseBody;
    }
    public function getForm($formCode, array $data){
        $url = $this->urlGetForm;

        $url = $url."/".$formCode;

        $options = [
            'query'=> $data
        ];

        $response = $this->request('GET', $url, $options);
        $responseRaw = $response->getBody()->getContents();
        $responseBody = json_decode($responseRaw);

        if(empty($responseBody)){
            throw new \Exception($responseRaw);
        }

        return $responseBody;
    }

    public function search($entityType, array $data){
        $url = "";
        switch(strtolower($entityType)){
            case "deals":
                $url = $this->urlDealSearch;
                break;
            case "persons":
                $url = $this->urlPersonSearch;
                break;
            case "organizations":
                $url = $this->urlOrganizationSearch;
                break;
            case "products":
                $url = $this->urlProductSearch;
                break;
        }

        $options = [
            'query'=> $data
        ];
        $response = $this->request('GET', $url, $options);
        $responseRaw = $response->getBody()->getContents();
        $responseBody = json_decode($responseRaw);

        if(empty($responseBody)){
            throw new \Exception($responseRaw);
        }

        return $responseBody;
    }
}
