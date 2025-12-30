<?php

namespace src\traits;

use src\support\IsWrong;
use src\support\Request;


trait Validations
{

    /**
     * Responsible for checking if the email provided is valid
     * @param string $field
     * @return ?string 
     */
    public function email(string $field): ?string
    {
        $input = Request::input($field);
        if(is_string($input)){
            $message = '';
            if (!filter_input(INPUT_POST, $field, FILTER_VALIDATE_EMAIL)) {
                if ($_ENV["APP_LANGUAGE"] === 'en-us') $message = "The email provided is not valid";
                else if ($_ENV["APP_LANGUAGE"] === 'pt-br') $message = "O e-mail fornecido não é válido";
                IsWrong::set($field, $message);
                return null;
            }
    
            return strip_tags($input, '<p>'); 
        }
        return null;
    }

    /**
     * Check if the field contains the amount of characters in its maximum limit
     * @param string $field
     * @param int $length
     * @return mixed
     */
    public function maxLen(string $field, int $length): mixed
    {
        $message = '';
        $data = Request::input($field);
        if (is_string($data)) {
            if (strlen($data) > $length) {
                if ($_ENV["APP_LANGUAGE"] === 'en-us') $message = "Field limit is {$length} characters";
                else if ($_ENV["APP_LANGUAGE"] === 'pt-br') $message = "O limite do campo é de {$length} caracteres";
                IsWrong::set($field, $message);
                return null;
            }
            return strip_tags($data, '<p>');
        }
        return null;
    }

    /**
     * Checks if the field has been completed
     * @param string $field
     * @return mixed
     */
    public function required(string $field): mixed
    {
        $message = '';
        $data = Request::input($field);
        if (empty($data)) {
            if ($data !== '0') {
                if ($_ENV["APP_LANGUAGE"] === 'en-us') $message = "The field is required";
                else if ($_ENV["APP_LANGUAGE"] === 'pt-br') $message = "O campo é obrigatório";
                IsWrong::set($field, $message);
                return null;
            }
        }

        if (is_array($data)) return $data;
        else return strip_tags($data, '<p><b><ul><span><em>');
    }


    /**
     * Only returns the informed content
     * @param string $field
     * @return mixed
     */
    public function nullable(string $field): mixed
    {
        return Request::input($field);
    }
}
