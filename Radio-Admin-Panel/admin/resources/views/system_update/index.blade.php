@extends('layouts.main')

@section('title')
    System Update
@endsection

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h4>@yield('title')</h4>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
            </div>
        </div>
    </div>
@endsection
@section('content')
    <section class="section">
        <div class="p-2 card">
            <div class="divider">
                <div class="divider-text">
                    <h4>System Update</h4>
                </div>
            </div>
            <div class="card-body">
                <form class="system-update" action="{{ url('system-update') }}" id="system-update" method="POST" novalidate="novalidate">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-sm-12 col-xs-12 form-group mandatory">
                                <label class="form-label col-md-12 col-sm-2 col-xs-12">Purchase Code</label>
                                <input class="form-control" type="text" id="purchase_code" name="purchase_code"
                                    value="{{ old('purchase_code') }}" required  placeholder="Purchase Code"/>
                            </div>
                        </div>
                        <div class="form-group col-sm-12 col-md-12">
                            <label>{{ __('Files') }} <span class="text-danger">* <small>(Only Zip File is allowed)</small></span></label>
                            <input type="file" name="file" class="form-control"/>
                            <small>Your Current Version is {{isset($system_version['message']) ? $system_version['message'] :''}}, Please update nearest version here if available</small>
                            <br>
                            <br>
                            <small>NOTE:- Make sure you update system in sequence. Like if you have current version 2.0.0 and you want to update this version to 2.0.4 then you can't update it directly. You must have to update in sequence like first update version 2.0.1 then 2.0.2 and so on.
                            </small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-1 col-sm-12 col-xs-12 form-group">
                            <input id="btnadd" name="btnadd" value="Submit" type="submit" class="btn btn-primary">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
