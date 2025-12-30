<?php

namespace src\app\requests;

use src\support\Request as httpRequest;

abstract class Request
{

    function __invoke()
    {
    }

    /** @var array<string, string> */
    protected array $rules;

    /**
     * This method returns all inputs except the token
     * 
     * @param ?string $key
     * @return array<mixed, mixed>|string|null
     */
    public function get(?string $key = null): array|string|null
    {
        if (!$key)
            return httpRequest::excepts(["token"]);
        return $this->input($key);
    }



    /**
     * This method returns all inputs except the token and keys in param
     * 
     * @param array $excepts
     * @return array<mixed, mixed>|string|null
     */
    public function excepts(array $keys): array
    {
        return httpRequest::excepts($keys);
    }


    /**
     * This method returns an index of Superglobal $ _Post
     * @param string $input
     * @return string|array<mixed, mixed>|null
     */
    public function input(string $input): string|array|null
    {
        return httpRequest::input($input);
    }

    /**
     * This method is responsible for returning the validation rules
     * 
     * @return array<string, string>
     */
    public function rules(): array
    {
        return $this->rules;
    }

    /**
     * Method responsible for performing validation
     * @return array<void>
     */
    public function execute(): array
    {

        if (!formValidate($this->rules())) {
            notification()->error('Verifique todos os campos antes de prosseguir');
            redirect()->back()->make();
            die;
        }
        return [];
    }
}
