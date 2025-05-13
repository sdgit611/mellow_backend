@extends('layouts.app')

@section('content')
<div class="content-wrapper p-4">
    <h2 class="mb-4">User Reviews</h2>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Current Location</th>
                    <th>Current CTC</th>
                    <th>Skills</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $applicant)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $applicant->full_name }}</td>
                        <td>{{ $applicant->current_location }}</td>
                        <td>{{ $applicant->current_ctc }}</td>
                        <td>{{ $applicant->skill }}</td>
                        <td>{{ $applicant->email ?? 'N/A' }}</td>
                        <td>
                            <form action="{{ route('applicant.select') }}" method="POST">
                                @csrf
                                <input type="hidden" name="swap_id" value="{{$applicant->id}}" />
                                <input type="hidden" name="developer_id" value="{{$id}}" />
                                <button type="submit" class="btn btn-success btn-sm">Select</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No matching applicants found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
</div>


@endsection
