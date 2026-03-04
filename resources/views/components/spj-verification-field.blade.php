@php
    $statusId = "verifikasi_{$documentType}";
    $commentId = "comments_{$documentType}";
    $wrapperId = "wrapper_comments_{$documentType}";
@endphp

<h5>Verifikasi</h5>
<div class="line mb-3"></div>
<div class="form-group">
    <label for="{{ $statusId }}">Status Dokumen {{ ucwords(str_replace('-', ' ', $documentType)) }}</label>
    <select name="{{ $statusId }}" id="{{ $statusId }}" class="form-control" onchange="toggleComment('{{ $documentType }}')">
        <option value="" disabled selected>-- Pilih Status --</option>
        <option value="valid" {{ $verification && $verification->status === 'valid' ? 'selected' : '' }}>✅ Sesuai</option>
        <option value="invalid" {{ $verification && $verification->status === 'invalid' ? 'selected' : '' }}>❌ Tidak Sesuai</option>
    </select>
</div>

<div class="form-group" id="{{ $wrapperId }}" style="{{ $verification && $verification->status === 'invalid' ? '' : 'display: none;' }}">
    <label for="{{ $commentId }}">Catatan</label>
    <textarea name="{{ $commentId }}" id="{{ $commentId }}" class="form-control" rows="5">{{ old($commentId, $verification->comments ?? '') }}</textarea>
</div>