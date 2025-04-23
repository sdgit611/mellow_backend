<!-- TAB CONTENT START -->
<div class="col-xl-12 col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4">

    <div class="row mb-3">
        <div class="col-md-12">
            <a class="f-15 f-w-500" href="javascript:;" id="add-skill-btn">
                <i class="icons icon-plus font-weight-bold mr-1"></i>
                @lang('app.add') @lang('modules.skill.skill')
            </a>
        </div>
    </div>

    <div class="d-flex flex-wrap" id="skill-list">
        @include('profile-settings.ajax.skill.skill-list')

    </div>

</div>
<!-- TAB CONTENT END -->

@push('scripts')
<script>
    $('#add-skill-btn').click(function () {
    let url = "{{ route('skill.create') }}";
    $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
    $.ajaxModal(MODAL_DEFAULT, url);
});

$('body').on('click', '.edit-skill', function () {
    let id = $(this).data('id');
    let url = "{{ route('skill.edit', ':id') }}".replace(':id', id);
    $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
    $.ajaxModal(MODAL_DEFAULT, url);
});

$('body').on('click', '.delete-skill', function () {
    let id = $(this).data('id');
    let url = "{{ route('skill.destroy', ':id') }}".replace(':id', id);
    let token = "{{ csrf_token() }}";

    Swal.fire({
        title: "@lang('messages.sweetAlertTitle')",
        text: "@lang('messages.recoverRecord')",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "@lang('messages.confirmDelete')",
        cancelButtonText: "@lang('app.cancel')",
        customClass: {
            confirmButton: 'btn btn-primary mr-3',
            cancelButton: 'btn btn-secondary'
        },
    }).then((result) => {
        if (result.isConfirmed) {
            $.easyAjax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'DELETE'
                },
                success: function (response) {
                    if (response.status === 'success') {
                        $('#skill-list').html(response.view); // 🔄 Dynamically update the skill list
                    }
                }
            });
        }
    });
});

</script>
@endpush
