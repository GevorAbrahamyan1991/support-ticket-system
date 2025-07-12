<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendTicketNotification;
use Illuminate\Support\Facades\Http;

class TicketController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Ticket::with('customer', 'agent');
        if (!$user->isAgent()) {
            $query->where('customer_id', $user->id);
        }
        $status = request('status');
        $category = request('category');
        if ($status) {
            $query->where('status', $status);
        }
        if ($category) {
            $query->where('category', $category);
        }
        $tickets = $query->latest()->paginate(10)->withQueryString();
        if (request()->ajax()) {
            $html = view('tickets.partials.list', ['tickets' => $tickets])->render();
            return response()->json(['html' => $html]);
        }
        return view('tickets.index', compact('tickets', 'status', 'category'));
    }

    public function show(Ticket $ticket)
    {
        $user = Auth::user();
        if ($user->isCustomer() && $ticket->customer_id !== $user->id) {
            abort(403);
        }
        $ticket->load('comments.user', 'customer', 'agent');
        if (request()->ajax()) {
            return response()->json([
                'ticket' => $ticket,
                'comments_html' => view('tickets.partials.comments', ['ticket' => $ticket])->render(),
            ]);
        }
        return view('tickets.show', compact('ticket'));
    }

    public function create()
    {
        $this->authorizeRole('customer');
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $this->authorizeRole('customer');
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
        ]);
        $data['customer_id'] = Auth::id();
        $data['status'] = 'open';
        
        $ip = $request->ip();

        $location = null;
        try {
            $response = Http::get("http://ip-api.com/json/{$ip}");
            if ($response->ok()) {
                $location = $response->json();
            }
        } catch (\Exception $e) {
            $location = null;
        }
        $data['metadata'] = [
            'ip' => $ip,
            'location' => $location,
        ];
        $ticket = Ticket::create($data);
        
        SendTicketNotification::dispatch($ticket, 'created');
        if ($request->ajax()) {
            return response()->json(['ticket' => $ticket, 'redirect' => route('tickets.show', $ticket)]);
        }
        return redirect()->route('tickets.show', $ticket);
    }

    public function assign(Ticket $ticket)
    {
        $this->authorizeRole('agent');
        $ticket->agent_id = Auth::id();
        $ticket->save();
        $ticket->load('agent');
        if (request()->ajax()) {
            return response()->json(['success' => true, 'ticket' => $ticket]);
        }
        return back();
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $this->authorizeRole('agent');
        $request->validate(['status' => 'required|in:open,in_progress,closed']);
        $ticket->status = $request->status;
        $ticket->save();
        $ticket->load('agent');
        if ($request->ajax()) {
            return response()->json(['success' => true, 'ticket' => $ticket]);
        }
        return back();
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        if ($user->isCustomer() && $ticket->customer_id !== $user->id) {
            abort(403);
        }
        $request->validate(['content' => 'required|string']);
        $comment = new Comment([
            'content' => $request->content,
            'user_id' => $user->id,
        ]);
        $ticket->comments()->save($comment);
        
        // Broadcast the new comment
        event(new \App\Events\CommentAdded($comment));
        
        SendTicketNotification::dispatch($ticket, 'replied');
        if ($request->ajax()) {
            $ticket->load('comments.user');
            return response()->json([
                'success' => true,
                'comment' => $comment,
                'comments_html' => view('tickets.partials.comments', ['ticket' => $ticket])->render(),
            ]);
        }
        return back();
    }
} 