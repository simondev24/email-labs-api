<?php

namespace App\Validator;

class ContactValidator {
    private array $errors = [];
    public function validate(string $email, string $firstName, string $lastName): array
    {
        if (empty($email)) {
            $this->errors['email_error'] = 'Email is required';
        } else if (!is_string($email)) {
            $this->errors['email_error'] = 'Email must be a string';
        }
        if (empty($firstName)) {
            $this->errors['first_name_error'] = 'First name is required';
        } else if (!is_string($firstName)) {
            $this->errors['first_name_error'] = 'First name must be a string';
        }
        if (empty($lastName)) {
            $this->errors['last_name_error'] = 'Last name is required';
        } else if (!is_string($lastName)) {
            $this->errors['last_name_error'] = 'Last name must be a string';
        }
        return $this->errors;
    }

}