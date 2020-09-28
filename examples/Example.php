<?php
namespace Enersales\Examples;
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../src/EnersalesClient.php';

class ExampleUnit {

    private $client;

    public function __construct(){
        // $credentials = json_decode(file_get_contents("./credentials/credentials-dev.json"), true); // Change this line to get your credentials
       
        $credentials = [
            'env'=>'dev',
            'instance'=>'core01',
            'access_key'=>'14ee5a59-c8ce-e604-3230-cedf9c605058',
            'secret_key'=>'n~Du2!8bwyPrrwhqBs~pBzt3!$+qHjh7!Nq=2koTrfFKjNIR0ynxFNRS-Q_hD!Fv01NeuO5OulE5QzeszymTxXtEk0r~U$LNgHPIz8HrdcpHYLlQmO2bKd9rL_bfc0c8',
        ];
    
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
            'titolo_opportunita'=>'Aldaro SDK FIRST TIME 3',
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
            'subobject_type'=>"cv",
            'deal_ids'=>['2351']
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

    public function getDataScheme(){
        $res = $this->client->getDataScheme([
            'object_type'=>'deals',
            'subobject_type'=>'default'
        ]);

        var_dump($res);
    }
    public function getForm(){
        $res = $this->client->getForm('default', [
            'object_type'=>'deals',
            "subobject_type"=>"cv",
            "layout"=>true
        ]);

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
    public function createActivity(){
        $res = $this->client->create('activities', [
            'note'=>"Activity from API Notes",
            'titolo'=>"Activity from",
            'type'=>0,
            'datatime_activity'=>'2020-09-22T13:00:00+02:00',
            'end_activity'=>'2020-09-22T14:00:00+02:00',
            'assigned_id'=>30,
            'done'=>30,
            'all_day'=>false,
            'deal_id'=>2351,
            'organization_id'=>18915,
            'person_ids'=>['1311']
        ]);

        var_dump($res);
    }
    public function updateActivity(){
        $res = $this->client->update('activities','2453', [
            'id'=>2453,
            'note'=>"Activity from API Notes",
            'titolo'=>"Activity from updated",
        ]);
        var_dump($res);
    }
    public function deleteActivity(){
        $res = $this->client->delete('2429');
        var_dump($res);
    }
    public function getActivity(){
        $res = $this->client->getActivity('2448');
        var_dump($res);
    }
    public function getAllActivity(){
        $res = $this->client->search('activities', [
            'date'=>'2020-09-15',
            //'person_ids'=>[1261,1997]
            //'titolo'=>'testactivities',
            //'deal_id'=>'2852',
            //'organization_id'=>'18140',
            //'done'=>true
//            'date_range'=>[
//                'start'=>'2020-09-15',
//                'end'=>'2020-09-16'
//            ]
        ]);

        var_dump($res);
    }

    public function getUser(){
        var_dump($this->client->get('users',"10"));
    }

    public function searchUser(){
        var_dump($this->client->search('users', [
            "nome"=>"Mario",
            "cognome"=>"Rossi",
            'email'=>'helpdesk@bluservice.it',
            'page'=>1,
            'limit'=>1
        ]));
    }
}
