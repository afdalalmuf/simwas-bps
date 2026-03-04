<div class="btn-group dropdown">
    <button type="button" class="btn btn-primary btn-sm dropdown-toggle no-arrow"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            data-id="{{ $k->id }}">
        ...
    </button>
    <div class="dropdown-menu dropdown-menu-right shadow-lg">
        <a href="/analis-sdm/kelola-kompetensi/{{ $k->id }}" class="dropdown-item">
            <i class="fas fa-circle-info text-primary mr-2"></i> Detail
        </a>
        <a href="javascript:void(0)" class="dropdown-item edit-btn" data-id="{{ $k->id }}"
           data-toggle="modal" data-target="#modal-edit-kompetensi">
            <i class="fas fa-edit text-warning mr-2"></i> Edit
        </a>
        @if ($k->status == 3)
            <a href="javascript:void(0)" class="dropdown-item setuju-btn" data-id="{{ $k->id }}">
                <i class="far fa-check-circle text-success mr-2"></i> Setujui
            </a>
            <a href="javascript:void(0)" class="dropdown-item tolak-btn" data-id="{{ $k->id }}" data-toggle="modal"
               data-target="#staticBackdrop">
                <i class="far fa-circle-xmark text-danger mr-2"></i> Tolak
            </a>
        @elseif ($k->status == 1)
            <a href="javascript:void(0)" class="dropdown-item tolak-btn" data-id="{{ $k->id }}" data-toggle="modal"
               data-target="#staticBackdrop">
                <i class="fas fa-ban text-danger mr-2"></i> Batal Setuju
            </a>
        @endif
        <a href="javascript:void(0)" class="dropdown-item delete-btn" data-id="{{ $k->id }}">
            <i class="fas fa-trash text-danger mr-2"></i> Hapus
        </a>
    </div>
</div>