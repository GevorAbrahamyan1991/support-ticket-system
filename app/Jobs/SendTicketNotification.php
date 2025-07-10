<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTicketNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ticket;
    public $type; 

    public function __construct(Ticket $ticket, string $type)
    {
        $this->ticket = $ticket;
        $this->type = $type;
    }

    public function handle()
    {
        if ($this->type === 'created') {
            $agents = User::where('role', 'agent')->get();
            foreach ($agents as $agent) {
                Mail::raw(
                    "New ticket #{$this->ticket->id}: {$this->ticket->title}",
                    function ($message) use ($agent) {
                        $message->to($agent->email)
                            ->subject('New Support Ticket');
                    }
                );
            }
        } elseif ($this->type === 'replied' && $this->ticket->agent) {
            $agent = $this->ticket->agent;
            Mail::raw(
                "Ticket #{$this->ticket->id} has a new reply.",
                function ($message) use ($agent) {
                    $message->to($agent->email)
                        ->subject('Ticket Reply Notification');
                }
            );
        }
    }
} 