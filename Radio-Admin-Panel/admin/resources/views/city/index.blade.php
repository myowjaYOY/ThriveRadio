@extends('layouts.main')

@section('title')
    Manage Cities
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
        <div class="p-3 card">
            <div class="divider">
                <div class="divider-text">
                    <h4>Create City</h4>
                </div>
            </div>
            <div class="card-body">
                <!-- form city start portion -->
                <form method="POST" class="general-form create-form" action="{{ route('city.store') }}" id="create-form" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-sm-4 mandatory form-group">
                            <label class="form-label col-md-12 col-sm-12">Name</label>
                            <input class="form-control" type="text" id="name" name="name" value="{{ old('city_name') }}" placeholder="Enter City Name" autofocus required />
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label col-md-12 col-sm-12">&nbsp;</label>
                            <input id="create-city" value="Submit" type="submit" class="btn btn-primary">
                        </div>
                    </div>
                </form>
                <!-- form city end portion -->
            </div>
        </div>
        <div class="card p-3">
            <div class="divider">
                <div class="divider-text">
                    <h4>Lists of Cities</h4>
                </div>
            </div>
            <div class="card-body">
                <!-- view city Start portion -->

                <table class="table-light" aria-describedby="mydesc" class='table-striped' id="table_list"
                    data-toggle="table" data-url="{{url('city/show')}}" data-click-to-select="true"
                    data-responsive="true" data-side-pagination="server" data-pagination="true"
                    data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                    data-show-columns="true" data-show-refresh="true" data-fixed-columns="true" data-fixed-number="1"
                    data-fixed-right-number="1" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc"
                    data-pagination-successively-size="3" data-query-params="queryParams">
                    <thead>
                        <tr>
                            <th scope="col" data-field="id" data-align="center" data-sortable="true" data-visible="false">{{ __('Id') }}</th>
                            <th scope="col" data-field="no" data-align="center">{{ __('Sr.No') }}</th>
                            <th scope="col" data-field="name" data-align="center" data-sortable="false">{{ __('City') }}</th>
                            <th scope="col" data-field="created_at" data-sortable="true" data-visible="false">{{__('Created at')}}</th>
                            <th scope="col" data-field="updated_at" data-sortable="true" data-visible="false">{{__('Updated at')}}</th>
                            <th scope="col" data-align="center" data-field="operate" data-sortable="false" data-events="actionEvents">{{__('Action')}}</th>
                        </tr>
                    </thead>
                </table>

                <!-- view city end portion -->
            </div>
        </div>

        <!-- start :: edit model -->
        <div class="modal fade text-left" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel1">Edit City</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <form method="POST" id="edit-form" enctype="multipart/form-data" class="form d-flex flex-column flex-lg-row fv-plugins-bootstrap5 fv-plugins-framework form-edit"
                    action="{{route('city.update',1)}}">
                        @csrf
                        <div class="modal-body">
                                <input type="hidden" name="edit_id" id="edit_id">
                                <div class="row col-12">
                                    <div class="form-group mandatory">
                                        <label class="form-label col-md-12 col-sm-12">Name</label>
                                        <input class="form-control" type="text" id="edit_name" name="name" placeholder="Enter City Name" />
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12 mt-4 text-end">
                                        <div class="form-group">
                                            <input value="Submit" type="submit" class="btn btn-primary">
                                        </div>
                                    </div>
                                </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end :: edit model -->
    </section>
@endsection
@section('script')
    <script>
        window.actionEvents = {
            'click .edit': function(e, value, row, index) {

                $('#edit_id').val(row.id);
                $('#edit_name').val(row.name);
            }
        };

        function queryParams(p) {
            return {
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                limit: p.limit,
                search: p.search
            };
        }
    </script>
@endsection
