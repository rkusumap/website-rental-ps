<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Level;
use App\Models\Company;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BiodataController extends Controller
{
    public function index(Request $request) {
        $get_data = User::where('id', auth()->user()->id)->first();
        return view('biodata', compact('get_data'));
    }

    public function rules1($request, $id = null)
    {
        $rule = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email' . ($id ? ",$id" : ''),
            'username' => 'required|unique:users,username' . ($id ? ",$id" : ''),
        ];
        $pesan = [
            'name.required' => 'Nama Wajib di isi',
            'email.required' => 'Email Wajib di isi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar, silakan gunakan email lain',

            'username.required' => 'Username Wajib di isi',
            'username.unique' => 'Username sudah terdaftar, silakan gunakan username lain',
        ];
        return Validator::make($request, $rule, $pesan);
    }

    public function rules2($request, $id = null)
    {
        $rule = [
            'password' => $id ? 'nullable' : 'required',
            'password-confirmation' => $id ? 'nullable|same:password' : 'required|same:password'
        ];
        $pesan = [
            'password.required' => 'Password Wajib di isi',
            'password-confirmation.required' => 'Konfirmasi Password Wajib di isi',
            'password-confirmation.same' => 'Konfirmasi Password harus sama dengan Password'
        ];
        return Validator::make($request, $rule, $pesan);
    }

    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try {
            $validator = $this->rules1($request->all(), $id);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data = User::find($id);
                $new_data->name = $request->name;
                $new_data->email = $request->email;
                $new_data->username = $request->username;
                $new_data->save();

                $dataLog = $new_data;
                insert_log('Update User dari Biodata','User',$dataLog->getKey(),json_encode($dataLog));
                DB::commit();
                return response()->json(['status' => true]);
            }
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function biodata_reset_password(Request $request,$id)
    {
        DB::beginTransaction();
        try {
            $validator = $this->rules2($request->all(), $id);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data = User::find($id);
                $new_data->password = Hash::make($request->password);
                $new_data->save();

                $dataLog = $new_data;
                insert_log('Update Password User dari Biodata','User',$dataLog->getKey(),json_encode($dataLog));
                DB::commit();
                return response()->json(['status' => true]);
            }
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

}
