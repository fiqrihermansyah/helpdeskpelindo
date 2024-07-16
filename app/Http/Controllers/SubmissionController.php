<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nomor_ppkb' => 'required|string',
            'ppkb_ke' => 'required|integer',
            'service_code' => 'required|string',
            'nama_kapal' => 'required|string',
            'keagenan' => 'required|string',
            // 'fileInput' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Simpan data submission
        $submission = new Submission();
        $submission->nomor_ppkb = $request->input('nomor_ppkb');
        $submission->ppkb_ke = $request->input('ppkb_ke');
        $submission->service_code = $request->input('service_code');
        $submission->nama_kapal = $request->input('nama_kapal');
        $submission->keagenan = $request->input('keagenan');
        $submission->status = 'New'; // Set status to 'New'
        $submission->user_id = Auth::id(); // Use authenticated user's ID
        // $submission->file_path = $filePath; (Jika diperlukan di masa depan)
        $submission->save();

        return response()->json(['message' => 'Submission berhasil disimpan'], 200);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $submissions = Submission::where('user_id', $user->id)->get();
        return response()->json($submissions, 200);
    }
}
