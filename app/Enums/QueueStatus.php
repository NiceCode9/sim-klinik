<?php

namespace App\Enums;

enum QueueStatus: string
{
    case WaitingOnlineConfirmation = 'waiting_online_confirmation';
    case Waiting = 'waiting';
    case Called = 'called';
    case InProgress = 'in_progress';
    case Done = 'done';
    case Skipped = 'skipped';
}
