<?php
namespace App\Core;
 
use GemLibrary\Helper\WebHelper;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\GemToken;
use GemLibrary\Http\JsonResponse;
 
/**
 * @function createToken(int $user_id);
 * @function verify():bool
 * @function renewToken:JsonResponse
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
        $pureToken = $this->getPureTokenFromHeader();
        if($pureToken)
        {
            $this->setToken($pureToken);
        }
 
    }
 
    public function createToken(int $user_id):JsonResponse
    {
        $token = $this->create($user_id,$_ENV['TOKEN_VALIDATION_IN_SECONDS']);
        return $this->response->success($token,1,'token generated successfully');
    }
 
 
    public function renewToken(): JsonResponse
    {
        $tokenString = $this->renew($_ENV['TOKEN_VALIDATION_IN_SECONDS']);
        if(!$tokenString)
        {
            return $this->response->internalError('please try again');
        }
        return $this->response->success($tokenString,1,'successfully token renewd');
    }
 
 
    private function getPureTokenFromHeader():null|string
    {
        $token_in_header = $this->request->authorizationHeader;
        if(!is_string($token_in_header)   || strlen($token_in_header) < 30)
        {
            return null;
        }
        $token_in_header = WebHelper::BearerTokenPurify($token_in_header);
        if(!$token_in_header)
        {
            return null;
        }
        return $token_in_header;
    }
 
}