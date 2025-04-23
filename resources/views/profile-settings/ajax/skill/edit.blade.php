<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('modules.skill.addskill')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>

<div class="modal-body">
    <x-form id="save-skill-form">
    <input type="hidden" name="id" id="id" value="{{ $skill->id ?? '' }}">
    <x-forms.text 
    fieldLabel="{{ __('modules.skill.technology') }}" 
    fieldName="technology"
    fieldRequired="true" 
    fieldId="technology" 
    fieldValue="{{$skill->name}}" 
/>

<x-forms.number 
    fieldLabel="{{ __('modules.skill.total') }}" 
    fieldName="total"
    fieldRequired="true" 
    fieldId="total"
    fieldValue="{{$skill->total}}"  
/>

    </x-form>
</div>

<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">
        @lang('app.cancel')
    </x-forms.button-cancel>
    <x-forms.button-primary id="submit-skill" icon="check">
        @lang('app.save')
    </x-forms.button-primary>
</div>

<script>
    $('#submit-skill').click(function () {
      
        var url = "{{ route('skill.update') }}";

        $.easyAjax({
            url: url,
            container: '#save-skill-form',
            type: "POST",
            disableButton: true,
            buttonSelector: "#submit-skill",
            data: $('#save-skill-form').serialize(),
            success: function (response) {
                if (response.status == 'success') {
                    $(MODAL_DEFAULT).modal('hide');
                    $('#skill-list').html(response.view);
                }
            }
        });
    });

    init('#save-skill-form');
</script>
