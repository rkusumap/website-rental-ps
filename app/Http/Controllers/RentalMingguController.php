<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RentalMinggu;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

class RentalMingguController extends Controller
{
    public function rules($request)
    {
        $rule = [
            'biaya_rm' => 'required'
        ];
        $pesan = [
            'biaya_rm.required' => 'Biaya Wajib di isi'
        ];
        return Validator::make($request, $rule, $pesan);
    }

    public function index() {
        $get_module = get_module_id('rental-minggu');
        if (!notAccessBackHome($get_module)) {
            return redirect('/home');
        }
        $get_data = RentalMinggu::first();
        return view('master-data.rental-minggu', compact('get_module','get_data'));
    }

    public function update($id,Request $request) {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data = RentalMinggu::find($id);
                $new_data->biaya_rm = rupiah_value($request->biaya_rm);
                $new_data->save();

                $dataLog = $new_data;
                insert_log('Update Rental Minggu','RentalMinggu',$dataLog->getKey(),json_encode($dataLog));

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


}
