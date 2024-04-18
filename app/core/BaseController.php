<?php
namespace App\Core;

use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;

/**
 * @protected  GemRequest $request
 * @protected  JsonResponse $response
 * @protected  null|string  $error 
 * @function   validatePosts(array $post_schema):bool
 */
class BaseController {
    protected GemRequest $request;
    protected JsonResponse $response;
    protected ?string $error;

    public function __construct(GemRequest $request)
    {
        $this->error = null;
        $this->request = $request;
        $this->response = new JsonResponse();
    }

    /**
     * @param array<string> $post_schema  Define Post Schema to validation
     * @return bool
     * validatePosts(['email'=>'email' , 'id'=>'int' , '?name' => 'string'])
     * @help : ?name means it is optional
     */
    protected function validatePosts(array $post_schema):bool{
        if(!$this->request->definePostSchema($post_schema))
        {
            $this->response->badRequest($this->request->error);
            return false;
        }
        return true;
    }

    /**
     * Validates string lengths in a dictionary against min and max constraints.
     *
     * @param array<string> $post_schema A dictionary where keys are strings and values are strings in the format "key:min-value|max-value" (optional).
     * @return bool True if all strings pass validation, False otherwise.
     */
    protected function validateStringPosts(array $post_schema):bool
    {
        if(!$this->request->validateStringPosts($post_schema))
        {
            $this->response->badRequest($this->request->error);
            return false;
        }
        return true;
    }

    /**
     * @param array<string> $posts_to_properties
     */
    protected function mapPostToObject(array $posts_to_properties ,object $object):bool
    {
        $ecxeptions = [];
        foreach($posts_to_properties as $incommingPost=>$propertyName)
        {
            try{
                $object->$propertyName = $this->request->post[$incommingPost];
            }
            catch(\Exception $e)
            {
                $ecxeptions[] = $e->getMessage();
            }
        }
        if(count($ecxeptions))
        {
            $final_error = "";
            foreach($ecxeptions as $error)
            {
                $final_error .= $error.', '; 
            }
            $this->response->badRequest($final_error);
            return false;
        }
        return true;
    }

}