@extends("admin.layouts.layout")

@section("title", "Deposit Company List")

@section("page_style")
    <link rel="stylesheet" type="text/css" href="{{ url('dist/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endsection

@section("content")
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <!-- <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    </div> -->
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">{{ __('message_lang.LBL_DPST_COMPANIES') }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <!--begin::Button-->
                        <a href="{{ route('admin-add-deposit-company') }}" class="btn btn-primary font-weight-bolder">
                        </span>{{ __('message_lang.LBL_ADD_DPST_CO') }}</a>
                        <!--end::Button-->
                    </div>
                </div>
                <div class="card-body">
                    <!--begin: Datatable-->
                    <table id="example" class="table table-bordered grid-table nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('message_lang.LBL_COUNTRY_NAMES') }}</th>
                                <th>{{ __('message_lang.LBL_CO_NAME') }}</th>
                                <th>{{ __('message_lang.LBL_LOGO') }}</th>
                                <th>{{ __('message_lang.LBL_MAX_REC_AMT') }}</th>
                                <th>{{ __('message_lang.LBL_STATUS') }}</th>
                                <th>{{ __('message_lang.LBL_ACTIONS') }}</th>
                            </tr>
                        </thead>
                    </table>
                    <!--end: Datatable-->
                </div>
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
@endsection

@section("page_vendors")
    <script src="{{ url('dist/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endsection

@section("page_script")
<script type="text/javascript">
    var list_table_one;
    $(document).ready(function ()
    {
        if ($('#example').length > 0)
        {
            list_table_one = $('#example').DataTable(
            {
                processing: true,
                serverSide: true,
                "responsive": true,
                "aaSorting": [],
                "ajax":
                {
                    "url": "{{ route('admin-list-fetch-deposit-company') }}",
                    "type": "POST",
                    "dataType": "json",
                    "data":
                    {
                        _token: "{{csrf_token()}}"
                    }
                },
                "columnDefs": [
                    {
                        "targets": [0, 3, 5], //first column / numbering column
                        "orderable": false, //set not orderable
                    },
                ],
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'country_names',
                        name: 'country_names'
                    },
                    {
                        data: 'company_name',
                        name: 'company_name'
                    },
                    {
                        data: 'logo_file_storage_location',
                        name: 'logo_file_storage_location'
                    },
                    {
                        data: 'max_rec_amt',
                        name: 'max_rec_amt'
                    },
                    {
                        data: 'dcs_status',
                        name: 'dcs_status'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                ]
            });
        }
    });

    function change_status(a_object)
    {
        var status = $(a_object).data("status");
        var id = $(a_object).data("id");
        $.ajax(
        {
            "url": "{!! route('change-deposit-company-status') !!}",
            "dataType": "json",
            "type": "POST",
            "data":
            {
                id: id,
                status: status,
                _token: "{{csrf_token()}}"
            },
            success: function (response)
            {
                if (response.status == "success")
                {
                    list_table_one.ajax.reload(null, false); //reload datatable ajax
                    toastr.success('{{ __('message_lang.STATUS_CHANGED_SUCCESSFULLY') }}', 'Success');
                }
                else
                {
                    toastr.error('{{ __('message_lang.FAILED_TO_UPDATE_STATUS') }}', 'Error');
                }
            }
        });
    }
</script>
@endsection