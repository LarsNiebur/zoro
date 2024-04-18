<?php
namespace App\Service;
use App\Controller\UserController;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;

class Authenticate {

    private GemRequest $request;

    public function __construct(GemRequest $request)
    {
        $this->request = $request;
    }

    public function loginByEmail():JsonResponse
    { 
        $userController = new UserController($this->request);
        return $userController->loginByEmail();
    }

    public function register():JsonResponse
    {
       $userController = new UserController($this->request);
       return $userController->register(); 
    }

    public function loginByToken():JsonResponse
    {
        $userController = new UserController($this->request);
        return $userController->loginByToken();
    }

    public function renewToken():JsonResponse
    {
        //1: Token existiert
        //2: Token ist validiert
        //3: wenn vadidiert generiere neues Token mit extra Zeit
    }
}