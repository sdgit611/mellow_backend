<x-table class="table-bordered">
    <x-slot name="thead">
        <th>@lang('modules.skill.name')</th>
        <th>@lang('modules.skill.total')</th>
        <th class="text-right">@lang('app.action')</th>
    </x-slot>

    @forelse ($skill as $val)
        <tr class="tableRow{{ $val->id }}">
            <td>{{ $val->name }}</td>
            <td>{{ $val->total }}</td>
            <td class="text-right">
                <div class="task_view">
                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle"
                           type="link"
                           id="dropdownMenuLink-{{ $val->id }}"
                           data-toggle="dropdown"
                           aria-haspopup="true"
                           aria-expanded="false"
                           data-boundary="viewport">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right"
                             aria-labelledby="dropdownMenuLink-{{ $val->id }}" tabindex="0">
                            <a href="javascript:;" class="dropdown-item edit-skill" data-id="{{ $val->id }}">
                                <i class="fa fa-edit mr-2"></i>@lang('app.edit')
                            </a>
                            <a href="javascript:;" class="dropdown-item delete-skill" data-id="{{ $val->id }}">
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
