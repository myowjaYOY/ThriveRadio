@extends('layouts.main')

@section('title')
    Manage Category
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

        <div class="p-3 mb-4 card">
            <div class="divider">
                <div class="divider-text">
                    <h4>Create Category</h4>
                </div>
            </div>
            <div class="card-body">
                <!-- form category start portion -->
                <form method="POST" class="general-form create-form" action="{{route('category.store')}}" id="create-form"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mandatory">
                                <!-- Enter Category Name -->
                                <label for="category_name" class="p-1 mb-1 form-label">Category Name</label>
                                <input class="form-control" type="text" name="name"
                                    value="{{ old('category_name') }}" placeholder="Enter Category Name" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card form-group mandatory">
                                <!-- Image -->
                                <label for="image" class="form-label col-md-4 col-sm-12 col-xs-12">Image</label>
                                <input type="file" class="filepond" id="image" name="image" required accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="row btn-right">
                        <div class="col-md-1 col-sm-12 col-xs-12 form-group">
                            <!-- submit button -->
                            <div class="mt-5">
                                <input value="Submit" type="submit" class=" btn btn-primary text-right">
                            </div>
                        </div>
                    </div>

            </div>
            </form>
            <!-- form category end portion -->
        </div>
        <!-- view category start portion -->
        <div class="card p-3">
            <div class="divider">
                <div class="divider-text">
                    <h4>Lists of Categories</h4>
                </div>
            </div>
            <div class="card-body">

                <table class="table-light" aria-describedby="mydesc" class='table-striped' id="table_list"
                    data-toggle="table" data-url="{{url('category/show')}}" data-click-to-select="true"
                    data-responsive="true" data-side-pagination="server" data-pagination="true"
                    data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                    data-show-columns="true" data-show-refresh="true" data-fixed-columns="true" data-fixed-number="1"
                    data-fixed-right-number="1" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc"
                    data-pagination-successively-size="3" data-query-params="queryParams">
                    <thead>
                        <tr>
                            <th data-field="id" data-align="center" data-sortable="true" data-visible="false">{{ __('Id') }}</th>
                            <th data-field="no" data-align="center">{{ __('Sr.No') }}</th>
                            <th data-field="name" data-align="center" data-sortable="false">{{ __('Category') }}</th>
                            <th data-field="image" data-formatter="imageFormatter" data-sortable="false">{{ __('Image') }}</th>
                            <th data-field="created_at" data-sortable="true" data-visible="false">{{__('Created at')}}</th>
                            <th data-field="updated_at" data-sortable="true" data-visible="false">{{__('Updated at')}}</th>
                            <th data-field="operate" data-events="actionEvents" data-align="center" data-sortable="false">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                </table>
                <!-- View Category end portion -->

            </div>

            <div class="modal fade text-left" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
                aria-hidden="true">
                <!-- Edit Category Modal start portion -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel1">Edit Category</h5>
                            <button type="button" class="close rounded-pill" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i data-feather="x"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" class="edit-form" action="{{route('category.update',1)}}"
                                id="edit_category" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="edit_id" id="edit_id">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 form-group mandatory">
                                        {{-- enter category name --}}
                                        <label for="category_name" class="mb-2 form-label">Category Name</label>
                                        <input class="form-control" type="text" id="edit_name"
                                            name="edit_name" required />
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3 card edit_img">
                                    {{-- select image --}}
                                    <label for="image" class="col-12 form-label">Image</label>
                                    <input accept="image/*" name='image' type='file' id="image"
                                        name="image" class="filepond" />
                                    <div style="width: 100px;">
                                        <img src="" id="edit_image" class="img-fluid w-200" />
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12 mt-4 text-end">
                                    <div class="form-group">
                                        <input id="btnupdate" name="btnupdate" value="Submit" type="submit"
                                            class="btn btn-primary">
                                    </div>
                                </div>
                            </form>
                            <!-- Edit Category Modal end portion -->
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection
@section('script')
    <script>
        // Define custom action events for the table
        window.actionEvents = {
            'click .edit': function(e, value, row, index) {
                // Populate the edit form fields with row data
                $('#edit_id').val(row.id);
                $('#edit_name').val(row.name);
                $('#edit_image').attr('src',row.image);
                $('#edit_city_id').val(row.city_id).trigger('change.select2');
            }
        };
        // Function to customize query parameters for table data retrieval
        function queryParams(p) {
            return {
                sort: p.sort, // Sort field
                order: p.order, // Sort order (asc or desc)
                offset: p.offset, // Data offset for pagination
                limit: p.limit, // Data limit per page
                search: p.search // Search query
            };
        }

        $(document).ready(function() {
            $('.edit_img').on('click', function() {
                var image = $('#blah');
                // Hide the image
                image.hide();

            });
        });
    </script>
@endsection
