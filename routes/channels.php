<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::routes();

Broadcast::channel('ticket.{ticketId}', function ($user, $ticketId) {
    return true;
}); 