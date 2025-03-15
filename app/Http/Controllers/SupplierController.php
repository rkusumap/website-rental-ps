<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function rules($request, $id_supplier = null)
    {
        $rule = [
            'name_supplier'  => 'required',
            'phone_supplier' => 'required|unique:ms_supplier,phone_supplier' . ($id_supplier ? ",$id_supplier,id_supplier" : ''),
            'address_supplier'  => 'required',
            'description_supplier'  => 'nullable',
        ];
        $pesan = [
            'name_supplier.required'         => 'Nama Supplier Wajib Diisi',
            'phone_supplier.required'        => 'No HP Supplier Wajib Diisi',
            'phone_supplier.unique'          => 'No HP Supplier Sudah Terdaftar',
            'address_supplier.required'      => 'Alamat Supplier Wajib Diisi',
        ];

        return Validator::make($request,$rule,$pesan);
    }

    public function index() {
        $get_module = get_module_id('supplier');
        if (!notAccessBackHome($get_module)) {
            return redirect('/home');
        }

        return view('master-data.supplier', compact('get_module'));
    }

    public function json()
    {
        $datas = Supplier::select(['id_supplier', 'name_supplier','phone_supplier','address_supplier','description_supplier','updated_at'])->orderBy('name_supplier', 'asc');

        return Datatables::of($datas)
            ->addColumn('action', function ($data) {
                //get module akses
                $id_module = get_module_id('supplier');

                //detail
                $btn_detail = '';
                if (isAccess('read', $id_module, auth()->user()->level_user)) {

                }

                //edit
                $btn_edit = '';
                if (isAccess('update', $id_module, auth()->user()->level_user)) {
                    $btn_edit = '<button type="button" data-id="' . $data->id_supplier . '" class="btn btn-sm btn-info btn-edit">Edit</button>';
                }

                //delete
                $btn_hapus = '';
                if (isAccess('delete', $id_module, auth()->user()->level_user)) {
                    $btn_hapus = '<button type="button" data-nama="'.$data->name_supplier.'" data-id="' . $data->id_supplier . '" class="btn btn-sm btn-danger btn-hapus">Hapus</button>';
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
        $get_data = Supplier::find($id);
        return response()->json(['status' => true, 'data' => $get_data]);
    }

    public function store(Request $request) {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data = new Supplier();
                $new_data->name_supplier = $request->name_supplier;
                $new_data->phone_supplier = $request->phone_supplier;
                $new_data->address_supplier = $request->address_supplier;
                $new_data->description_supplier = $request->description_supplier;

                $new_data->save();

                $dataLog = $new_data;
                insert_log('Add Supplier','Supplier',$dataLog->getKey(),json_encode($dataLog));

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
                $new_data = Supplier::find($id);
                $new_data->name_supplier = $request->name_supplier;
                $new_data->phone_supplier = $request->phone_supplier;

                $new_data->save();

                $dataLog = $new_data;
                insert_log('Update Supplier','Supplier',$dataLog->getKey(),json_encode($dataLog));

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
            $data = Supplier::find($id);
            $dataLog = $data;
            insert_log('Delete Supplier','Supplier',$dataLog->getKey(),json_encode($dataLog));
            Supplier::destroy($id);
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
