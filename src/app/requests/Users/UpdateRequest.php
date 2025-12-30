<?php
namespace src\app\requests\Users;

use src\app\requests\Request;

class UpdateRequest extends Request
{
    protected array $rules = [
        "name" => "required",
        "email" => "required",
    ];
}