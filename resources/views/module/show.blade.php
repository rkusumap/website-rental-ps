@extends('admin.layout')

@section('css')

@endsection

@section('page-title')

@endsection

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="page-header">
            <h3 class="page-title">
                Detail Module
            </h3>
            <nav>
                <a href="/module">
                    <button type="button" class="btn btn-sm btn-outline-primary btn-icon-text"><i class="fa fa-chevron-left text-dark btn-icon-prepend"></i> Kembali</button>
                </a>
            </nav>
        </div>
        <div class="card">
            <div class="card-body">
                <form action="" method="POST" class="form-sample" id="form">
                    @csrf
                    <input type="hidden" value="PUT" name="_method">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-stripped">
                                <tr>
                                    <th width="250px">Parent Module</th>
                                    <td>{{$data->module->name_module ?? "Parent"}}</td>
                                </tr>
                                <tr>
                                    <th width="250px">Module Name</th>
                                    <td>{{$data->name_module}}</td>
                                </tr>
                                <tr>
                                    <th width="250px">Module Link</th>
                                    <td>{{$data->link_module}}</td>
                                </tr>
                                <tr>
                                    <th width="250px">Icon</th>
                                    <td>{{$data->icon_module}}</td>
                                </tr>
                                <tr>
                                    <th width="250px">Order Number</th>
                                    <td>{{$data->order_module}}</td>
                                </tr>
                                <tr>
                                    <th width="250px">Action Module</th>
                                    <td>{{$data->action_module}}</td>
                                </tr>
                                <tr>
                                    <th width="250px">Description</th>
                                    <td>{{$data->description_module}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@endsection
