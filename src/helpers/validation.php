<?php

use src\support\Request;
use src\support\Sessions;
use src\support\Validate;

/**
 * Method responsible for validating each of the fields informed in the array
 * 
 * @param  array<string, string> $validations
 * @return array<string, mixed>|bool Form data or false if data is not valid
 */
function formValidate(array $validations): array|bool
{
    $data = (new Validate)->validate($validations);
    $isArray = is_array($data);
    
    if ($isArray) return $data;
    else {
        Sessions::set('old', Request::excepts(['token']), true);
        return false;
    }
}
