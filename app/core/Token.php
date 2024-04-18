<?php
namespace App\Core;

use GemLibrary\Helper\WebHelper;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\GemToken;
use GemLibrary\Http\JsonResponse;

class Token extends GemToken {

    private GemRequest $request;
    private JsonResponse $response;
    public function __construct(GemRequest $request)
    {
        parent::__construct($_ENV['TOKEN_SECRET'],"");
        $this->response = new JsonResponse();
        $this->role = "";
        $this->request = $request;
        $this->iss = "";
        //TODO:
        //In Production Remove following line of code 
        $this->ip = "213.213.213.213";
         //TODO:
        //In Production Remove following line of code 
        $this->userAgent = "Some_User_Agent";

        $this->type = "";
        //TODO:
        //In Production Uncomment following codes 
        //$this->userAgent = $this->request->userMachine;
        //$this->ip = $this->request->remoteAddress;

    }

    public function createToken(int $user_id):JsonResponse
    {
        $token = $this->create($user_id,$_ENV['TOKEN_VALIDATION_IN_SECONDS']);
        return $this->response->success($token,1,'token generated successfully');
    }

    public function validate():bool
    {
        $token_in_header = $this->request->authorizationHeader;
        if(!is_string($token_in_header)   || strlen($token_in_header) < 30)
        {
            return false;
        }
        $token_in_header = WebHelper::BearerTokenPurify($token_in_header);
        if(!$token_in_header)
        {
            return false;
        }
        return $this->verify($token_in_header);
    }
}