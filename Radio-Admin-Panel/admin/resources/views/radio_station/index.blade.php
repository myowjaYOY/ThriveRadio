@extends('layouts.main')
@section('title')
    Manage Radio Station
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
        <div class="card">
            <div class="divider mt-3">
                <div class="divider-text">
                    <h4>Create Radio Station</h4>
                </div>
            </div>
            <div class="card-body">
                <!-- form radio station start portion -->
                <form method="POST" class="create-form" action="{{route('radio-station.store')}}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <!-- select Radio sation Name-->
                            <div class="form-group mandatory">
                                <label class="mb-2 form-label">Radio Station
                                    Name</label>
                                <input class="form-control" type="text" id="radio_name" name="name"
                                    value="{{ old('name') }}" placeholder="Enter Radio Station Name" required autofocus />
                            </div>
                        </div>
                        @if($city_mode == 1)
                            <div class="col-md-4">
                                <div class="form-group mandatory">
                                    <!-- select city -->
                                    <label class="mb-2 form-label">City</label>
                                    <select name="city_id" class="form-control city_id"
                                        required>
                                        <option value="">Select City</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group mandatory">
                                <!-- select Category -->
                                <label class="mb-2 form-label">Category</label>
                                <select name="category_id" class="form-control category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mandatory">
                                <!-- Radio Stream URL -->
                                <label class="mb-2 form-label">Radio Stream URL</label>
                                <input class="form-control" type="text" id="radio_url" name="radio_url"
                                    value="{{ old('radio_stream_url') }}" placeholder="Enter Radio Stream URL" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card form-group">
                                <div class="form-group">
                                    <!-- Description -->
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="2" value="{{ old('description') }}" placeholder="Description"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mandatory">
                                <!-- Image -->
                                <label for="image" class="form-label">Image</label>
                                <input type="file" class="filepond" id="image" name="image" accept="image/*" required>
                            </div>
                        </div>
                    </div>
                    <div class="row  btn-right">
                        <div class="col-md-1 col-sm-12 col-xs-12 form-group">
                            <input id="btnadd" name="btnadd" value="Submit" type="submit" class="btn btn-primary">
                        </div>
                    </div>
                </form>
                <!-- form radio station start portion -->
            </div>
        </div>

        <div class="card p-3">
            <div class="divider">
                <div class="divider-text">
                    <h4>Lists of Radio Stations</h4>
                </div>
            </div>

            <div class="card-body">
                <!-- view radio station start portion -->
                <table class="table-light" aria-describedby="mydesc" class='table-striped' id="table_list"
                    data-toggle="table" data-url="{{url('radio-station/show')}}"
                    data-click-to-select="true" data-responsive="true" data-side-pagination="server" data-pagination="true"
                    data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                    data-show-columns="true" data-show-refresh="true" data-fixed-columns="true" data-fixed-number="2"
                    data-fixed-right-number="1" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc"
                    data-pagination-successively-size="3" data-mobile-responsive="true" data-query-params="queryParams">

                    <thead>
                        <tr>
                            <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('Id') }}</th>
                            <th scope="col" data-field="no">{{ __('Sr.No') }}</th>
                            <th scope="col" data-field="name" data-sortable="false">{{ __('Radio Station Name') }}</th>
                            @if($city_mode == 1)
                                <th scope="col" data-field="city_id" data-visible="false" data-sortable="false">{{ __('City ID') }}</th>
                                <th scope="col" data-field="city" data-sortable="false">{{ __('City') }}</th>
                            @endif
                            <th scope="col" data-field="category_id" data-visible="false" data-sortable="false">{{ __('Category ID') }}</th>
                            <th scope="col" data-field="category" data-sortable="false">{{ __('Category') }}</th>
                            <th scope="col" data-field="radio_url" data-sortable="false">{{ __('Radio Stream URL') }}</th>
                            <th scope="col" data-field="image" data-formatter="imageFormatter" data-sortable="false">{{ __('Image') }}</th>
                            <th scope="col" data-field="description" data-sortable="false">{{ __('Description') }}</th>
                            <th scope="col" data-field="created_at" data-sortable="true" data-visible="false">{{__('Created at')}}</th>
                            <th scope="col" data-field="updated_at" data-sortable="true" data-visible="false">{{__('Updated at')}}</th>
                            <th scope="col" data-field="operate" data-events="actionEvents" data-sortable="false">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                </table>
                <!-- view radio station end portion -->
            </div>
        </div>
        <!-- model edit radio station form start portion -->
        <div class="modal fade text-left" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel1">Edit Radio Station</h5>
                    <button type="button" class="close rounded-pill" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" id="formdata" enctype="multipart/form-data" class="form edit-form" action="{{route('radio-station.update',1)}}">
                            @csrf
                            <input type="hidden" name="id" id="id">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                    {{-- enter radio station name --}}
                                    <label class="mb-2 form-label col-md-12 col-sm-12 col-xs-12">Radio Station Name</label>
                                    <input class="form-control" type="text" id="edit_radio_name" name="name"
                                        placeholder="Enter Radio Station Name" autofocus required />
                                </div>
                                @if($city_mode == 1)
                                <div class="col-md-12 col-sm-12 col-xs-12 form-group mandatory">
                                    <label class="mb-2 form-label col-md-4 col-sm-12 col-xs-12">City</label>
                                    <select name="city_id" id="edit_city_id"
                                        class="form-control select2" style="width:100%" required>
                                        <option value="">Select City</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="col-md-12 col-sm-12 col-xs-12 form-group mandatory">
                                    {{-- enter category name --}}
                                    <label class="mb-2 form-label col-md-4 col-sm-12 col-xs-12">Category</label>
                                    <select name="category_id" id="edit_category_id" class="form-control" style="width:100%" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                    {{-- enter radio stream url --}}
                                    <label class="mb-2 form-label col-md-4 col-sm-12 col-xs-12">Radio URL</label>
                                    <input class="form-control" type="text" id="edit_radio_url" name="radio_url"
                                        placeholder="Enter Radio stream URL" required />
                                </div>
                                <div class="col-md-12 mt-3">
                                    <div class="form-group">
                                        <!-- Description -->
                                        <label for="description" class="mb-2">Description</label>
                                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
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
                            </div>
                            <div class="row">
                                <div class="text-end form-group">
                                    <input id="btnupdate" name="btnupdate" value="Submit" type="submit"
                                        class="btn btn-primary">
                                </div>
                            </div>
                        </form>
                        <!-- model edit radio station form start portion -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script>
        window.actionEvents = {
            'click .edit': function(e, value, row, index) {
                // Populate the edit form fields with row data
                $('#id').val(row.id);
                $('#edit_city_id').val(row.city_id);
                $('#city_name').val(row.city_name);
                $('#edit_category_id').val(row.category_id);
                $('#category_name').val(row.category_name);
                $('#edit_radio_name').val(row.name);
                $('#edit_radio_url').val(row.radio_url);
                $('#edit_description').val(row.description);

                $('#edit_image').attr('src',row.image);
                // Trigger change events for both dropdowns
                $('#edit_city_id').val(row.city_id).trigger('change.select2');
                $('#edit_category_id').val(row.category_id).trigger('change.select2');

            }
        };
        // Function to customize query parameters for table data retrieval
        function queryParams(p) {
            return {
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                limit: p.limit,
                search: p.search
            };
        }
        // pop-up image
        $(document).ready(function() {
            $('.edit_img').on('click', function() {
                var image = $('#blah');
                // Hide the image

                image.hide();
            });
        });
    </script>
@endsection
