<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockOpname;
use App\Models\Product;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function rules($request)
    {
        $rule = [
            'product_so' => 'required',
            'type_so' => 'required',
            'qty_so' => 'required|min:1',
        ];
        $pesan = [
            'product_so.required' => 'Produk Wajib di isi',
            'type_so.required' => 'Tipe Wajib di isi',
            'qty_so.required' => 'Jumlah Wajib di isi',
            'qty_so.min' => 'Jumlah Minimal 1',
        ];
        return Validator::make($request, $rule, $pesan);
    }

    public function index() {
        $get_module = get_module_id('stock-opname');
        if (!notAccessBackHome($get_module)) {
            return redirect('/home');
        }

        $dataProduct = Product::orderBy('name_product', 'ASC')->get();

        return view('stock-opname', compact('get_module','dataProduct'));
    }

    public function json()
    {
        $datas = StockOpname::with('product')->orderBy('created_at', 'desc');

        return Datatables::of($datas)
            ->addColumn('action', function ($data) {
                //get module akses
                $id_module = get_module_id('stock-opname');

                //detail
                $btn_detail = '';
                if (isAccess('read', $id_module, auth()->user()->level_user)) {

                }

                //edit
                $btn_edit = '';
                if (isAccess('update', $id_module, auth()->user()->level_user)) {
                    $btn_edit = '<button type="button" data-id="' . $data->id_so . '" class="btn btn-sm btn-info btn-edit">Edit</button>';
                }

                //delete
                $btn_hapus = '';
                if (isAccess('delete', $id_module, auth()->user()->level_user)) {
                    if ($data->type_so == 'S') {
                        $type = 'Keluar';
                    }
                    $type = 'Masuk';
                    $text = $data->product->name_product . ' dengan jenis ' . $type. ', jumlahnya ' . $data->qty_so.', tanggal input '.fdate($data->date_so,'DDMMYYYY');
                    $btn_hapus = '<button type="button" data-nama="'.$text.'" data-id="' . $data->id_so . '" class="btn btn-sm btn-danger btn-hapus">Hapus</button>';
                }
                return '
                <div class="btn-group">
                    ' . $btn_edit . '

                    ' . $btn_hapus . '
                </div>
              ';
            })
            ->addColumn('tipe', function ($data) {
                if ($data->type_so == 'S') {
                    return 'Keluar';
                }
                return 'Masuk';
            })
            ->addColumn('tgl', function ($data) {
                return fdate($data->date_so,'DDMMYYYY');
            })
            ->editColumn('grand_total_so', function ($data) {
                return "Rp. ".rupiah_format($data->grand_total_so);
            })
            ->editColumn('hpp_so', function ($data) {
                return "Rp. ".rupiah_format($data->hpp_so);
            })

            ->addIndexColumn() //increment
            ->make(true);
    }

    public function show($id)
    {
        $get_data = StockOpname::find($id);
        return response()->json(['status' => true, 'data' => $get_data]);
    }

    public function store(Request $request) {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {

                $new_data = new StockOpname();
                $new_data->type_so = $request->type_so;
                $new_data->product_so = $request->product_so;
                $new_data->qty_so = rupiah_value($request->qty_so);
                $new_data->hpp_so = rupiah_value($request->hpp_so);

                $new_data->grand_total_so = rupiah_value($request->grand_total_so);
                $new_data->description_so = $request->description_so;
                $new_data->date_so = date('Y-m-d');
                $new_data->day_so= date('d');
                $new_data->month_so= date('m');
                $new_data->year_so= date('Y');

                $new_data->save();

                $dataLog = $new_data;
                insert_log('Add Stock Opname','Stock Opname',$dataLog->getKey(),json_encode($dataLog));

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
                $new_data =  StockOpname::find($id);
                $new_data->type_so = $request->type_so;
                $new_data->product_so = $request->product_so;
                $new_data->qty_so = rupiah_value($request->qty_so);
                $new_data->hpp_so = rupiah_value($request->hpp_so);

                $new_data->grand_total_so = rupiah_value($request->grand_total_so);
                $new_data->description_so = $request->description_so;
                // $new_data->date_so = date('Y-m-d');
                // $new_data->day_so= date('d');
                // $new_data->month_so= date('m');
                // $new_data->year_so= date('Y');

                $new_data->save();

                $dataLog = $new_data;
                insert_log('Update Stock Opname','Stock Opname',$dataLog->getKey(),json_encode($dataLog));

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
            $data = StockOpname::find($id);
            $dataLog = $data;
            insert_log('Delete Stock Opname','Stock Opname',$dataLog->getKey(),json_encode($dataLog));
            StockOpname::destroy($id);
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
