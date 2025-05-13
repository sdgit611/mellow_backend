@extends('layouts.app')

@section('content')
<div class="content-wrapper p-4">
    <h2 class="mb-4">User Reviews</h2>

    <x-form id="save-reviews-data-form" class="row">
        @csrf
        <input type="hidden" name="task_id" value="{{ $taskId }}" />
        {{-- User Selector --}}
        <div class="col-md-6">
            <x-forms.label class="my-3" fieldId="user" fieldLabel="Select User" />
            <x-forms.input-group>
                <select class="form-control p-2" name="user" id="user" required>
                    <option value="">-- Select User --</option>
                    @foreach($task->users as $val)
                        @if(!in_array($val->id, $userRivews_group_by))
                            <option value="{{ $val->id }}">
                                {{ $val->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </x-forms.input-group>
        </div>
        {{-- Dynamic Rating Questions --}}
        @foreach($question as $val)
            <div class="col-md-6">
                <x-forms.label class="my-3" fieldId="rating_{{ $val->id }}" fieldLabel="{{ $val->Question }}" />
                <x-forms.input-group>
                    <select class="form-control p-2" name="ratings[{{ $val->id }}]" id="rating_{{ $val->id }}" required>
                        <option value="">-- Select Rating --</option>
                        @for ($i = 1; $i <= $val->star; $i++)
                            <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                </x-forms.input-group>
            </div>
        @endforeach

        {{-- Comment --}}
        <div class="col-md-12">
            <x-forms.textarea 
                fieldId="comment" 
                fieldName="comment" 
                fieldLabel="Review Comment" 
                placeholder="Write your review..." 
                required
            />
        </div>

        {{-- Submit --}}
        <div class="col-md-12 mt-4">
            <!-- <x-forms.button-primary id="save-review">Submit Review</x-forms.button-primary> -->
             <button type="submit" class="btn-primary rounded f-14 p-2" id="save-review" fdprocessedid="iyuozc">
                    Submit Review
            </button>
        </div>
    </x-form>
</div>

<!-- User Reviews Table -->
@if($userRivews->count())
<div class="mt-5">
    <h4 class="mb-3">Submitted Reviews</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Question</th>
                    <th>Rating</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($userRivews as $index => $review)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $review->user->name ?? 'N/A' }}</td>
                        <td>{{ $review->question->Question ?? 'N/A' }}</td>
                        <td>{{ $review->star }} ★</td>
                        <td>{{ $review->comment }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="mt-5 alert alert-info">
    No reviews submitted yet.
</div>
@endif


@endsection


@push('scripts')
<script>
    $(document).ready(function () {
        $('#save-reviews-data-form').on('submit', function (e) {
            e.preventDefault(); // prevent default form submission
            
            let form = $(this);
            let formData = new FormData(this);
            let url = "{{ route('tasks.reviews.store') }}"
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                     console.log(response)
                    // ✅ Handle success
                    Swal.fire({
                        icon: 'success',
                        title: 'Review Submitted!',
                        text: response.message || 'Thank you for your feedback.'
                    });

                    if (typeof response.route != 'undefined' || response.route != null)
                        window.location.href = response.route; // Redirect if route exists 
                       
                    

                    // if(route)
                },
                error: function (xhr) {
                    // ❌ Handle errors
                    let errors = xhr.responseJSON?.errors;
                    if (errors) {
                        let message = '';
                        $.each(errors, function (key, value) {
                            message += `${value}<br>`;
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: message
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong',
                            text: 'Please try again later.'
                        });
                    }
                }
            });
        });
    });
</script>
@endpush