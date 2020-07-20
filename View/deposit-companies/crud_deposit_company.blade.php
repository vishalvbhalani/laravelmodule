@extends("admin.layouts.layout")

@if(isset($deposit_company_id))
    @section("title", "Update Deposit Company")
@else
    @section("title", "Add Deposit Company")
@endif

@section("page_style")

@endsection

@section("content")
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b example example-compact">
                        <div class="card-header">
                            <h3 class="card-title">
                                @if(isset($deposit_company_id))
                                    {{ __('message_lang.LBL_UPDATE_DPST_CO') }}
                                @else
                                    {{ __('message_lang.LBL_ADD_DPST_CO') }}
                                @endif
                            </h3>
                        </div>
                        <!--begin::Form-->
                        <form method="post" name="frm_crud_deposit_company" id="frm_crud_deposit_company" action="<?php
                        if(isset($deposit_company_id))
                        {
                        ?>{{ route('admin-update-deposit-company-post', $deposit_company_id) }}<?php
                        }
                        else
                        {
                        ?>{{ route('admin-add-deposit-company-post') }}<?php
                        }
                        ?>" enctype="multipart/form-data">

                            @csrf

                            <div class="card-body">
                                <div class="form-group">
                                    <label class="" for="country_id">{{ __('message_lang.LBL_COUNTRY') }}</label>

                                    <?php
                                        if(isset($deposit_company_id))
                                        {
                                            $country_ids_array = explode(",", $deposit_company->country_ids);
                                        }
                                    ?>

                                    <select class="form-control @error('country_id') is-invalid @enderror custom-field" name="country_id[]" id="country_id" autocomplete="country_id" multiple>

                                        <?php
                                        foreach($countries as $key => $value)
                                        {
                                        ?>
                                            <option value="{{ $value->id }}" 
                                            @if(old('country_id'))
                                                {{ (in_array($value->id, old("country_id")) ? "selected":"") }}
                                            @else
                                                @if( isset($country_ids_array) && in_array($value->id, $country_ids_array) )
                                                    selected
                                                @endif
                                            @endif>{{ $value->country_name }}</option>
                                        <?php
                                        }
                                        ?>

                                    </select>
                                    @error('country_id')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="" for="company_name">{{ __('message_lang.LBL_CO_NAME') }}</label>
                                    <input class="form-control @error('company_name') is-invalid @enderror custom-field" id="company_name" type="text" value="{{ old('company_name', @$deposit_company->company_name) }}" name="company_name" placeholder="{{ __('message_lang.LBL_CO_NAME') }}" >
                                    @error('company_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="" for="max_rec_amt">{{ __('message_lang.LBL_MAX_REC_AMT') }}</label>
                                    <input min="0.01" step="0.01" onkeyup="if(this.value<0){this.value= this.value * -1}" class="form-control @error('max_rec_amt') is-invalid @enderror custom-field" id="max_rec_amt" name="max_rec_amt" value="{{ old('max_rec_amt', @$deposit_company->max_rec_amt) }}" type="number" placeholder="{{ __('message_lang.LBL_MAX_REC_AMT') }}" >
                                    @error('max_rec_amt')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="logo_file_storage_location">{{ __('message_lang.LBL_LOGO') }}</label>
                                    <div class="custom-file">
                                        <input class="custom-file-input" id="logo_file_storage_location" name="logo_file_storage_location" type="file" onchange="readURL(this);">
                                        <label class="custom-file-label" for="logo_file_storage_location">{{ __('message_lang.CHOOSE_FILE') }}</label>
                                    </div>
                                    <small class="form-text text-muted">{{ __('message_lang.JPG_PNG_MAX_FILE_SIZE') }}</small>

                                    @error('logo_file_storage_location')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                    <div class="row">
                                        <div class="col-md-6">
                                            @if(isset($deposit_company) && $deposit_company->logo_file_storage_location != '')
                                                <img onerror="this.onerror=null;this.src='{{ URL::to('/') }}/public/uploads/default/100_no_img.jpg';" src="{{ URL::to('/') }}/img/{{ $deposit_company->logo_file_storage_location }}" 
                                                style="height: 200px; width: 200px;">
                                            @else
                                                <img onerror="this.onerror=null;this.src='{{ URL::to('/') }}/public/uploads/default/100_no_img.jpg';" src="{{ URL::to('/') }}/public/uploads/default/100_no_img.jpg" style="height: 200px; width: 200px;">
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <img id="logo_file_storage_location_preview" src="" alt="" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary mr-2">{{ __('message_lang.BTN_SUBMIT') }}</button>
                                <a href="{{ route('admin-deposit-companies') }}" class="btn btn-secondary">{{ __('message_lang.BUTTON_CANCEL') }}</a>
                            </div>
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Card-->
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
@endsection

@section("page_vendors")
<script src="{{ asset('ex_plugins/jquery-validation-1.19.1/dist/jquery.validate.js') }}"></script>
@endsection

@section("page_script")
<script type="text/javascript">
    $(document).ready(function ()
    {
        $("#frm_crud_deposit_company").validate(
        {
            errorElement: "span", // contain the error msg in a span tag
            errorClass: 'help-block',
            errorPlacement: function (error, element)
            {
                // render error placement for each input type
                error.insertAfter(element);
                // for other inputs, just perform default behavior
            },
            ignore: "",
            rules:
            {
                'country_id[]':
                {
                    required: true
                },
                company_name:
                {
                    required: true
                },
                max_rec_amt:
                {
                    required: true,
                    number: true
                },
            },
            messages:
            {
                'country_id[]':
                {
                    required: '{{ __('message_lang.PLS_SEL_COUNTRY') }}'
                },
                company_name:
                {
                    required: '{{ __('message_lang.PLS_ENT_SENDING_CO_NAME') }}'
                },
                max_rec_amt:
                {
                    required: '{{ __('message_lang.PLS_ENT_MAX_REC_AMT') }}',
                    number: '{{ __('message_lang.PLEASE_ENTER_NUMBER_ONLY') }}'
                }
            },
            invalidHandler: function (event, validator)
            {
                //display error alert on form submit
                // debugger;
                //successHandler1.hide();
                //errorHandler1.show();
            },
            highlight: function (element)
            {
                // debugger;
                $(element).closest('.help-block').removeClass('valid');
                // display OK icon
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');
                // add the Bootstrap error class to the control group
            },
            unhighlight: function (element)
            {
                // revert the change done by hightlight
                // debugger;
                $(element).closest('.form-group').removeClass('has-error');
                // set error class to the control group
            },
            success: function (label, element)
            {
                // debugger;
                label.addClass('help-block valid');
                // mark the current input as valid and display OK icon
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success').find('.symbol').removeClass('required').addClass('ok');
            },
            submitHandler: function (frmadd)
            {
                // debugger;
                successHandler1.show();
                errorHandler1.hide();
            }
        });
    });

    function readURL(input)
    {
        if (input.files && input.files[0])
        {
            var reader = new FileReader();

            reader.onload = function (e)
            {
                $('#logo_file_storage_location_preview').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection