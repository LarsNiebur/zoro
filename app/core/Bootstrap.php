<?php
namespace App\Core;

use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;
class Bootstrap {

    private GemRequest $request;
    private string $requested_service;
    private string $requested_method;
    
    public function __construct(GemRequest $request)
    {
        $this->request = $request;
        $this->setRequestdService();
    }

    public function runApp():void{
        $serviceInstance = false;
        $error = null;
        try{
            $service = 'App\\Service\\'.$this->requested_service;
            $serviceInstance = new $service($this->request);
        }catch(\Throwable $e){
            $error = $e->getMessage();
        }
        if($error)
        {
            $jsonResponse = new JsonResponse();
            $jsonResponse->notFound('requested service not found');
            $jsonResponse->show();
            die;
        }
        $method = $this->requested_method;
        $serviceInstance->$method()->show();
    }


    private function setRequestdService():void{
        $segments = explode('/',$this->request->requestedUrl);
        $this->requested_service = ucfirst($segments[$_ENV["SERVICE_IN_URL_SECTION"]]);
        $this->requested_method = $segments[$_ENV["METHOD_IN_URL_SECTION"]];
    }



    
}