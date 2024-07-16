<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tiket;
use App\Models\Prioritas;
use App\Models\Divisi;
use App\Models\Reply;
use App\Models\File;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
                $fileRecord = File::create([
                    'uuid' => (string) Str::uuid(),
                    'nama_file' => $originalName,
                    'nama_server' => $originalName,
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'path' => $path,
                    'extension' => $file->getClientOriginalExtension(),
                    'disk' => 'local',
                    'user_id' => $user->id,
                ]);
                $ticket->file_id = $fileRecord->id;
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

    public function reply(Request $request)
    {
        $request->validate([
            'tiket_id' => 'required|integer|exists:tikets,id',
            'balasan' => 'required|string',
            'fileInput' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User is not authenticated'], 401);
        }

        $reply = new Reply();
        $reply->tiket_id = $request->tiket_id;
        $reply->user_id = $user->id;
        $reply->balasan = $request->balasan;

        if ($request->hasFile('fileInput')) {
            $file = $request->file('fileInput');
            $originalName = $file->getClientOriginalName();
            $serverName = $file->hashName();
            $size = $file->getSize();
            $mime = $file->getMimeType();
            $path = $file->store('public/uploads');
            $extension = $file->getClientOriginalExtension();
            $disk = 'local';

            // Store the file information in the database
            $fileData = [
                'uuid' => (string) Str::uuid(),
                'nama_file' => $originalName,
                'nama_server' => $serverName,
                'size' => $size,
                'mime' => $mime,
                'path' => $path,
                'extension' => $extension,
                'disk' => $disk,
                'user_id' => $user->id,
            ];
            $fileRecord = File::create($fileData);
            $reply->file_id = $fileRecord->id;
        }

        $reply->save();

        return response()->json(['message' => 'Reply submitted successfully'], 200);
    }

    public function getTicketDetails($id)
    {
        try {
            $ticket = Tiket::with(['divisi', 'prioritas', 'status', 'user', 'files', 'balasan'])->findOrFail($id);
            return response()->json($ticket, 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch ticket details: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch ticket details', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function getUsers(Request $request)
    {
        $query = User::with('roles', 'divisi')->orderBy('id', 'asc');

        if ($request->has('search') && $request->input('search') != '') {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('nama', 'LIKE', '%' . $search . '%')
                      ->orWhere('nipp', 'LIKE', '%' . $search . '%')
                      ->orWhere('nomor_hp', 'LIKE', '%' . $search . '%');
            })->orWhereHas('divisi', function ($query) use ($search) {
                $query->where('nama_divisi', 'LIKE', '%' . $search . '%');
            });
        }

        $users = $query->paginate(10);
        return response()->json($users);
    }

    public function getRoles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    public function attachRoles(Request $request)
    {
        $user = User::find($request->user_id);
        $roles = $request->role_ids;

        $user->roles()->sync($roles);

        return response()->json(['message' => 'Roles attached successfully']);
    }

}
