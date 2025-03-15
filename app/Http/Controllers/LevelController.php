<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Level;
use App\Models\Module;
use App\Models\Role;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

class LevelController extends Controller
{
    public function rules($request)
    {
        $rule = [
            'name_level' => 'required',
            'code_level' => 'required'
        ];
        $pesan = [
            'name_level.required' => 'Nama Level Wajib di isi',
            'code_level.required' => 'Kode Level Wajib di isi',
        ];
        return Validator::make($request, $rule, $pesan);
    }

    public function index()
    {
        $get_module = get_module_id('level');
        if (!notAccessBackHome($get_module)) {
            return redirect('/home');
        }

        return view('level.index', compact('get_module'));
    }

    public function json()
    {
        $datas = Level::select(['id_level', 'name_level', 'updated_at'])
        ;
        return Datatables::of($datas)
            ->addColumn('action', function ($data) {
                //get module akses
                $id_module = get_module_id('level');

                //detail
                $btn_detail = '';
                if (isAccess('read', $id_module, auth()->user()->level_user)) {
                    $btn_detail = '<a class="dropdown-item" href="' . route('level.show', $data->id_level) . '">Detail</a>';
                }

                //edit
                $btn_edit = '';
                if (isAccess('update', $id_module, auth()->user()->level_user)) {
                    $btn_edit = '<button type="button" onclick="location.href=' . "'" . route('level.edit', $data->id_level) . "'" . ';" class="btn btn-sm btn-info">Edit</button>';
                }

                //delete
                $btn_hapus = '';
                if (isAccess('delete', $id_module, auth()->user()->level_user)) {
                    $btn_hapus = '<a class="dropdown-item btn-hapus" href="#hapus" data-id="' . $data->id_level . '" data-nama="' . $data->name_level . '">Hapus</a>';
                }
                return '
                <div class="btn-group">
                    ' . $btn_edit . '
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle dropdown-toggle-split" id="dropdownMenuSplitButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton1">
                        ' . $btn_detail . '
                        ' . $btn_hapus . '

                    </div>
                </div>
              ';
            })
            ->addColumn('tgl', function ($data) {
                return fdate($data->updated_at, "HHDDMMYYYY");
            })
            ->addIndexColumn() //increment
            ->make(true);
    }

    public function create()
    {
        $dataModule = Module::where('induk_module', '0')

        ->orderby('order_module', 'ASC')->get();
        return view('level.create', compact('dataModule'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data = new Level();
                $new_data->name_level = $request->name_level;
                $new_data->code_level = $request->code_level;

                $new_data->save();

                $dataLog = $new_data;
                insert_log('Add Level','Level',$dataLog->getKey(),json_encode($dataLog));

                Role::where('level_gmd', $new_data->id_level)->delete();
                foreach ($request->action_gmd as $key => $value) {
                    if ($value != null) {

                        $dataRole = new Role;

                        $dataRole->level_gmd = $new_data->id_level;
                        $dataRole->module_gmd = $key;
                        $dataRole->action_gmd = $value;

                        $dataRole->save();

                        $dataLog = $dataRole;
                        insert_log('Insert Role','Role',$dataLog->getKey(),json_encode($dataLog));
                    }
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

    public function show($id)
    {
        $get_data = Level::find($id);
        return view('level.show', compact('get_data'));
    }

    public function edit($id)
    {
        $get_data = Level::find($id);
        $dataModule = Module::where('induk_module', '0')

        ->orderby('order_module', 'ASC')->get();
        return view('level.edit', compact('get_data','dataModule'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data = Level::find($id);
                $new_data->name_level = $request->name_level;
                $new_data->code_level = $request->code_level;

                $new_data->save();

                $dataLog = $new_data;
                insert_log('Update Level','Level',$dataLog->getKey(),json_encode($dataLog));

                Role::where('level_gmd', $new_data->id_level)->delete();
                foreach ($request->action_gmd as $key => $value) {
                    if ($value != null) {

                        $dataRole = new Role;

                        $dataRole->level_gmd = $new_data->id_level;
                        $dataRole->module_gmd = $key;
                        $dataRole->action_gmd = $value;

                        $dataRole->save();

                        $dataLog = $dataRole;
                        insert_log('Insert Role','Role',$dataLog->getKey(),json_encode($dataLog));
                    }
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

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = Level::find($id);
            $dataLog = $data;
            insert_log('Delete Level','Level',$dataLog->getKey(),json_encode($dataLog));
            Level::destroy($id);
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
