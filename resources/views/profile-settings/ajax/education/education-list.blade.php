<x-table class="table-bordered">
    <x-slot name="thead">
        <th>@lang('modules.education.institution')</th>
        <th>@lang('modules.education.degree')</th>
        <th>@lang('modules.education.year')</th>
        <th>@lang('modules.education.percentage')</th>
        <th class="text-right">@lang('app.action')</th>
    </x-slot>

    @forelse ($educations as $education)
        <tr class="tableRow{{ $education->id }}">
            <td>{{ $education->collage_name }}</td>
            <td>{{ $education->degree }}</td>
            <td>{{ $education->passing_year }}</td>
            <td>{{ $education->percentage }}</td>
            <td class="text-right">
                <div class="task_view">
                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle"
                            type="link"
                            id="dropdownMenuLink-{{ $education->id }}"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                            data-boundary="viewport">
                            <i class="icon-options-vertical icons"></i>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right"
                            aria-labelledby="dropdownMenuLink-{{ $education->id }}" tabindex="0">

                            <a href="javascript:;"
                                class="dropdown-item edit-education"
                                data-id="{{ $education->id }}">
                                <i class="fa fa-edit mr-2"></i>@lang('app.edit')
                            </a>

                            <a href="javascript:;"
                                class="dropdown-item delete-education"
                                data-id="{{ $education->id }}">
                                <i class="fa fa-trash mr-2"></i>@lang('app.delete')
                            </a>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    @empty
        <x-cards.no-record-found-list colspan="5" />
    @endforelse
</x-table>