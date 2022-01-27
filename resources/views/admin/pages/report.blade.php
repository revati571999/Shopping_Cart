@extends('admin.master')
@section('content')
<div class="content-wrapper">
<div class="container mt-5">
<div class="container-fluid">
                <div class="row mb-2">
                    <div class="card col-sm-6">
                        <div class="card-header">
                            User Reports:
                        </div>
                        <p class="card-body">
                            <a href="{{route('usercsv')}}" class="btn btn-secondary" id="export" >User Download</a>
                        </p>
                    </div>
                    <div class="card col-sm-6">
                        <div class="card-header">
                            Order Reports:
                        </div>
                        <p class="card-body">
                            <a href="{{route('ordercsv')}}" class="btn btn-secondary" id="export" >Order Download</a>
                        </p>
                    </div>
                </div>
</div>
            </div>
</div>
@endsection
<style>
    .w-5{
        display:none;
    }
</style>