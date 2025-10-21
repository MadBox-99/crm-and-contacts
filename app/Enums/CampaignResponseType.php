<?php

declare(strict_types=1);

namespace App\Enums;

enum CampaignResponseType: string
{
    case Interested = 'interested';
    case NotInterested = 'not_interested';
    case Callback = 'callback';
    case NoResponse = 'no_response';
}
