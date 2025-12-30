<?php

namespace src\support;

use src\traits\Validations;
use Exception;

class Validate
{

    use Validations;
    /** @var array<string, mixed> */
    private array $inputsValidation = [];
    private array $validations = [];

    /**
     * @param string $validation
     * @param mixed $param
     * @return array<int, mixed>
     */
    private function getParams($validation, $param): array
    {
        if (str_contains($validation, ':')) {
            [$validation, $param] = explode(':', $validation);
        }

        return [$validation, $param];
    }


    /**
     * @param string $validation
     * @return void
     */
    private function validationExist(string $validation): void
    {
        if (!method_exists($this, $validation))
            throw new Exception("A validação {$validation} não existe");
    }


    /**
     * @param array<string, mixed>  $validationsFields
     * 
     * @return string|array<string, mixed>
     */
    public function validate(array $validationsFields): string|array
    {
        $this->validations = $validationsFields;
        foreach ($validationsFields as $field => $validation) {
            $havePipes = str_contains($validation, '|');
            if (!$havePipes) {
                $param = '';

                [$validation, $param] = $this->getParams($validation, $param);

                $this->validationExist($validation);

                $this->inputsValidation[$field] = $this->$validation($field, $param);
            } else {
                $validations = explode('|', $validation);
                $param = '';
                $this->multipleValidations($validations, $field, $param);
            }
        }

        return $this->returnValidation();
    }

    /**
     * @param array<int, string> $validations
     * @param string $field
     * @param mixed $param
     * @return void
     */
    private function multipleValidations($validations, $field, $param): void
    {
        foreach ($validations as $validation) {
            [$validation, $param] = $this->getParams($validation, $param);


            $this->validationExist($validation);

            $this->inputsValidation[$field] = $this->$validation($field, $param);

            if ($this->inputsValidation[$field] === null) {
                break;
            }
        }
    }


    /**
     * @return string|array<string, mixed>
     */
    private function returnValidation(): string|array
    {

        if (in_array(null, $this->inputsValidation, true)) {
            foreach ($this->inputsValidation as $input => $value) {
                if ($value === null && $this->validations[$input] !== 'nullable') {
                    return $input;
                }
            }
        }

        return $this->inputsValidation;
    }


    /**
     * @param mixed $data
     */
    public function validated(mixed $data): bool
    {
        return is_array($data);
    }
}
