<?php
namespace Enersales\Examples;
require __DIR__.'/../src/EnersalesClient.php';

class ExampleUnit {

    private $client;

    public function __construct(){
        $credentials = json_decode(file_get_contents("./credentials/credentials-dev.json"), true); // Change this line to get your credentials
        /*
			$credentials = [
				'instance'=>'your instance code',
				'access_key'=>'YOUR ACCESS KEY',
				'secret_key'=>'YOUR SECRET KEY',
			]
        */
        $this->client = new \Enersales\EnersalesClient($credentials);
    }
    
    public function refreshToken(){
        var_dump($this->client->getToken());
        $this->client->refreshToken();
    }
    
    public function fileUpload(){

        $res = $this->client->fileUpload([
            'file'=>__DIR__.'/fileUploadTest.xlsx',
            'deal_id'=> 2054,
            'person_id'=>'',
            'organization_id'=>'',
        ]);

        var_dump($res);
    }
    
    public function createDeal(){
        $res = $this->client->create('Deals', [
            'titolo_opportunita'=>'Test deal from the API TEST2',
            "visibility"=> 1,
            "data_lead"=> "2019-07-17T15:21:55+00:00",
            "user_id"=> 1,
            "stato_opportunita"=> "in corso ",
            "stage_id"=> 135,
            "person_id"=> "1318",
            "organization_id"=> "809",
            "valore"=> [
                "value" => "7777.00",
                "currency" => "EUR"
            ],
            "campaign_id"=> "3",
            "label_id"=> "3",
            "influencer"=> "21",
        ]);

        var_dump($res);
    }
    public function updateDeal(){
        $res = $this->client->update('Deals','2154', [
            'titolo_opportunita'=>'Test deal from the API 7777 [updated]',
            "stage_id"=> 135,
        ]);

    }

    public function createPerson(){
        $res = $this->client->create('Persons', [
            'age'=> 27,
            'nome'=> 'Test Name',
            'cognome'=> 'from API',
            'denominazione'=> 'ENERSALES API',
            'geolocation'=> 'Via roma 12, Napoli (Na), Italia',
            'insertedby'=> 1,
            'birthday'=> "2019-10-28T16:25:07.986Z",
            'user_id'=> 1,
            'start_work'=> "2019-11-30T16:25:11.673Z",
            'organization_id'=> 809,
        ]);
        var_dump($res);
    }
    public function updatePerson(){
        $res = $this->client->update('Persons','3772', [
            'id'=> '3772',
            'denominazione'=>'API update',
            'age'=> 27,
            'nome'=> 'API',
            'insertedby'=> 1,
            'birthday'=> "2019-10-28T16:25:07.986Z",
            'user_id'=> 1,
            'start_work'=> "2019-11-30T16:25:11.673Z",
            'organization_id'=> 809,
            'cognome'=> 'ENER UPDATE',
        ]);
        var_dump($res);
    }

    public function createOrganization(){
        $res = $this->client->create('Organizations', [
            'denominazione'=>'API ORG',
            'insertedby'=> 1,
            'user_id'=> 1,
            'note'=> "Test Notes API",
            'data_creazione_org'=> "2019-12-06T16:14:26.562Z",
        ]);

        var_dump($res);
    }
    public function updateOrganization(){
        $res = $this->client->update('Organizations','18861', [
            'denominazione'=>'API ORG UPDATE',
            'insertedby'=> 1,
            'user_id'=> 1,
            'note'=> "Test Notes API",
            'data_creazione_org'=> "2019-12-06T16:14:26.562Z",
        ]);

    }

    public function createProduct(){
        $res = $this->client->create('Products', [
            'name'=>'API PRODUCT UPDATE',
            'organization_id'=> 809,
            'owner'=> 1,
            'user_id'=> 1,
            'date'=> "2019-12-06T16:14:26.562Z",
            'tipologia_id'=> "Fisico",
            'categoria_id'=> 1,

        ]);

        var_dump($res);
    }
    public function updateProduct(){
        $res = $this->client->update('Products', '64', [
            'name' => 'API PRODUCT',
            'organization_id' => 809,
            'owner' => 1,
            'user_id' => 1,
            'date' => "2019-12-06T16:14:26.562Z",
            'tipologia_id' => "Fisico",
        ]);
        var_dump($res);

    }

    public function getForm(){
        $res = $this->client->getFormConfig('deals_add', []);

        var_dump($res);
    }

    public function searchPerson(){
        $res = $this->client->search('persons', [
            'persons'=>[
                "nome"=>"NewPERSON33",
//               "cognome"=>"NewPERSON33",
            ],
            'page'=>1,
            'limit'=>30
        ]);

        var_dump($res);
    }
}
