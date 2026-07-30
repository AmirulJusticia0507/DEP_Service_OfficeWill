<?php

namespace App\Enums;

enum TodoTypeEnum: string
{
    case QUESTIONNAIRE = 'QUESTIONNAIRE';
    case REPORT = 'REPORT';
    case TEST = 'TEST';
}
