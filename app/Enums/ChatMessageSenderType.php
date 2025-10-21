<?php

declare(strict_types=1);

namespace App\Enums;

enum ChatMessageSenderType: string
{
    case Customer = 'customer';
    case User = 'user';
    case Bot = 'bot';
}
