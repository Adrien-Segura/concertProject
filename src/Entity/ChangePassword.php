<?php

namespace App\Entity;

use App\Repository\ChangePasswordRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class ChangePassword {
    #[SecurityAssert\UserPassword(message: 'Mauvaise valeur pour votre ancien mot de passe')]
    private $oldPassword;

    private $plainPassword;

    public function getOldPassword(): ?string {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getPlainPassword(): ?string {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self {
        $this->plainPassword = $plainPassword;

        return $this;
    }
}
