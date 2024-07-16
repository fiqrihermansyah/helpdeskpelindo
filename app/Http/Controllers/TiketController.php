<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tiket;
use App\Models\Prioritas;
use App\Models\Divisi;
use App\Models\File;
use App\Models\Reply;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Role;

class TiketController extends Controller
{
    public function index(Request $request)
    {
        $query = Tiket::orderBy('id', 'desc');

        if ($request->has('search_filter') && $request->input('search_filter') != '') {
            $search_filter = $request->input('search_filter');
            $query->where(function ($query) use ($search_filter) {
                $query->where('judul', 'LIKE', '%' . $search_filter . '%')
                    ->orWhere('pengaju', 'LIKE', '%' . $search_filter . '%')
                    ->orWhere('aplikasi', 'LIKE', '%' . $search_filter . '%')
                    ->orWhere('deskripsi', 'LIKE', '%' . $search_filter . '%')
                    ->orWhere('user_id', 'LIKE', '%' . $search_filter . '%')
                    ->orWhere('agent_id', 'LIKE', '%' . $search_filter . '%');
            })
                ->orWhereHas('status', function ($query) use ($search_filter) {
                    $query->where('nama_status', 'LIKE', '%' . $search_filter . '%');
                })
                ->orWhereHas('prioritas', function ($query) use ($search_filter) {
                    $query->where('nama_prioritas', 'LIKE', '%' . $search_filter . '%');
                })
                ->orWhereHas('divisi', function ($query) use ($search_filter) {
                    $query->where('nama_divisi', 'LIKE', '%' . $search_filter . '%');
                });
        }

        if ($request->has('date') && $request->input('date') != '') {
            $date = $request->input('date');
            $query->whereDate('tiket.created_at', '=', $date);
        }

        if ($request->has('prioritas')) {
            $prioritas_filter = $request->input('prioritas');
            if ($prioritas_filter != 'all') {
                $query->whereHas('prioritas', function ($query) use ($prioritas_filter) {
                    $query->where('nama_prioritas', $prioritas_filter);
                });
            }
        }

        if ($request->has('status')) {
            $status_filter = $request->input('status');
            if ($status_filter != 'all') {
                $query->whereHas('status', function ($query) use ($status_filter) {
                    $query->where('nama_status', $status_filter);
                });
            }
        }

        // Execute the query and paginate the results
        $data = $query->join('prioritas', 'tiket.prioritas_id', '=', 'prioritas.id')
            ->join('status', 'tiket.status_id', '=', 'status.id')
            ->select('tiket.*', 'prioritas.nama_prioritas', 'status.nama_status')
            ->paginate(10);

        $data->append($request->all());

        return view('tiket.daftar', compact('data'));
    }

    public function getTickets(Request $request)
    {
        $user = Auth::user();
        Log::info('Fetching tickets for user:', ['user_id' => $user->id]);

        $tickets = Tiket::where('user_id', $user->id)
            ->with('prioritas', 'status')
            ->get();

        Log::info('Fetched tickets:', ['tickets' => $tickets]);

        return response()->json($tickets);
    }

    public function create()
    {
        $prioritasOptions = Prioritas::all();
        $divisiOptions = Divisi::all();

        return view('tiket.ajukan', compact('prioritasOptions', 'divisiOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'pengaju' => 'required|string|max:255',
            'aplikasi' => 'required|string|max:255',
            'prioritas_id' => 'required|exists:prioritas,id',
            'deskripsi' => 'required|string',
            'divisi_id' => 'required|exists:divisi,id',
        ]);

        Session::flash('judul', $request->judul);
        Session::flash('pengaju', $request->pengaju);
        Session::flash('aplikasi', $request->aplikasi);
        Session::flash('prioritas_id', $request->prioritas_id);
        Session::flash('deskripsi', $request->deskripsi);
        Session::flash('divisi_id', $request->divisi_id);

        $data = [
            'judul' => $request->judul,
            'pengaju' => $request->pengaju,
            'aplikasi' => $request->aplikasi,
            'prioritas_id' => $request->prioritas_id,
            'deskripsi' => $request->deskripsi,
            'status_id' => 1,
            'divisi_id' => $request->divisi_id,
            'user_id' => Auth::user()->id,
        ];

        if ($request->hasFile('formFile')) {
            $file = $request->file('formFile');
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
                'user_id' => Auth::user()->id,
            ];
            $fileRecord = File::create($fileData);
            $data['file_id'] = $fileRecord->id;
        }

        $user = Auth::user();
        $data['ticket_id'] = Tiket::generateTicketId($user);

        try {
            Tiket::create($data);
            return redirect()->route('tiket.daftar')->with('success', 'Tiket berhasil ditambahkan');
        } catch (\Exception $e) {
            return "Gagal menambahkan tiket: " . $e->getMessage();
        }
    }


    public function show($id)
    {
        $tiket = Tiket::with(['prioritas', 'status', 'user', 'balasan.user', 'balasan.files', 'files', 'divisi'])->find($id);

        if (!$tiket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        return response()->json($tiket, 200);
    }

    public function reply(Request $request)
    {
        $data = [
            'tiket_id' => $request->tiket_id,
            'user_id' => $request->user_id,
            'balasan' => $request->balasan,
        ];

        $tiket = Tiket::find($request->tiket_id);
        if ($tiket->status_id == 1) {
            $tiket->status_id = 2;
            $tiket->save();
        }

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
                'user_id' => Auth::user()->id,
            ];
            $fileRecord = File::create($fileData);
            $data['file_id'] = $fileRecord->id;
        }

        try {
            Reply::create($data);
            return redirect()->route('tiket.show', $request->tiket_id)->with('success', 'Balasan berhasil ditambahkan');
        } catch (\Exception $e) {
            return ("Gagal menambahkan balasan " . $e->getMessage());
        }
    }

    public function close($id)
    {
        $tiket = Tiket::find($id);
        if (!$tiket) {
            return redirect()->back()->with('error', 'Ticket not found');
        }

        $tiket->status_id = 3;
        $tiket->closed_by = Auth::user()->id;
        $tiket->closed_at = now();
        $tiket->save();

        return redirect()->back()->with('success', 'Ticket closed');
    }

    public function updatePrioritas(Request $request, Tiket $tiket)
    {
        $validatedData = $request->validate([
            'prioritas' => 'required|integer',
        ]);

        $tiket->update(['prioritas_id' => $validatedData['prioritas']]);

        return redirect()->back()->with('success', 'Prioritas updated successfully');
    }

    public function reopen($id)
    {
        $tiket = Tiket::find($id);
        if (!$tiket) {
            return redirect()->back()->with('error', 'Ticket not found');
        }

        $tiket->status_id = 1;
        $tiket->save();

        return redirect()->back()->with('success', 'Ticket has been reopened');
    }
}
