<style>
    .file-action {
        visibility: hidden;
    }

    .file-card:hover .file-action {
        visibility: visible;
    }
</style>

<!-- TAB CONTENT START -->
<div class="col-xl-12 col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4">

    <x-form id="bankDetailForm">

        <x-forms.text 
            fieldLabel="{{ __('modules.skill.bank_number') }}" 
            fieldName="bank_number"
            fieldRequired="true" 
            fieldId="bank_number" 
        />

        <x-forms.text 
            fieldLabel="{{ __('modules.skill.account_name') }}" 
            fieldName="account_name"
            fieldRequired="true" 
            fieldId="account_name" 
        />

        <x-forms.number 
            fieldLabel="{{ __('modules.skill.account_number') }}" 
            fieldName="account_number"
            fieldRequired="true" 
            fieldId="account_number" 
        />

        <x-forms.text 
            fieldLabel="{{ __('modules.skill.ifsc_code') }}" 
            fieldName="ifsc_code"
            fieldRequired="true" 
            fieldId="ifsc_code" 
        />

        <x-forms.text 
            fieldLabel="{{ __('modules.skill.micr_number') }}" 
            fieldName="micr_number"
            fieldRequired="true" 
            fieldId="micr_number" 
        />

        <x-forms.select 
            fieldLabel="{{ __('modules.skill.account_type') }}" 
            fieldName="account_type" 
            fieldRequired="true" 
            fieldId="account_type"
        >
            <option value="">Select Account Type</option>
            <option value="saving">Saving</option>
            <option value="current">Current</option>
        </x-forms.select>  
        
        <button class="btn btn-primary mt-3">@lang('app.save')</button>
    </x-form>
</div>

<!-- TAB CONTENT END -->


@push('scripts')
<script>
    $('#bankDetailForm').on('submit', function (e) {
        e.preventDefault();

        let url = "{{ route('bank_detail.stores') }}";
        let formData = $(this).serialize();

        $.easyAjax({
            type: 'POST',
            url: url,
            data: formData,
            success: function (response) {
                // if (response.status === 'success') {
                    // Optional: Show success message or update UI
                    Swal.fire('Success', 'Bank details saved successfully!', 'success');
                // }
            }
        });
    });

</script>
@endpush