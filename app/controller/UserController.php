<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Core\Token;
use App\Model\UserModel;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;

class UserController extends BaseController
{

    public function __construct(GemRequest $request)
    {
        parent::__construct($request);
    }

    public function register(): JsonResponse
    {
        if (!$this->validatePosts(['email' => 'email', 'password' => 'string']) || !$this->validateStringPosts(['password' => 'min-5|max'])) {
            return $this->response;
        }
        $userModel = new UserModel();
        if (!$this->mapPostToObject(['email' => 'email', 'password' => 'password'], $userModel)) {
            return $this->response;
        }
        if (!$userModel->register()) {
            return $this->response->internalError($userModel->getError());
        }
        return $this->response->created($userModel, 1, 'Registerirung ist erfolged');
    }

    public function loginByEmail(): JsonResponse
    {
        if (!$this->validatePosts(['email' => 'email', 'password' => 'string'])) {
            return $this->response;
        }
        $userModel = new UserModel();
        if (!$this->mapPostToObject(['email' => 'email'], $userModel)) {
            return $this->response;
        }      

        $result = $userModel->loginByEmail($this->request->post['password']);/**@phpstan-ignore-line */
        if (!$result) {
            return $this->response->unauthorized('Benutzname oder Kennwort ist falsch');
        }
        $token = new Token($this->request);
        return $token->createToken($userModel->id);
    }

    public function loginByToken():JsonResponse
    {
        $token_in_header = $this->request->authorizationHeader;
        
        //step 1: check if token in header exists
        if(!$token_in_header || strlen($token_in_header) < 30 )/**@phpstan-ignore-line */
        {
            return $this->response->forbidden('no token in header');
        }
        //step 2: validate Token
        $tokenClass = new Token($this->request);
        if(!$tokenClass->validate())
        {
            return $this->response->forbidden($tokenClass->error);
        }
        
        return $this->response->success($tokenClass->user_id);
    }
}
