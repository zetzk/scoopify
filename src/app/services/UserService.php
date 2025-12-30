<?php

namespace src\app\services;

use src\app\database\entities\User;
use src\exceptions\app\EmailDuplicateException;
use src\exceptions\app\NotFoundWithUuidException;

class UserService
{

    /**
     * create a user
     * Validations: email is unique
     * @param array{name: string, email: string} $data - array with index to base user
     * @throws EmailDuplicateException
     * @return array|bool
     */
    function create(array $data): array|bool
    {
        if ($this->by_email($data["email"]))
            throw new EmailDuplicateException;

        return User::create($data);
    }


    /**
     * Update user
     * @param array{uuid: string, name: string, email: string} $data - dados do formulário
     * @throws NotFoundWithUuidException
     * @return bool|object|object[]
     */
    function update(array $data)
    {
        return User::updateByUuid($data["uuid"], $data);
    }

    /**
     * Get user using email
     * @param string $email
     * @return bool|object|array
     */
    function by_email(string $email): bool|object|array
    {
        return $user_by_email = User::selectOne(['*'])->where("email", "=", $email)->finish();
    }
}
