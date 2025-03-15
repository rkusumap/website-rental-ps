<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Level;
use App\Models\Customer;

use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function rules($request, $id = null)
    {
        $rule = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email' . ($id ? ",$id" : ''),
            'username' => 'required|unique:users,username' . ($id ? ",$id" : ''),
            'password' => $id ? 'nullable' : 'required',
            'password-confirmation' => $id ? 'nullable|same:password' : 'required|same:password'
        ];
        $pesan = [
            'name.required' => 'Nama Wajib di isi',

            'email.required' => 'Email Wajib di isi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar, silakan gunakan email lain',
            'username.required' => 'Username Wajib di isi',
            'username.unique' => 'Username sudah terdaftar, silakan gunakan username lain',
            'password.required' => 'Password Wajib di isi',
            'password-confirmation.required' => 'Konfirmasi Password Wajib di isi',
            'password-confirmation.same' => 'Konfirmasi Password harus sama dengan Password'
        ];
        return Validator::make($request, $rule, $pesan);
    }

    public function index()
    {
        $get_module = get_module_id('user');
        if (!notAccessBackHome($get_module)) {
            return redirect('/home');
        }
        return view('user.index', compact('get_module'));
    }

    public function json()
    {
        $datas = User::select('*');

        return Datatables::of($datas)
            ->addColumn('action', function ($data) {
                //get module akses
                $id_module = get_module_id('user');

                //detail
                $btn_detail = '';
                if (isAccess('read', $id_module, auth()->user()->level_user)) {
                    $btn_detail = '<a class="dropdown-item" href="' . route('user.show', $data->id) . '">Detail</a>';
                }

                //edit
                $btn_edit = '';
                $btn_reset_password = '';
                if (isAccess('update', $id_module, auth()->user()->level_user)) {
                    $btn_edit = '<button type="button" onclick="location.href=' . "'" . route('user.edit', $data->id) . "'" . ';" class="btn btn-sm btn-info">Edit</button>';
                    $btn_reset_password = '<a class="dropdown-item btn-reset-password" href="#resetpassword" data-id="' . $data->id . '" data-nama="' . $data->name . '">Reset Password</a>';
                }

                //delete
                $btn_hapus = '';
                if (isAccess('delete', $id_module, auth()->user()->level_user)) {
                    $btn_hapus = '<a class="dropdown-item btn-hapus" href="#hapus" data-id="' . $data->id . '" data-nama="' . $data->name . '">Hapus</a>';
                }
                return '
                <div class="btn-group">
                    ' . $btn_edit . '
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle dropdown-toggle-split" id="dropdownMenuSplitButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton1">
                        ' . $btn_detail . '
                        ' . $btn_reset_password . '
                        ' . $btn_hapus . '

                    </div>
                </div>
              ';
            })
            ->addColumn('tgl', function ($data) {
                return fdate($data->updated_at, "HHDDMMYYYY");
            })
            ->editColumn('status_user', function ($data) {
                if ($data->status_user == 1) {
                    $btn = '<button type="button" class="btn btn-sm btn-change btn-success" data-id="' . $data->id . '" data-nama="' . $data->name . '">Aktif</button>';
                } else {
                    $btn = '<button type="button" class="btn btn-sm btn-change btn-danger" data-id="' . $data->id . '" data-nama="' . $data->name . '">Non-Aktif</button>';
                }
                return $btn;
            })
            ->rawColumns(['action', 'status_user'])
            ->addIndexColumn() //increment
            ->make(true);
    }

    public function create()
    {
        $dataLevel = Level::orderBy('name_level', 'ASC')
        ->get();
        $dataCustomer = Customer::orderBy('name_customer', 'ASC')->get();
        return view('user.create',compact('dataLevel','dataCustomer'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data = new User();
                $new_data->name = $request->name;
                $new_data->level_user = $request->level_user;

                $new_data->status_user = 1;
                $new_data->email = $request->email;
                $new_data->username = $request->username;
                $new_data->password = Hash::make($request->password);

                if ($request->customer_user != null) {
                    $new_data->customer_user = $request->customer_user;
                }

                $new_data->save();

                $dataLog = $new_data;
                insert_log('Add User','User',$dataLog->getKey(),json_encode($dataLog));
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

    public function show($id)
    {
        $get_data = User::find($id);
        return view('user.show', compact('get_data'));
    }

    public function edit($id)
    {
        $get_data = User::find($id);
        $dataLevel = Level::orderBy('name_level', 'ASC')
        ->get();
        $dataCustomer = Customer::orderBy('name_customer', 'ASC')->get();
        return view('user.edit', compact('get_data','dataLevel','dataCustomer'));
    }

    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all(), $id);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data = User::find($id);
                $new_data->name = $request->name;
                $new_data->level_user = $request->level_user;

                $new_data->status_user = 1;
                $new_data->email = $request->email;
                $new_data->username = $request->username;
                $new_data->password = Hash::make($request->password);

                if ($request->customer_user != null) {
                    $new_data->customer_user = $request->customer_user;
                }

                $new_data->save();

                $dataLog = $new_data;
                insert_log('Update User','User',$dataLog->getKey(),json_encode($dataLog));
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

    public function reset_password($id) {
        DB::beginTransaction();
        try {
            $new_data = User::find($id);

            $dataLog = $new_data;
            insert_log('Before Reset Password User','User',$dataLog->getKey(),json_encode($dataLog));

            $new_data->password = Hash::make('pass123');
            $new_data->save();

            $dataLog = $new_data;
            insert_log('After Reset Password User','User',$dataLog->getKey(),json_encode($dataLog));

            DB::commit();
            return response()->json(['status' => true]);
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function change_status($id) {
        DB::beginTransaction();
        try {
            $new_data = User::find($id);

            $dataLog = $new_data;
            insert_log('Before Change Status User','User',$dataLog->getKey(),json_encode($dataLog));
            $status = null;
            if ($new_data->status_user == 1) {
                $status = 0;
            }
            else {
                $status = 1;
            }
            $new_data->status_user = $status;
            $new_data->save();

            $dataLog = $new_data;
            insert_log('After Change Status User','User',$dataLog->getKey(),json_encode($dataLog));

            DB::commit();
            return response()->json(['status' => true]);
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = User::find($id);
            $dataLog = $data;
            insert_log('Delete User','User',$dataLog->getKey(),json_encode($dataLog));
            $dataCustomer = Customer::where('id_customer', $data->customer_user)->first();
            if ($dataCustomer != null) {
                $dataLog = $dataCustomer;
                insert_log('Delete Customer','Customer',$dataLog->getKey(),json_encode($dataLog));
                Customer::destroy($dataCustomer->id_customer);
            }
            User::destroy($id);
            DB::commit();
            return response()->json(['status' => true]);
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
