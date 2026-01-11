@extends('layouts.main')

@section('title')
    Manage Notification
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
                    <h4>Create Notification
                    </h4>
                </div>
            </div>
            <!-- start form -->
            <form method="POST" class="notification-form" action="{{route('notification.store')}}" id="create-form" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="col-sm-12 col-xs-12 form-group mandatory">
                            <label class="form-label" for="title">Title</label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Title" required class="form-control">
                        </div>
                    </div>
                    @if($city_mode == 1)
                        <div class="col-md-4">
                            <div class="col-sm-12 col-xs-12 form-group mandatory">
                                <label for="city_id" class="form-label">City</label>
                                <select name="city_id" id="city_id" class="form-control" required autofocus>
                                    <option value="">Select City</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="col-sm-12 col-xs-12 form-group mandatory">
                                <!-- Enter category-->
                                <label for="city_id" class="form-label">Category</label>
                                <select name="category_id" id="category_id" class="form-control"
                                    required>
                                    <option value="">Select Category</option>

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
                        <div class="col-sm-12 col-xs-12 form-group mandatory mt-1">
                            <label class="form-label col-md-12 col-sm-2 col-xs-12" for="message">Message</label>
                            <textarea id="message" name="message" rows="2" value="{{ old('message') }}" required class="form-control" placeholder="Message"></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-sm-12 col-xs-12 card form-group">
                            <label for="image" class="form-label col-sm-2 col-xs-12">Image</label>
                            <input type="image" class="filepond" id="image" name="image" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="row  btn-right">
                    <div class="col-md-1 col-sm-12 col-xs-12 form-group">
                        <input id="btnadd" name="btnadd" value="Submit" type="submit" class="btn btn-primary">
                    </div>
                </div>
            </form>
            {{-- end form --}}
        </div>
        <!-- view send notification -->
        <div class="card">
            <div class="card-body">
                <table class="table-light" aria-describedby="mydesc" class='table-striped' id="table_list"
                    data-toggle="table" data-url="{{route('notification.show',1)}}" data-click-to-select="true"
                    data-responsive="true" data-side-pagination="server" data-pagination="true"
                    data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                    data-show-columns="true" data-show-refresh="true" data-fixed-columns="true" data-fixed-number="1"
                    data-fixed-right-number="1" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc"
                    data-pagination-successively-size="3" data-query-params="queryParams">
                    <thead>
                        <tr>
                            <th scope="col" data-field="id" data-align="center" data-sortable="true" data-visible="false">{{ __('Id') }}</th>
                            <th scope="col" data-field="no" data-align="center">{{ __('Sr.No') }}</th>
                            @if($city_mode == 1)
                                <th scope="col" data-field="city_id" data-align="center" data-sortable="true" data-visible="false">{{ __('City Id') }}</th>
                                <th scope="col" data-field="city" data-align="center" data-sortable="false">{{ __('City') }}</th>
                            @endif
                            <th scope="col" data-field="category_id" data-align="center" data-sortable="true" data-visible="false">{{ __('Category Id') }}</th>
                            <th scope="col" data-field="category" data-align="center" data-sortable="false">{{ __('Category') }}</th>
                            <th scope="col" data-field="radio_station_id" data-align="center" data-sortable="true" data-visible="false">{{ __('Radio Station Id') }}</th>
                            <th scope="col" data-field="radiostation" data-align="center" data-sortable="false">{{ __('Radio Station') }}</th>
                            <th scope="col" data-field="title" data-align="center" data-sortable="false">{{ __('Title') }}</th>
                            <th scope="col" data-field="date" data-align="center" data-sortable="false">{{ __('Date') }}</th>
                            <th scope="col" data-field="image" data-align="center" data-sortable="false" data-formatter="imageFormatter">{{ __('Image') }}</th>
                            <th scope="col" data-field="operate" data-align="center" data-sortable="false">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        {{-- end view send notification --}}
    </section>
@endsection
@section('script')
    <script>
        function queryParams(p) {
            return {
                sort: p.sort, // Sort field (column) name
                order: p.order, // Sort order (asc or desc)
                offset: p.offset, // Offset for pagination
                limit: p.limit, // Number of records per page
                search: p.search // Search keyword
            };
        }
    </script>
@endsection
