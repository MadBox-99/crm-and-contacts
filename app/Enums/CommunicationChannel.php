<?php

declare(strict_types=1);

namespace App\Enums;

enum CommunicationChannel: string
{
    case Email = 'email';
    case Sms = 'sms';
    case Chat = 'chat';
    case Social = 'social';
}
