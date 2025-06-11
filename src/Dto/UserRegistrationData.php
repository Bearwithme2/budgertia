<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class UserRegistrationData
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public string $password;

    public function __construct(string $email = '', string $password = '')
    {
        $this->email = $email;
        $this->password = $password;
    }
}
