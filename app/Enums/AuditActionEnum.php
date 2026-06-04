<?php

namespace App\Enums;

enum AuditActionEnum: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
}
