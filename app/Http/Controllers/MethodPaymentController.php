<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MethodPayment;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

class MethodPaymentController extends Controller
{
    public function rules($request)
    {
        $rule = [
            'name_mp' => 'required'
        ];
        $pesan = [
            'name_mp.required' => 'Nama Metode Pembayaran Wajib di isi'
        ];
        return Validator::make($request, $rule, $pesan);
    }

    public function index() {
        $get_module = get_module_id('method-payment');
        if (!notAccessBackHome($get_module)) {
            return redirect('/home');
        }

        return view('master-data.method-payment', compact('get_module'));
    }

    public function json()
    {
        $datas = MethodPayment::select(['id_mp', 'name_mp', 'updated_at'])->orderBy('name_mp', 'asc');

        return Datatables::of($datas)
            ->addColumn('action', function ($data) {
                //get module akses
                $id_module = get_module_id('method-payment');

                //detail
                $btn_detail = '';
                if (isAccess('read', $id_module, auth()->user()->level_user)) {

                }

                //edit
                $btn_edit = '';
                if (isAccess('update', $id_module, auth()->user()->level_user)) {
                    $btn_edit = '<button type="button" data-id="' . $data->id_mp . '" class="btn btn-sm btn-info btn-edit">Edit</button>';
                }

                //delete
                $btn_hapus = '';
                if (isAccess('delete', $id_module, auth()->user()->level_user)) {
                    $btn_hapus = '<button type="button" data-nama="'.$data->name_mp.'" data-id="' . $data->id_mp . '" class="btn btn-sm btn-danger btn-hapus">Hapus</button>';
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
        $get_data = MethodPayment::find($id);
        return response()->json(['status' => true, 'data' => $get_data]);
    }

    public function store(Request $request) {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data = new MethodPayment();
                $new_data->name_mp = $request->name_mp;

                $new_data->save();

                $dataLog = $new_data;
                insert_log('Add Method Payment','Method Payment',$dataLog->getKey(),json_encode($dataLog));

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
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data = MethodPayment::find($id);
                $new_data->name_mp = $request->name_mp;

                $new_data->save();

                $dataLog = $new_data;
                insert_log('Update Method Payment','Method Payment',$dataLog->getKey(),json_encode($dataLog));

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
            $data = MethodPayment::find($id);
            $dataLog = $data;
            insert_log('Delete Method Payment','Method Payment',$dataLog->getKey(),json_encode($dataLog));
            MethodPayment::destroy($id);
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
