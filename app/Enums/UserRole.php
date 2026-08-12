<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Instructor = 'instructor';
    case Student = 'student';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Instructor => 'Instrutor',
            self::Student => 'Aluno',
        };
    }

    public function isStaff(): bool
    {
        return $this === self::Admin || $this === self::Instructor;
    }
}
