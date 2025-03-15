<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Level;
use DB;
use Validator;

class AuthController extends Controller
{

    public function rules($request, $id = null)
    {
        $rule = [
            'name' => 'required',
            'nohp' => 'required|unique:ms_customer,phone_customer' . ($id ? ",$id" : ''),
            'email' => 'required|email|unique:users,email' . ($id ? ",$id" : ''),
            'username' => 'required|unique:users,username' . ($id ? ",$id" : ''),
            'password' => 'required',

        ];
        $pesan = [
            'name.required' => 'Nama Wajib di isi',
            'nohp.required' => 'No HP Wajib di isi',
            'nohp.unique' => 'No HP sudah terdaftar, silakan gunakan No HP lain',

            'email.required' => 'Email Wajib di isi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar, silakan gunakan email lain',
            'username.required' => 'Username Wajib di isi',
            'username.unique' => 'Username sudah terdaftar, silakan gunakan username lain',
            'password.required' => 'Password Wajib di isi',

        ];
        return Validator::make($request, $rule, $pesan);
    }

    protected function validateUser(User $user)
    {
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->status_user == '0') {
            return response()->json(['error' => 'Account is inactive or blocked'], 403);
        }

        return null; // Return null if validation passes
    }
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        $user = User::where('username', $request->username)->first();

        // Validate the user
        $validationError = $this->validateUser($user);
        if ($validationError) {
            return $validationError;
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json(['status' => true, 'pesan' => 'Username dan Password Benar']);
        }

        return response()->json(['status' => false, 'pesan' => 'Username Atau Password Salah']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
    public function register(Request $request) {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {

                $dataCustomer = new Customer();
                $dataCustomer->name_customer = $request->name;
                $dataCustomer->phone_customer = $request->nohp;
                $dataCustomer->save();

                $dataLevelCustomer = Level::where('code_level','CUSTOMER')->first();

                $new_data = new User();
                $new_data->customer_user = $dataCustomer->getKey();
                $new_data->name = $request->name;
                $new_data->level_user = $dataLevelCustomer->id_level;
                $new_data->status_user = 1;
                $new_data->email = $request->email;
                $new_data->username = $request->username;
                $new_data->password = Hash::make($request->password);

                $new_data->save();

                $dataLog = $new_data;
                insert_log('Add User','User',$dataLog->getKey(),json_encode($dataLog));

                $credentials = array(
                    'username' => $request->username,
                    'password' => $request->password
                );
                if (Auth::attempt($credentials)) {
                    $request->session()->regenerate();
                }


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
