<?php
namespace App\Core;
 
use GemLibrary\Helper\WebHelper;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\GemToken;
use GemLibrary\Http\JsonResponse;

/**
 * @function createToken(int $user_id):JsonResponse
 * @function validate():bool
 * @function renewToken():JsonResponse
 */
 
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
        $tokenString = $this->getPureTokenFromHeader();
        if(!$tokenString)
        {
            return false;
        }
        return $this->verify($tokenString);
    }
 
    public function renewToken(): JsonResponse
    {
        //TODO::
        //1: check if token ist kurz to ablauf
        //2: if token hat genug zeit ; answer => token hat noch 5 minuten zeit ,
        //3: run following code
        $tokenString = $this->getPureTokenFromHeader();
        if(!$tokenString)
        {
            return $this->response->forbidden('not token found in header');
        }
        if(!$this->verify($tokenString))
        {
            return $this->response->unauthorized($this->error);
        }
       
        $tokenString = $this->renew($tokenString,$_ENV['TOKEN_VALIDATION_IN_SECONDS']);
        if(!$tokenString)
        {
            return $this->response->internalError('please try again');
        }
        return $this->response->success($tokenString,1,'successfully generated new token');
    } 
 
    private function getPureTokenFromHeader():false|string
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
        return $token_in_header;
    }   
}