<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::orderBy('id', 'asc');

        if ($request->has('search') && $request->input('search') != '') {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('nama', 'LIKE', '%' . $search . '%')
                    ->orWhere('nipp', 'LIKE', '%' . $search . '%')
                    ->orWhere('nomor_hp', 'LIKE', '%' . $search . '%');
            })
                ->orWhereHas('divisi', function ($query) use ($search) {
                    $query->where('nama_divisi', 'LIKE', '%' . $search . '%');
                });
        }

        $users = $query->paginate(10);
        $roles = Role::all();

        return view('admin.user', compact('users', 'roles'));
    }

    public function updateRole(Request $request)
    {
        $user = User::find($request->user_id);
        $roles = $request->role_ids;

        $user->roles()->sync($roles);

        return redirect()->back()->with('success', 'Roles attached successfully');
    }
}
