<?php

namespace Data\Model;

class RegisterParams {
    public function __construct(
        protected string $username,
        protected string $password
    ) {}
    public function getUsername(): string {
        return $this->username;
    }

    public function getPassword(): string {
        return $this->password;
    }
}
