<?php
namespace src\app\requests\Users;

use src\app\requests\Request;

class StoreRequest extends Request
{
    protected array $rules = [
        "name" => "required",
        "email" => "required",
    ];
}