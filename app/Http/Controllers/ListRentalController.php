<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\RentalDetail;
use App\Models\Product;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

class ListRentalController extends Controller
{
    public function rules($request)
    {
        $rule = [
            'status_rental' => 'required'
        ];
        $pesan = [
            'status_rental.required' => 'Status Rental Wajib di isi'
        ];
        return Validator::make($request, $rule, $pesan);
    }

    public function index() {

        $get_module = get_module_id('list-pemesanan');
        if (!notAccessBackHome($get_module)) {
            return redirect('/home');
        }

        return view('rental.list', compact('get_module'));
    }

    public function json()
    {
        $datas = Rental::with(['user', 'rental_detail_one.product'])
                ->select('tb_rental.*')
                ->orderBy('created_at', 'desc');


        return Datatables::of($datas)
            ->addColumn('action', function ($data) {
                //get module akses
                $id_module = get_module_id('list-pemesanan');

                $btn_detail = '';
                if (isAccess('read', $id_module, auth()->user()->level_user)) {
                        $btn_detail = '<button type="button" data-id="' . $data->id_rental . '"  class="btn btn-sm btn-info btn-detail m-1">Detail</button>';
                }

                //barang
                $btn_rental = '';
                if (isAccess('update', $id_module, auth()->user()->level_user)) {
                    if ($data->payment_status_rental == 'success') {
                        $btn_rental = '<button type="button" data-id="' . $data->id_rental . '" class="btn btn-sm btn-success btn-rental m-1">Rental</button>  <br>';
                    }
                }

                $btn_delete = '';
                if (isAccess('update', $id_module, auth()->user()->level_user)) {

                        $btn_delete = '<button type="button" data-id="' . $data->id_rental . '" data-nama="' . $data->code_rental . '" class="btn btn-sm btn-danger btn-hapus m-1">Delete</button>';
                }


                return '

                    ' . $btn_detail . '
                    ' . $btn_rental . '
                    ' . $btn_delete . '

              ';
            })
            ->addColumn('tanggal', function ($data) {
                return fdate($data->date_start_rental, 'DDMMYYYY').'<br> - <br>'. fdate($data->date_akhir_rental, 'DDMMYYYY');
            })

            ->addColumn('status_bayar', function ($data) {
                if ($data->payment_status_rental == 'pending') {
                    return '<span class="badge badge-warning">Pending</span>';
                }
                if ($data->payment_status_rental == 'success') {
                    return '<span class="badge badge-success">Success</span>';
                }

            })

            ->addColumn('status_rental', function ($data) {
                return reference('status_rental', strval($data->return_status_rental));
            })

            ->addColumn('biaya', function ($data) {
                return 'Rp '.number_format($data->grand_total_rental, 0, ',', '.');
            })
            ->rawColumns(['action', 'status_bayar','tanggal'])
            ->addIndexColumn() //increment
            ->make(true);
    }

    public function show($id)
    {
        // dd($id);
        $get_data = Rental::where('id_rental', $id)->with(['rental_detail_one.product', 'user'])->first();
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $status = \Midtrans\Transaction::status($get_data->order_id);
        if ($get_data->one_day_rental == 1) {
            $formated_date = fdate($get_data->date_start_rental, 'DDMMYYYY');
        }
        else{
            $formated_date = fdate($get_data->date_start_rental, 'DDMMYYYY') .' - '. fdate($get_data->date_akhir_rental, 'DDMMYYYY');
        }
        $total = rupiah_format($get_data->grand_total_rental);
        return response()->json([
            'status' => true,
            'data' => $get_data,
            'status' => $status,
            'formated_date' => $formated_date,
            'total' => $total
        ]);
    }

    public function rental_show($id) {
        $get_data = Rental::where('id_rental', $id)->with(['rental_detail_one.product', 'user'])->first();

        return response()->json([
            'status' => true,
            'data' => $get_data,

        ]);
    }

    public function update($id,Request $request) {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data = Rental::find($id);
                $new_data->return_status_rental = $request->status_rental;
                $new_data->save();

                $dataLog = $new_data;
                insert_log('Update Rental','Rental',$dataLog->getKey(),json_encode($dataLog));

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

    public function destroy($id) {
        DB::beginTransaction();
        try {
            $data = Rental::find($id);
            $dataLog = $data;
            insert_log('Delete Rental','Rental',$dataLog->getKey(),json_encode($dataLog));

            $data = RentalDetail::where('rental_rtd', $id)->get();
            $dataLog = $data;
            insert_log('Delete RentalDetail','RentalDetail',$id,json_encode($dataLog));

            Rental::destroy($id);
            RentalDetail::where('rental_rtd', $id)->delete();
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
