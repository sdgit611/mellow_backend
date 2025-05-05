<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('modules.education.editEducation')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>

<div class="modal-body">
    <x-form id="save-education-form">

        <div class="row">
            <input type="hidden" name="id" id="id" value="{{ $education->id ?? '' }}">
            <div class="col-md-12">
                <x-forms.text 
                    fieldLabel="{{ __('modules.education.degree') }}" 
                    fieldName="degree"
                    fieldRequired="true" 
                    fieldId="degree" 
                    fieldValue="{{$education->degree}}" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.text 
                    fieldLabel="{{ __('modules.education.institution') }}" 
                    fieldName="institution"
                    fieldRequired="true" 
                    fieldId="institution" 
                    fieldValue="{{$education->collage_name}}" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.text 
                    fieldLabel="{{ __('modules.education.passing_year') }}" 
                    fieldName="year"
                    fieldRequired="true" 
                    fieldId="year" 
                    fieldPlaceholder="e.g. 2020"
                    fieldValue="{{$education->passing_year}}" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.text 
                    fieldLabel="{{ __('modules.education.percentage') }}" 
                    fieldName="percentage"
                    fieldRequired="true" 
                    fieldId="percentage"
                    fieldValue="{{$education->percentage}}" 
                />
            </div>

        </div>
    </x-form>
</div>

<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">
        @lang('app.cancel')
    </x-forms.button-cancel>
    <x-forms.button-primary id="submit-education" icon="check">
        @lang('app.save')
    </x-forms.button-primary>
</div>

<script>
    $('#submit-education').click(function () {
      
        var url = "{{ route('education.update') }}";

        $.easyAjax({
            url: url,
            container: '#save-education-form',
            type: "POST",
            disableButton: true,
            buttonSelector: "#submit-education",
            data: $('#save-education-form').serialize(),
            success: function (response) {
                if (response.status == 'success') {
                    $(MODAL_DEFAULT).modal('hide');
                    $('#education-list').html(response.view);
                }
                // window.location.reload();
            }
        });
    });

    init('#save-education-form');
</script>
