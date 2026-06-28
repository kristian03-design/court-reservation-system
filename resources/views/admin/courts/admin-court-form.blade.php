@extends('admin.layouts.shell', ['pageTitle' => $court->exists ? 'Edit Court' : 'Add Court', 'active' => 'courts'])

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">Facility Management</p>
        <h1>{{ $court->exists ? 'Edit Court' : 'Add Court' }}</h1>
    </div>
    <a href="{{ route('admin.courts.index') }}" class="btn btn-outline">
        <i class="ti ti-arrow-left" style="font-size:14px;"></i>
        Back to Courts
    </a>
</div>

<form action="{{ $court->exists ? route('admin.courts.update', $court) : route('admin.courts.store') }}"
      method="POST"
      enctype="multipart/form-data"
      class="court-form-grid"
      id="court-form">
    @csrf
    @if($court->exists)
        @method('PUT')
    @endif

    {{-- Left Column: Main Info --}}
    <div class="court-form-main">

        {{-- Court Name & Type --}}
        <div class="cf-card">
            <div class="cf-card-head">
                <span class="cf-card-icon" style="background:rgba(29,92,255,.1);color:var(--admin-blue-2);">
                    <i class="ti ti-building-stadium"></i>
                </span>
                <div>
                    <h3>Court Details</h3>
                    <p>Basic information about this court</p>
                </div>
            </div>

            <div class="cf-grid-2">
                <div class="cf-field">
                    <label class="cf-label" for="court_name">Court Name <span class="cf-required">*</span></label>
                    <input id="court_name" type="text" name="court_name"
                           value="{{ old('court_name', $court->court_name) }}"
                           placeholder="e.g. Peak Basketball Court"
                           class="cf-input @error('court_name') is-error @enderror"
                           required>
                    @error('court_name')<span class="cf-error">{{ $message }}</span>@enderror
                </div>

                <div class="cf-field">
                    <label class="cf-label" for="court_type">Court Type <span class="cf-required">*</span></label>
                    <select id="court_type" name="court_type"
                            class="cf-input @error('court_type') is-error @enderror"
                            required>
                        <option value="" disabled @selected(empty(old('court_type', $court->court_type)))>Select type</option>
                        @foreach(['Basketball', 'Volleyball', 'Badminton', 'Tennis', 'Futsal'] as $type)
                            <option value="{{ $type }}" @selected(old('court_type', $court->court_type) == $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('court_type')<span class="cf-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="cf-grid-2">
                <div class="cf-field">
                    <label class="cf-label" for="hourly_rate">
                        <i class="ti ti-currency-peso" style="font-size:13px;"></i>
                        Hourly Rate (PHP) <span class="cf-required">*</span>
                    </label>
                    <div class="cf-input-prefix">
                        <span>₱</span>
                        <input id="hourly_rate" type="number" step="0.01" min="0" name="hourly_rate"
                               value="{{ old('hourly_rate', $court->hourly_rate) }}"
                               placeholder="0.00"
                               class="cf-input @error('hourly_rate') is-error @enderror"
                               required>
                    </div>
                    @error('hourly_rate')<span class="cf-error">{{ $message }}</span>@enderror
                </div>

                <div class="cf-field">
                    <label class="cf-label" for="capacity">
                        <i class="ti ti-users" style="font-size:13px;"></i>
                        Capacity (Persons) <span class="cf-required">*</span>
                    </label>
                    <input id="capacity" type="number" min="1" name="capacity"
                           value="{{ old('capacity', $court->capacity) }}"
                           placeholder="e.g. 10"
                           class="cf-input @error('capacity') is-error @enderror"
                           required>
                    @error('capacity')<span class="cf-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="cf-field">
                <label class="cf-label" for="description">Description</label>
                <textarea id="description" name="description" rows="4"
                          class="cf-input @error('description') is-error @enderror"
                          placeholder="Describe the court's features, facilities, flooring, lighting…">{{ old('description', $court->description) }}</textarea>
                @error('description')<span class="cf-error">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    {{-- Right Column: Image & Status --}}
    <div class="court-form-side">

        {{-- Image Upload --}}
        <div class="cf-card">
            <div class="cf-card-head">
                <span class="cf-card-icon" style="background:rgba(139,92,246,.1);color:#7c3aed;">
                    <i class="ti ti-photo"></i>
                </span>
                <div>
                    <h3>Court Image</h3>
                    <p>Upload or provide a URL</p>
                </div>
            </div>

            {{-- Drag & Drop Zone --}}
            <div class="cf-dropzone" id="cf-dropzone">

                {{-- Preview --}}
                <div class="cf-dropzone-preview" id="cf-dropzone-preview" style="display:none;">
                    <img id="cf-preview-img" src="" alt="Preview">
                    <button type="button" class="cf-dropzone-remove" id="cf-remove-btn" title="Remove image">
                        <i class="ti ti-x"></i>
                    </button>
                </div>

                {{-- Placeholder --}}
                <div class="cf-dropzone-placeholder" id="cf-dropzone-placeholder">
                    <span class="cf-dropzone-icon">
                        <i class="ti ti-cloud-upload"></i>
                    </span>
                    <strong>Drag &amp; drop your image here</strong>
                    <span>or <u>click to browse</u></span>
                    <small>PNG, JPG, WEBP &middot; max 4 MB</small>
                </div>

                <input type="file"
                       name="image_file"
                       id="cf-file-input"
                       accept="image/png,image/jpeg,image/webp"
                       style="display:none;"></div>

            {{-- Manual URL fallback --}}
            <div class="cf-field cf-url-field" id="cf-url-field">
                <label class="cf-label" for="image_path">
                    <i class="ti ti-link" style="font-size:12px;"></i>
                    Or paste image path / URL
                </label>
                <input id="image_path" type="text" name="image"
                       value="{{ old('image', $court->image) }}"
                       placeholder="/images/court-name.png"
                       class="cf-input">
                @error('image')<span class="cf-error">{{ $message }}</span>@enderror
            </div>

            <p id="cf-file-chosen" class="cf-file-chosen" style="display:none;">
                <i class="ti ti-check"></i>
                <span id="cf-file-name"></span>
            </p>
        </div>

        {{-- Status Card --}}
        <div class="cf-card">
            <div class="cf-card-head">
                <span class="cf-card-icon" style="background:var(--admin-green-soft);color:#15803d;">
                    <i class="ti ti-toggle-right"></i>
                </span>
                <div>
                    <h3>Availability</h3>
                    <p>Set current court status</p>
                </div>
            </div>

            <div class="cf-status-opts">
                @foreach([
                    ['value' => 'available',   'label' => 'Available',    'desc' => 'Open for bookings', 'color' => '#15803d', 'bg' => '#f0fdf4', 'border' => '#bbf7d0', 'icon' => 'ti-circle-check'],
                    ['value' => 'maintenance', 'label' => 'Maintenance',  'desc' => 'Under repair/maintenance', 'color' => '#b45309', 'bg' => '#fffbeb', 'border' => '#fde68a', 'icon' => 'ti-tool'],
                    ['value' => 'closed',      'label' => 'Closed',       'desc' => 'Not accepting bookings', 'color' => '#dc2626', 'bg' => '#fff1f2', 'border' => '#fecaca', 'icon' => 'ti-ban'],
                ] as $opt)
                    <label class="cf-status-opt" style="--opt-color:{{ $opt['color'] }};--opt-bg:{{ $opt['bg'] }};--opt-border:{{ $opt['border'] }};">
                        <input type="radio" name="status" value="{{ $opt['value'] }}"
                               @checked(old('status', $court->status ?? 'available') == $opt['value'])>
                        <span class="cf-status-opt-body">
                            <i class="ti {{ $opt['icon'] }}"></i>
                            <span>
                                <strong>{{ $opt['label'] }}</strong>
                                <small>{{ $opt['desc'] }}</small>
                            </span>
                        </span>
                    </label>
                @endforeach
            </div>
            @error('status')<span class="cf-error">{{ $message }}</span>@enderror
        </div>

        {{-- Actions --}}
        <div class="cf-actions">
            <a href="{{ route('admin.courts.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-secondary">
                <i class="ti {{ $court->exists ? 'ti-device-floppy' : 'ti-plus' }}"></i>
                {{ $court->exists ? 'Update Court' : 'Save Court' }}
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
(function () {
    var dropzone      = document.getElementById('cf-dropzone');
    var preview       = document.getElementById('cf-dropzone-preview');
    var previewImg    = document.getElementById('cf-preview-img');
    var placeholder   = document.getElementById('cf-dropzone-placeholder');
    var removeBtn     = document.getElementById('cf-remove-btn');
    var fileInput     = document.getElementById('cf-file-input');
    var urlField      = document.getElementById('cf-url-field');
    var urlInput      = document.getElementById('image_path');
    var fileChosen    = document.getElementById('cf-file-chosen');
    var fileName      = document.getElementById('cf-file-name');

    var initialPreview = `{{ $court->image ? asset($court->image) : '' }}`;

    // Set initial state
    if (initialPreview) {
        showPreview(initialPreview);
    }

    function showPreview(src) {
        previewImg.src = src;
        preview.style.display    = '';
        placeholder.style.display = 'none';
        dropzone.classList.add('has-preview');
    }

    function clearImage() {
        previewImg.src = '';
        preview.style.display    = 'none';
        placeholder.style.display = '';
        dropzone.classList.remove('has-preview');
        fileInput.value  = '';
        if (urlInput) urlInput.value = '';
        fileChosen.style.display = 'none';
        urlField.style.display   = '';
    }

    function loadFile(file) {
        fileName.textContent     = file.name;
        fileChosen.style.display = '';
        urlField.style.display   = 'none';
        var reader = new FileReader();
        reader.onload = function (e) { showPreview(e.target.result); };
        reader.readAsDataURL(file);
    }

    // Click to browse
    dropzone.addEventListener('click', function (e) {
        if (e.target !== removeBtn && !removeBtn.contains(e.target)) {
            fileInput.click();
        }
    });

    // Remove button
    if (removeBtn) {
        removeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            clearImage();
        });
    }

    // File input change
    fileInput.addEventListener('change', function (e) {
        var file = e.target.files[0];
        if (file) loadFile(file);
    });

    // Drag & drop
    dropzone.addEventListener('dragenter', function (e) {
        e.preventDefault();
        dropzone.classList.add('dragging');
    });
    dropzone.addEventListener('dragleave', function (e) {
        e.preventDefault();
        dropzone.classList.remove('dragging');
    });
    dropzone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropzone.classList.add('dragging');
    });
    dropzone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropzone.classList.remove('dragging');
        var file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            loadFile(file);
            var dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
        }
    });

    // URL input live preview
    if (urlInput) {
        urlInput.addEventListener('input', function () {
            if (fileInput.files.length === 0) {
                var val = urlInput.value;
                if (val) showPreview(val);
                else if (initialPreview) showPreview(initialPreview);
                else clearImage();
            }
        });
    }
})();
</script>
@endpush
@endsection
