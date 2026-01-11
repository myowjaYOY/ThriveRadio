@extends('layouts.main')

@section('title')
    User Reported Station
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
            <div class="card-header">
                <div class="divider">
                    <div class="divider-text">
                        <h4>User Reported Station</h4>
                    </div>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    {{-- view radio station report start --}}
                    <table class="table-light" aria-describedby="mydesc" class='table-striped' id="table_list"
                        data-toggle="table" data-url="{{route('radio-station-report.show',1)}}" data-click-to-select="true"
                        data-responsive="true" data-side-pagination="server" data-pagination="true"
                        data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                        data-show-columns="true" data-show-refresh="true" data-fixed-columns="true" data-fixed-number="1"
                        data-fixed-right-number="1" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc"
                        data-pagination-successively-size="3" data-query-params="queryParams">

                        <thead>
                            <tr>
                                <th scope="col" data-field="id" data-align="center" data-sortable="true" data-visible="false">{{ __('Id') }}</th>
                                <th scope="col" data-field="no" data-align="center">{{ __('Sr.No') }}</th>
                                <th scope="col" data-field="radiostation" data-align="center" data-sortable="false">{{ __('Radio Station') }}</th>
                                <th scope="col" data-field="message" data-align="center" data-sortable="false">{{ __('Message') }}</th>
                                <th scope="col" data-field="date" data-align="center" data-sortable="false">{{ __('Date') }}</th>
                                <th scope="col" data-field="operate" data-align="center" data-sortable="false">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                    </table>
                    {{-- view radio station report end --}}
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script>
        function queryParams(p) {
            return {
                sort: p.sort, // Sort field
                order: p.order, // Sort order (asc or desc)
                offset: p.offset, // Data offset for pagination
                limit: p.limit, // Data limit per page
                search: p.search // Search query
            };
        }
    </script>
@endsection
