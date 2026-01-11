@extends('layouts.main')

@section('title')
    Manage Slider
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
                    <h4>Create Slider</h4>
                </div>
            </div>
            <div class="card-body">
                <!-- form slider start portion -->
                <form method="POST" class="create-form" action="{{ route('slider.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="col-sm-12 col-xs-12 form-group mandatory">
                                <label class="form-label col-md-12 col-sm-2 col-xs-12">Title</label>
                                <input class="form-control" type="text" id="title" name="title"
                                    value="{{ old('title') }}" required  placeholder="Title"/>
                            </div>
                        </div>
                        @if($city_mode == 1)
                            <div class="col-md-4">
                                <div class="col-sm-12 col-xs-12 col-sm-12 col-xs-12 form-group mandatory">
                                    <label for="city_id" class="form-label">City</label>
                                    <select name="city_id" id="city_id" class="form-control" required>
                                        <option value="">Select City</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-sm-12 col-xs-12 col-sm-12 col-xs-12  form-group mandatory">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select name="category_id" id="category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-sm-12 col-xs-12 col-sm-12 col-xs-12  form-group mandatory">
                                    <label for="radio_station_id" class="form-label">Radio
                                        Station</label>
                                    <select name="radio_station_id" id="radio_station_id"
                                        class="form-control" required>
                                        <option value="" disabled selected>Select Radio Station</option>
                                    </select>
                                </div>
                            </div>
                        @else
                            <div class="col-md-4">
                                <div class="col-sm-12 col-xs-12 form-group mandatory">
                                    <!-- Enter category-->
                                    <label for="city_id" class="form-label">Category</label>
                                    <select name="category_id" id="category_id" class="form-control"
                                        required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-sm-12 col-xs-12 form-group mandatory">
                                    <label for="city_id" class="form-label">Radio Station</label>
                                    <select name="radio_station_id" id="radio_station_id" class="form-control" required>
                                        <option value="" selected>Select Radio Station</option>
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-4">
                            <div class="col-sm-12 col-xs-12 card form-group mandatory">
                                <label for="image_name" class="form-label col-md-12 col-sm-2 col-xs-12">Image</label>
                                <input type="image" class="filepond" id="image" name="image" accept="image/*" required>
                            </div>
                        </div>
                        <div class="row  btn-right">
                            <div class="col-md-1 col-sm-12 col-xs-12 form-group">
                                <input id="btnadd" name="btnadd" value="Submit" type="submit" class="btn btn-primary">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="p-3 card">
            <div class="divider">
                <div class="divider-text">
                    <h4>List of Sliders</h4>
                </div>
            </div>
            <div class="card-body">

                <table class="table-light" aria-describedby="mydesc" class='table-striped' id="table_list"
                    data-toggle="table" data-url="{{url('slider/show')}}" data-click-to-select="true"
                    data-responsive="true" data-side-pagination="server" data-pagination="true"
                    data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                    data-show-columns="true" data-show-refresh="true" data-fixed-columns="true" data-fixed-number="1"
                    data-fixed-right-number="1" data-trim-on-search="false" data-sort-name="sequence" data-sort-order="asc"
                    data-pagination-successively-size="3" data-query-params="queryParams" data-use-row-attr-func="true"
                    data-reorderable-rows="true" data-maintain-selected="true"  data-mobile-responsive="true">

                    <thead>
                        <tr>
                            <th scope="col" data-field="id" data-align="center" data-sortable="true" data-visible="false">{{ __('Id') }}</th>
                            <th scope="col" data-field="no" data-align="center">{{ __('Sr.No') }}</th>
                            <th scope="col" data-field="sequence" data-align="center" data-sortable="false">{{ __('Sequence') }}</th>
                            @if($city_mode == 1)
                                <th scope="col" data-field="city_id" data-align="center" data-sortable="true" data-visible="false">{{ __('City Id') }}</th>
                                <th scope="col" data-field="city" data-align="center" data-sortable="false">{{ __('City') }}</th>
                            @endif
                            <th scope="col" data-field="category_id" data-align="center" data-sortable="true" data-visible="false">{{ __('Category Id') }}</th>
                            <th scope="col" data-field="category" data-align="center" data-sortable="false">{{ __('Category') }}</th>
                            <th scope="col" data-field="radio_station_id" data-align="center" data-sortable="true" data-visible="false">{{ __('Radio Station Id') }}</th>
                            <th scope="col" data-field="radiostation" data-align="center" data-sortable="false">{{ __('Radio Station') }}</th>
                            <th scope="col" data-field="title" data-align="center" data-sortable="false">{{ __('Title') }}</th>
                            <th scope="col" data-field="image" data-align="center" data-sortable="false" data-formatter="imageFormatter">{{ __('Image') }}</th>
                            <th scope="col" data-field="created_at" data-sortable="true" data-visible="false">{{__('Created at')}}</th>
                            <th scope="col" data-field="updated_at" data-sortable="true" data-visible="false">{{__('Updated at')}}</th>
                            <th scope="col" data-field="operate" data-events="sliderEvents" data-align="center" data-sortable="false">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                </table>
                <span class="d-block mb-4 mt-2 text-danger small">{{ __('Change Sequence by dragging rows') }}</span>
                <div class="mt-1">
                    <button id="change-sequence-slider" class="btn btn-primary">Update Rank</button>
                </div>
            </div>
        </div>
        <!-- View Category end portion -->
        <!-- Edit Category Modal start portion -->
        <div class="modal fade text-left" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel1">Edit Slider</h5>
                        <button type="button" class="close rounded-pill" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" id="formdata" enctype="multipart/form-data" class="edit-form" action="{{ route('slider.update',1) }}">
                            @csrf
                            <input type="hidden" name="id" id="id">
                            <div class="form-group">
                                <label class="mb-2 control-label col-md-12 col-sm-2 col-xs-12">Title</label>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <input class="form-control" type="text" id="edit_title" name="title"
                                        required />
                                </div>
                            </div>
                            @if($city_mode == 1)
                                <div class="form-group mandatory">
                                    <label class="mb-2 form-label col-md-12 col-sm-2 col-xs-12 ">City</label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <select name="city_id" id="edit_city_id"
                                            class="form-control" style="width:100%" autofocus
                                            required>
                                            <option value="">Select City</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="form-group mandatory">
                                <label class="mb-2 form-label col-md-12 col-sm-2 col-xs-12 ">Category</label>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <select name="category_id" id="edit_category_id"
                                        class="form-control dropdown_my_category" style="width:100%" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- Select Radio Station -->
                            <div class="form-group mandatory">
                                <label class="mb-2 form-label col-md-12 col-sm-2 col-xs-12 form-group mandatory">Radio
                                    Station</label>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <select name="radio_station_id" id="edit_radio_station_id"
                                        class="form-control dropdown_my_radio" style="width:100%" required>
                                        <option value="" selected>Select Radio Station</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 card edit_img">
                                <label for="image" class="mb-2 control-label col-md-12 col-sm-2 col-xs-12">
                                    Image</label>
                                <input accept="image/*" name='image' type='file' name="image" class="filepond" />
                                <div style="width: 100px;">
                                    <img src="" id="edit_image" class="img-fluid w-200" />
                                </div>
                            </div>
                            <div class="mt-4 form-group">
                                <!-- submit button -->
                                <div class="form-group text-end">
                                    <input id="btnupdate" name="btnupdate" value="Submit" type="submit"
                                        class="btn btn-primary">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script>
       window.sliderEvents = {
            'click .edit': function(e, value, row, index) {
                $('#id').val(row.id);
                $('#city_name').val(row.city_id);
                $('#category_name').val(row.category_id);
                $('#edit_radio_station_id').val(row.radio_station_id);
                $('#edit_title').val(row.title);
                $('#edit_image').attr('src',row.image);
                $('#sequence').val(row.sequence);

                // Trigger change events for all three dropdowns
                $('#edit_city_id').val(row.city_id).trigger('change.select2');
                $.ajax({
                    type: "GET",
                    url: "get-category/" + row.city_id,
                    dataType: 'json',
                    success: function(response) {
                        $('#edit_category_id').empty();
                        if (response.error == false) {
                            $('#edit_category_id').append($('<option>', {
                                value: '',
                                text: 'Select Category'
                            }));
                            $.each(response.data, function(i, item) {
                                var text_name = item.name;
                                $('#edit_category_id').append($('<option>', {
                                    value: item.id,
                                    text: text_name
                                }));
                            });
                            $('#edit_category_id').val(row.category_id).trigger('change.select2');
                        } else {
                            $('.dropdown_my_category').empty();
                        }
                    }
                });

                $('#edit_category_id').val(row.category_id).trigger('change.select2');
                if(row.city_id)
                {
                    $.ajax({
                        type: "GET",
                        url: "get-radio-stations/" + row.city_id + "/" + row.category_id,
                        dataType: 'json',
                        success: function(response) {
                            $('#edit_radio_station_id').empty();
                            if (response.error == false) {
                                $('#edit_radio_station_id').append($('<option>', {
                                    value: '',
                                    text: 'Select Radio Station'
                                }));
                                $.each(response.data, function(i, item) {
                                    var text_name = item.name;
                                    $('#edit_radio_station_id').append($('<option>', {
                                        value: item.id,
                                        text: text_name
                                    }));
                                });
                                $('#edit_radio_station_id').val(row.radio_station_id).trigger('change.select2');
                            } else {
                                $('.dropdown_my_radio').empty();
                            }
                        }
                    });
                }
                else{
                    $.ajax({
                        type: "GET",
                        url: "/get-radio-station-by-category/" + row.category_id,
                        dataType: 'json',
                        success: function(response) {
                            $('#edit_radio_station_id').empty();
                            if (response.error == false) {
                                $('#edit_radio_station_id').append($('<option>', {
                                    value: '',
                                    text: 'Select Radio Station'
                                }));
                                $.each(response.data, function(i, item) {
                                    var text_name = item.name;
                                    $('#edit_radio_station_id').append($('<option>', {
                                        value: item.id,
                                        text: text_name
                                    }));
                                });
                                $('#edit_radio_station_id').val(row.radio_station_id).trigger('change.select2');
                            } else {
                                $('.dropdown_my_radio').empty();
                            }
                        }
                    });
                }


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
        // hide image
        $(document).ready(function() {
            $('.edit_img').on('click', function() {
                var image = $('#blah');
                // Hide the image
                image.hide();
            });
        });
    </script>
@endsection
