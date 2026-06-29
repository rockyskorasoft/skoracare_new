<ul class="justify-start gap-3 data-table-list list-group list-group-horizontal align-items-center">

    @if (!empty($viewRoute))
        <li class="p-0 bg-transparent border-0 data-table-list-item list-group-item">
            <a class="gap-2 view d-flex align-items-center" href="{{ $viewRoute }}" target="{{ $target ?? '_self' }}">
                <i class="fa-solid fa-eye" title="view page"></i>
            </a>
        </li>
    @endif

    @if (!empty($downloadRoute))
        <li class="p-0 bg-transparent border-0 data-table-list-item list-group-item">
            <a class="gap-2 download d-flex align-items-center" href="{{ $downloadRoute }}" target="{{ $target ?? '_self' }}">
                <i class="fa-solid fa-download"></i>
            </a>
        </li>
    @endif

    @if (!empty($invoiceRoute))
        <li class="p-0 bg-transparent border-0 data-table-list-item list-group-item">
            <a class="gap-2 invoice d-flex align-items-center" href="{{ $invoiceRoute }}"
                title="{{ __('labels.invoice') }}" target="_blank">
                <i class="fa-solid fa-file-pdf"></i>
            </a>
        </li>
    @endif

    @if (!empty($editRoute))
        <li class="p-0 bg-transparent border-0 data-table-list-item list-group-item">
            <a class="gap-2 edit d-flex align-items-center" href="{{ $editRoute }}">
                <i class="fa-solid fa-pen-to-square"></i>
            </a>
        </li>
    @elseif (!empty($editDisabled))
        <li class="p-0 bg-transparent border-0 data-table-list-item list-group-item">
            <span class="gap-2 edit d-flex align-items-center text-muted opacity-50 pe-none" title="{{ __('labels.order') }} locked">
                <i class="fa-solid fa-pen-to-square"></i>
            </span>
        </li>
    @endif


    @if (!empty($restoreRoute))
        <li class="data-table-list-item list-group-item border-0 p-0 bg-transparent">
            <form action="{{ $restoreRoute }}" method="PUT" onsubmit="return confirm('Are you want to restore ?');"
                style="display: inline-block;">
                <input type="hidden" name="_method" value="PUT">
                <button type="submit" class="p-0 border-0 bg-transparent">
                    <i class="fa-solid fa-rotate-left" title="Restore"></i>
                </button>
            </form>
        </li>
    @endif

    @if (!empty($deleteRoute))
        <li class="p-0 bg-transparent border-0 data-table-list-item list-group-item">
            <form action="{{ $deleteRoute }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display: inline-block;" class="delete-form m-0">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" class="gap-2 p-0 bg-transparent border-0 d-flex align-items-center delete-btn">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </li>
    @endif

    @if (!empty($viewModalRoute))
        <li class="p-0 bg-transparent border-0 data-table-list-item list-group-item show-btn" data-bs-toggle="modal"
            data-bs-target="#showBaseModal" modal-title="{{ $modalData['showModalTitle'] }}"
            show-url="{{ $viewModalRoute }}">
            <a class="gap-2 view d-flex align-items-center" href="javascript:void(0)">
                <i class="fa-solid fa-eye"></i>
            </a>
        </li>
    @endif

    @if (!empty($editModalRoute))
        <li class="p-0 bg-transparent border-0 data-table-list-item list-group-item baseModal edit-btn"
            id="{{ $modalData['rowId'] }}" data-bs-toggle="modal" data-bs-target=".baseModal"
            modal-title="{{ $modalData['modalTitle'] }}" edit-url="{{ $editModalRoute }}"
            table-id="{{ $modalData['tableId'] }}" form-url="{{ !empty($modalData['updateRoute']) ? route($modalData['updateRoute'], $modalData['rowId']) : '' }}">
            <a class="gap-2 edit d-flex align-items-center" href="javascript:void(0)">
                <i class="fa-solid fa-pen-to-square"></i>
            </a>
        </li>
    @endif
</ul>
