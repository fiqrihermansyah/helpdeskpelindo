<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tiket;
use App\Models\Prioritas;
use App\Models\Divisi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function getPrioritas()
    {
        try {
            $priorities = Prioritas::all();
            return response()->json($priorities, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch priorities', 'error' => $e->getMessage()], 500);
        }
    }

    public function getDivisi()
    {
        try {
            $divisions = Divisi::all();
            return response()->json($divisions, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch divisions', 'error' => $e->getMessage()], 500);
        }
    }

    public function storeTicket(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'submitter' => 'required|string|max:255',
                'application' => 'required|string|max:255',
                'priority_id' => 'required|integer|exists:prioritas,id',
                'division_id' => 'required|integer|exists:divisi,id',
                'description' => 'required|string',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'User is not authenticated'], 401);
            }

            $ticket = new Tiket();
            $ticket->judul = $validatedData['title'];
            $ticket->pengaju = $validatedData['submitter'];
            $ticket->aplikasi = $validatedData['application'];
            $ticket->prioritas_id = $validatedData['priority_id'];
            $ticket->divisi_id = $validatedData['division_id'];
            $ticket->deskripsi = $validatedData['description'];
            $ticket->user_id = $user->id;
            $ticket->status_id = 1;  // Mengisi status_id dengan nilai 1
            $ticket->ticket_id = Tiket::generateTicketId($user);

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $originalName = $file->getClientOriginalName();
                $path = $file->storeAs('attachments', $originalName);
                $ticket->file_path = $path; // Pastikan kolom file_path ada pada tabel tickets
            }

            $ticket->save();

            return response()->json(['message' => 'Ticket created successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Ticket creation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Ticket creation failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function getTickets()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'User is not authenticated'], 401);
            }

            $tickets = Tiket::where('user_id', $user->id)
                ->with('prioritas', 'status')
                ->get();

            return response()->json($tickets, 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch tickets: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch tickets', 'error' => $e->getMessage()], 500);
        }
    }
}
