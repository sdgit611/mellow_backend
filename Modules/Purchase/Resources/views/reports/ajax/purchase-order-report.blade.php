@section('content')
<!-- CONTENT WRAPPER START -->
<div class="content-wrapper">
    <form action="" id="filter-form">
            <div class="d-flex my-3">
                <!-- DATE START -->
                <div class="select-box py-2 px-0">
                    <div class="select-box d-flex pr-2 border-right-grey border-right-grey-sm-0">
                        <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.duration')</p>
                        <div class="select-status d-flex">
                            <input type="text" class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                                id="datatableRange2" placeholder="@lang('placeholders.dateRange')">
                        </div>
                    </div>
                </div>
                <!-- DATE END -->
                <div class="select-box d-flex  py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
                    <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('purchase::app.vendor')</p>
                    <div class="select-status">
                        <select class="form-control select-picker" name="vendor" id="vendor" data-live-search="true"
                            data-size="8">
                            <option value="all">@lang('app.all')</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{$vendor->id}}">{{$vendor->primary_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- RESET START -->
                <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0 h-25">
                    <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                        @lang('app.clearFilters')
                    </x-forms.button-secondary>
                </div>
                <!-- RESET END -->
            </div>
    </form>
    <!-- Task Box Start -->
    <div class="d-flex flex-column w-tables rounded mt-4 bg-white">

        {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

    </div>
    <!-- Task Box End -->
</div>
<!-- CONTENT WRAPPER END -->

@endsection

@push('scripts')
    @include('sections.datatable_js')


    <script type="text/javascript">

        function getDate()  {
            var start = moment().clone().startOf('month');
            var end = moment();

            $('#datatableRange2').daterangepicker({
                locale: daterangeLocale,
                linkedCalendars: false,
                startDate: start,
                endDate: end,
                ranges: daterangeConfig
            }, cb);
        }
        $(function() {
            getDate()
            $('#datatableRange2').on('apply.daterangepicker', function(ev, picker) {
                showTable();
            });

        });

    </script>

    <script>
        $('#purchase-order-report-table').on('preXhr.dt', function(e, settings, data) {

            var dateRangePicker = $('#datatableRange2').data('daterangepicker');
            var startDate = $('#datatableRange2').val();

            if (startDate == '') {
                startDate = null;
                endDate = null;
            } else {
                startDate = dateRangePicker.startDate.format('{{ company()->moment_date_format }}');
                endDate = dateRangePicker.endDate.format('{{ company()->moment_date_format }}');
            }

            var vendor = $('#vendor').val();

            data['startDate'] = startDate;
            data['endDate'] = endDate;
            data['vendor'] = vendor;
        });
        const showTable = () => {
            window.LaravelDataTables["purchase-order-report-table"].draw(false);
        }

        $('#vendor').on('change keyup',
            function() {
                if ($('#vendor').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else {
                    $('#reset-filters').addClass('d-none');
                    showTable();
                }
            });

        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();
            getDate()

            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });

        $('#reset-filters-2').click(function() {
            $('#filter-form')[0].reset();

            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });

    </script>
@endpush
