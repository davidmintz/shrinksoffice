<?php

namespace App\Entity;

enum PersonType : string
{
    case PATIENT = 'patient';
    case PAYER = 'payer';
}
