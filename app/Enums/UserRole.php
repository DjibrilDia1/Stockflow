<?php

namespace App\Enums;

enum UserRole: string
{
    case GESTIONNAIRE = 'gestionnaire';
    case DEMANDEUR = 'demandeur';
    case RESPONSABLE = 'responsable';
}
