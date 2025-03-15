<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function rules($request, $id_customer = null)
    {
        $rule = [
            'name_customer'  => 'required',
            'phone_customer' => 'required|unique:ms_customer,phone_customer' . ($id_customer ? ",$id_customer,id_customer" : ''),
        ];
        $pesan = [
            'name_customer.required'         => 'Nama Customer Wajib Diisi',
            'phone_customer.required'        => 'No HP Customer Wajib Diisi',
            'phone_customer.unique'          => 'No HP Customer Sudah Terdaftar'
        ];

        return Validator::make($request,$rule,$pesan);
    }

    public function index() {
        $get_module = get_module_id('customer');
        if (!notAccessBackHome($get_module)) {
            return redirect('/home');
        }

        return view('master-data.customer', compact('get_module'));
    }

    public function json()
    {
        $datas = Customer::select(['id_customer', 'name_customer','phone_customer', 'updated_at'])->orderBy('name_customer', 'asc');

        return Datatables::of($datas)
            ->addColumn('action', function ($data) {
                //get module akses
                $id_module = get_module_id('customer');

                //detail
                $btn_detail = '';
                if (isAccess('read', $id_module, auth()->user()->level_user)) {

                }

                //edit
                $btn_edit = '';
                if (isAccess('update', $id_module, auth()->user()->level_user)) {
                    $btn_edit = '<button type="button" data-id="' . $data->id_customer . '" class="btn btn-sm btn-info btn-edit">Edit</button>';
                }

                //delete
                $btn_hapus = '';
                if (isAccess('delete', $id_module, auth()->user()->level_user)) {
                    $btn_hapus = '<button type="button" data-nama="'.$data->name_customer.'" data-id="' . $data->id_customer . '" class="btn btn-sm btn-danger btn-hapus">Hapus</button>';
                }
                return '
                <div class="btn-group">
                    ' . $btn_edit . '

                    ' . $btn_hapus . '
                </div>
              ';
            })
            ->addIndexColumn() //increment
            ->make(true);
    }

    public function show($id)
    {
        $get_data = Customer::find($id);
        return response()->json(['status' => true, 'data' => $get_data]);
    }

    public function store(Request $request) {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data = new Customer();
                $new_data->name_customer = $request->name_customer;
                $new_data->phone_customer = $request->phone_customer;

                $new_data->save();

                $dataLog = $new_data;
                insert_log('Add Customer','Customer',$dataLog->getKey(),json_encode($dataLog));

                DB::commit();
                return response()->json(['status' => true,'type' => 'add']);
            }
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function update($id,Request $request) {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all(),$id);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data = Customer::find($id);
                $new_data->name_customer = $request->name_customer;
                $new_data->phone_customer = $request->phone_customer;
                $cekDataUser = User::where('customer_user', $id)->first();
                if ($cekDataUser) {
                    $cekDataUser->name = $request->name_customer;
                    $cekDataUser->save();
                }
                $new_data->save();

                $dataLog = $new_data;
                insert_log('Update Customer','Customer',$dataLog->getKey(),json_encode($dataLog));

                DB::commit();
                return response()->json(['status' => true,'type' => 'edit']);
            }
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id) {
        DB::beginTransaction();
        try {
            $data = Customer::find($id);
            $dataLog = $data;
            insert_log('Delete Customer','Customer',$dataLog->getKey(),json_encode($dataLog));
            Customer::destroy($id);
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
