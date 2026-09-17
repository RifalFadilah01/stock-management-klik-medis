@extends('layouts.app')

@section('content')
<style>
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] {
        -moz-appearance: textfield;
    }

    .auto-save-indicator {
        font-size: 0.75rem;
        margin-left: 5px;
    }

    .modal-header.draggable-handle {
        cursor: move;
    }

    .page-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.9rem;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .filter-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #6b7280;
        margin-bottom: 0.35rem;
    }

    #medicinesTable thead th {
        background: #1f2937;
        color: #fff;
        border-bottom: none;
        white-space: nowrap;
    }

    #medicinesTable tbody tr:hover {
        background-color: #f8f9fe;
    }

    .table-card-wrapper {
        border-radius: 0.9rem;
        overflow: hidden;
        border: 1px solid #eef0f4;
    }

    .btn-icon-sm {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
    }

    .modal-section-title {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #4f46e5;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 0.9rem;
    }

    .modal-section-title:not(:first-child) {
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px dashed #e5e7eb;
    }
</style>

<div class="d-flex align-items-center gap-3 mb-4">
    <div class="page-header-icon">
        <i class="bi bi-capsule"></i>
    </div>
    <div>
        <h4 class="fw-bold mb-0">Manajemen Stok Obat</h4>
        <p class="text-muted mb-0 small">Kelola data, stok, dan status kedaluwarsa obat secara real-time.</p>
    </div>
</div>

<div class="card shadow-soft border-0 mb-4">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <div class="filter-label"><i class="bi bi-search me-1"></i>Pencarian</div>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0 bg-light" placeholder="Cari kode, nama, atau kategori...">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="filter-label"><i class="bi bi-tags me-1"></i>Kategori</div>
                <select id="categoryFilter" class="form-select bg-light">
                    <option value="">Semua Kategori</option>
                    <option value="Tablet">Tablet</option>
                    <option value="Sirup">Sirup</option>
                    <option value="Kapsul">Kapsul</option>
                    <option value="Injeksi">Injeksi</option>
                    <option value="Salep">Salep</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <div class="filter-label"><i class="bi bi-funnel me-1"></i>Status</div>
                <select id="statusFilter" class="form-select bg-light">
                    <option value="">Semua Status</option>
                    <option value="Tersedia">Tersedia</option>
                    <option value="Stok Menipis">Stok Menipis</option>
                    <option value="Stok Habis">Stok Habis</option>
                    <option value="Kedaluwarsa">Kedaluwarsa</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button class="btn btn-primary w-100" id="btnAdd">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Data
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 mb-3">
            <button class="btn btn-success" id="btnExportExcel">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </button>
            <button type="button" class="btn btn-danger" id="btnExportPdf">
                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
            </button>
        </div>

        <div id="exportProgressContainer" class="mb-4 p-3 border rounded-3 bg-light d-none position-relative shadow-sm">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" id="btnCloseProgress" aria-label="Close"></button>

            <div class="d-flex justify-content-between mb-1 pe-4">
                <label class="form-label fw-bold mb-0 text-success" id="exportStatusLabel">Mempersiapkan Export Data...</label>
                <span id="exportProgressText" class="fw-bold text-success">0%</span>
            </div>
            <div class="progress" style="height: 10px;">
                <div id="exportProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;"></div>
            </div>
            <div class="mt-2" id="exportDownloadContainer"></div>
        </div>

        <div class="table-card-wrapper">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="medicinesTable">
                    <thead>
                        <tr>
                            <th class="py-3 ps-3">Kode</th>
                            <th class="py-3">Nama</th>
                            <th class="py-3">Kategori</th>
                            <th class="py-3">Harga Jual</th>
                            <th class="py-3">Stok</th>
                            <th class="py-3">Min Stok</th>
                            <th class="py-3">Expired</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-center pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div> Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2" id="paginationContainer">
        </div>
    </div>
</div>

<div class="modal fade" id="medicineModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="medicineForm">
                <div class="modal-header bg-light border-bottom-0 draggable-handle">
                    <h5 class="modal-title fw-bold" id="modalTitle">
                        <i class="bi bi-capsule me-2 text-primary"></i><span>Tambah Obat</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="medicineId" name="id">

                    <div class="modal-section-title"><i class="bi bi-info-circle"></i> Informasi Umum</div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Kode Obat <span class="auto-save-indicator" id="status-code"></span></label>
                            <input type="text" class="form-control auto-save-field" id="code" name="code" placeholder="MED-XXXXX">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nama Obat <span class="auto-save-indicator" id="status-name"></span></label>
                            <input type="text" class="form-control auto-save-field" id="name" name="name" placeholder="Masukkan nama obat">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Kategori <span class="auto-save-indicator" id="status-category"></span></label>
                            <select class="form-select auto-save-field" id="category" name="category">
                                <option value="">Pilih Kategori...</option>
                                <option value="Tablet">Tablet</option>
                                <option value="Sirup">Sirup</option>
                                <option value="Kapsul">Kapsul</option>
                                <option value="Injeksi">Injeksi</option>
                                <option value="Salep">Salep</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Satuan <span class="auto-save-indicator" id="status-unit"></span></label>
                            <select class="form-select auto-save-field" id="unit" name="unit">
                                <option value="">Pilih Satuan...</option>
                                <option value="Botol">Botol</option>
                                <option value="Strip">Strip</option>
                                <option value="Box">Box</option>
                                <option value="Tube">Tube</option>
                                <option value="Vial">Vial</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="modal-section-title"><i class="bi bi-cash-coin"></i> Harga</div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Harga Beli <span class="auto-save-indicator" id="status-purchase_price"></span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control auto-save-field" id="purchase_price" name="purchase_price" min="0">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Harga Jual <span class="auto-save-indicator" id="status-selling_price"></span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control auto-save-field" id="selling_price" name="selling_price" min="0">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-section-title"><i class="bi bi-box-seam"></i> Stok &amp; Kedaluwarsa</div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Stok Saat Ini <span class="auto-save-indicator" id="status-stock"></span></label>
                            <input type="number" class="form-control auto-save-field" id="stock" name="stock" min="0">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Batas Minimum <span class="auto-save-indicator" id="status-minimum_stock"></span></label>
                            <input type="number" class="form-control auto-save-field" id="minimum_stock" name="minimum_stock" min="0">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Expired Date <span class="auto-save-indicator" id="status-expired_date"></span></label>
                            <input type="date" class="form-control auto-save-field" id="expired_date" name="expired_date">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="modal-section-title"><i class="bi bi-journal-text"></i> Catatan</div>
                    <div class="mb-2">
                        <label class="form-label fw-medium">Catatan Internal <span class="auto-save-indicator" id="status-notes"></span></label>
                        <textarea class="form-control auto-save-field" id="notes" name="notes" rows="2" placeholder="Catatan opsional..."></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4" id="btnSave">Simpan Obat</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let currentPage = 1;
    let ajaxRequest = null;

    $('#medicineModal').on('shown.bs.modal', function () {
        $('.modal-content').draggable({
            handle: ".modal-header",
            cursor: "move"
        });
    });

    $('#medicineModal').on('hidden.bs.modal', function () {
        $('.modal-content').removeAttr('style');
    });

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Keeps a separate timer per key, so debouncing one field (e.g. "name")
    // doesn't cancel a pending save for a different field (e.g. "category").
    function debounceByKey(func, wait) {
        const timeouts = {};
        return function(key, ...args) {
            clearTimeout(timeouts[key]);
            timeouts[key] = setTimeout(() => func(...args), wait);
        };
    }

    function loadMedicines(page = 1) {
        currentPage = page;

        const search = $('#searchInput').val();
        const category = $('#categoryFilter').val();
        const status = $('#statusFilter').val();

        if (ajaxRequest !== null) {
            ajaxRequest.abort();
        }

        $('#medicinesTable tbody').html('<tr><td colspan="9" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Memuat data...</td></tr>');

        ajaxRequest = $.ajax({
            url: `/medicines?page=${page}`,
            type: 'GET',
            data: { search, category, status },
            success: function(response) {
                let rows = '';

                if (response.data.length === 0) {
                    rows = `<tr><td colspan="9" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Data tidak ditemukan</td></tr>`;
                } else {
                    response.data.forEach(item => {
                        let statusBadge = '';
                        if (item.status_label === 'Tersedia') statusBadge = 'bg-success';
                        else if (item.status_label === 'Stok Menipis') statusBadge = 'bg-warning text-dark';
                        else if (item.status_label === 'Stok Habis') statusBadge = 'bg-secondary';
                        else statusBadge = 'bg-danger';

                        rows += `
                            <tr>
                                <td class="fw-medium ps-3">${item.code}</td>
                                <td>${item.name}</td>
                                <td>${item.category}</td>
                                <td>Rp ${parseInt(item.selling_price).toLocaleString('id-ID')}</td>
                                <td><span class="fw-bold">${item.stock}</span> <small class="text-muted">${item.unit}</small></td>
                                <td>${item.minimum_stock}</td>
                                <td>${item.expired_date}</td>
                                <td><span class="badge rounded-pill ${statusBadge}">${item.status_label}</span></td>
                                <td class="text-center pe-3">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-sm btn-outline-primary btn-icon-sm btn-edit" data-id="${item.id}" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon-sm btn-delete" data-id="${item.id}" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }

                $('#medicinesTable tbody').html(rows);
                renderPagination(response.meta);
            }
        });
    }

    function renderPagination(meta) {
        let paginationHtml = '';

        if (meta.last_page > 1) {
            paginationHtml += `<nav><ul class="pagination pagination-sm mb-0">`;

            meta.links.forEach(link => {
                let activeClass = link.active ? 'active' : '';
                let disabledClass = link.url === null ? 'disabled' : '';
                let urlArray = link.url ? link.url.split('page=') : [];
                let pageNumber = urlArray.length > 1 ? urlArray[1] : 1;

                let label = link.label;
                if (label.includes('&laquo;')) label = '&laquo;';
                if (label.includes('&raquo;')) label = '&raquo;';

                paginationHtml += `
                    <li class="page-item ${activeClass} ${disabledClass}">
                        <a class="page-link" href="#" data-page="${pageNumber}">${label}</a>
                    </li>
                `;
            });

            paginationHtml += `</ul></nav>`;
        }

        $('#paginationContainer').html(paginationHtml);
    }

    const triggerSearch = debounce(function() {
        loadMedicines(1);
    }, 300);

    $('#searchInput').on('keyup', triggerSearch);
    $('#categoryFilter, #statusFilter').on('change', function() {
        loadMedicines(1);
    });

    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) loadMedicines(page);
    });

    function resetForm() {
        $('#medicineForm')[0].reset();
        $('#medicineId').val('');
        $('#modalTitle').text('Tambah Data Obat');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('.auto-save-indicator').text('').removeClass('text-success text-warning text-danger');
        $('#btnSave').show();
    }

    // $('#btnAdd').on('click', function() {
    //     resetForm();
    //     $('#medicineModal').modal('show');
    // });

    $('#btnAdd').on('click', function() {
        resetForm();
        
        // --- FITUR BARU: Auto-generate kode saat modal Tambah dibuka ---
        let randomNum = Math.floor(1000 + Math.random() * 9000);
        let catVal = $('#category').val();
        // Jika kategori kosong, beri default 'XXX'
        let categoryCode = catVal ? catVal.substring(0, 3).toUpperCase() : 'XXX'; 
        $('#code').val(`MED-${categoryCode}-${randomNum}`);
        // ---------------------------------------------------------------

        $('#medicineModal').modal('show');
    });

    // --- FITUR BARU: Update kode otomatis jika Kategori diganti ---
    $('#category').on('change', function() {
        // HANYA jalankan jika dalam mode Tambah Data (id obat kosong)
        // Ini mencegah kode obat berubah secara tak sengaja saat mode Edit
        if (!$('#medicineId').val()) {
            let currentCode = $('#code').val();
            
            if (currentCode.startsWith('MED-')) {
                let randomNum = currentCode.split('-')[2] || Math.floor(1000 + Math.random() * 9000);
                let catVal = $(this).val();
                let categoryCode = catVal ? catVal.substring(0, 3).toUpperCase() : 'XXX';
                
                $('#code').val(`MED-${categoryCode}-${randomNum}`);
            }
        }
    });
    // --------------------------------------------------------------

    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        resetForm();

        $.ajax({
            url: `/medicines/${id}/edit`,
            type: 'GET',
            success: function(response) {
                const data = response.data;
                $('#medicineId').val(data.id);
                $('#code').val(data.code);
                $('#name').val(data.name);
                $('#category').val(data.category);
                $('#unit').val(data.unit);
                $('#purchase_price').val(data.purchase_price);
                $('#selling_price').val(data.selling_price);
                $('#stock').val(data.stock);
                $('#minimum_stock').val(data.minimum_stock);
                $('#expired_date').val(data.expired_date);
                $('#notes').val(data.notes);

                $('#modalTitle').text('Edit Data Obat');
                $('#btnSave').hide();
                $('#medicineModal').modal('show');
            }
        });
    });

    $('#medicineForm').on('submit', function(e) {
        e.preventDefault();

        const id = $('#medicineId').val();

        if (id) {
            $('#medicineModal').modal('hide');
            return;
        }

        const formData = $(this).serialize();
        const btnSave = $('#btnSave');
        const originalText = btnSave.html();
        btnSave.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        $.ajax({
            url: '/medicines',
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#medicineModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data obat baru berhasil ditambahkan.',
                    showConfirmButton: false,
                    timer: 2000
                });
                loadMedicines(1);
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (const field in errors) {
                        $(`#${field}`).addClass('is-invalid');
                        $(`#${field}`).next('.invalid-feedback').text(errors[field][0]);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan pada server!'
                    });
                }
            },
            complete: function() {
                btnSave.prop('disabled', false).html(originalText);
            }
        });
    });

    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Hapus Data?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/medicines/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: 'Data obat berhasil dihapus.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        loadMedicines(currentPage);
                    },
                    error: function() {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data.', 'error');
                    }
                });
            }
        });
    });

    const triggerAutoSave = debounceByKey(function(element) {
        const id = $('#medicineId').val();
        if (!id) return;

        const fieldName = element.attr('name');
        const fieldValue = element.val();
        const indicator = $(`#status-${fieldName}`);

        indicator.removeClass('text-success text-danger').addClass('text-warning').text('Saving...');

        let payload = {
            _method: 'PUT'
        };
        payload[fieldName] = fieldValue;

        $.ajax({
            url: `/medicines/${id}`,
            type: 'POST',
            data: payload,
            success: function() {
                indicator.removeClass('text-warning text-danger').addClass('text-success').html('<i class="bi bi-check-circle-fill"></i> Saved');
                loadMedicines(currentPage);
                setTimeout(() => indicator.text(''), 3000);
            },
            error: function(xhr) {
                indicator.removeClass('text-warning text-success').addClass('text-danger').html('<i class="bi bi-x-circle-fill"></i> Failed');
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors[fieldName]) {
                        element.addClass('is-invalid');
                        element.next('.invalid-feedback').text(errors[fieldName][0]);
                    }
                }
            }
        });
    }, 800);

    $('.auto-save-field').on('input change', function() {
        const id = $('#medicineId').val();
        if (id) {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').text('');
            triggerAutoSave($(this).attr('name'), $(this));
        }
    });

    let progressInterval = null;
    let activeExportBtn = null;
    let activeExportOriginalContent = '';

    $('#btnCloseProgress').on('click', function() {
        $('#exportProgressContainer').addClass('d-none');
        if (progressInterval) {
            clearInterval(progressInterval);
        }
        if (activeExportBtn) {
            activeExportBtn.prop('disabled', false).html(activeExportOriginalContent);
        }
    });

    $('#btnExportExcel').on('click', function() {
        const btn = $(this);
        const originalContent = btn.html();

        const search = $('#searchInput').val();
        const category = $('#categoryFilter').val();
        const status = $('#statusFilter').val();

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');
        $('#exportProgressContainer').removeClass('d-none');
        $('#exportProgressBar').css('width', '0%').attr('aria-valuenow', 0).removeClass('bg-danger').addClass('bg-success progress-bar-animated');
        $('#exportProgressText').text('0%');
        $('#exportDownloadContainer').html('');

        $.ajax({
            url: '/exports/excel',
            type: 'POST',
            data: { search, category, status },
            success: function(response) {
                if (response.export_id) {
                    checkExportProgress(response.export_id, btn, originalContent);
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal memulai proses export.', 'error');
                btn.prop('disabled', false).html(originalContent);
                $('#exportProgressContainer').addClass('d-none');
            }
        });
    });

    function checkExportProgress(exportId, btn, originalContent) {
        if (progressInterval) {
            clearInterval(progressInterval);
        }

        activeExportBtn = btn;
        activeExportOriginalContent = originalContent;

        progressInterval = setInterval(function() {
            $.ajax({
                url: `/exports/progress/${exportId}`,
                type: 'GET',
                success: function(response) {
                    if (response) {
                        const progress = response.progress || 0;
                        const processed = response.processed || 0;
                        const total = response.total || 0;

                        $('#exportProgressBar').css('width', `${progress}%`).attr('aria-valuenow', progress);
                        $('#exportProgressText').text(`${progress}% (${processed}/${total} data)`);

                        if (response.status === 'completed') {
                            clearInterval(progressInterval);
                            $('#exportProgressText').text('100% - Export Selesai!');
                            $('#exportProgressBar').removeClass('progress-bar-animated');

                            $('#exportDownloadContainer').html(`
                                <a href="${response.file_url}" target="_blank" class="btn btn-sm btn-outline-success w-100 fw-bold">
                                    <i class="bi bi-download"></i> Download File
                                </a>
                            `);

                            btn.prop('disabled', false).html(originalContent);
                        } else if (response.status === 'failed') {
                            clearInterval(progressInterval);
                            $('#exportProgressBar').removeClass('bg-success').addClass('bg-danger').removeClass('progress-bar-animated');
                            $('#exportProgressText').text('Gagal: ' + (response.error || 'Terjadi kesalahan pada server'));
                            btn.prop('disabled', false).html(originalContent);
                        }
                    }
                },
                error: function(xhr) {
                    if (xhr.status >= 500) {
                        clearInterval(progressInterval);
                        $('#exportProgressBar').removeClass('bg-success').addClass('bg-danger').removeClass('progress-bar-animated');
                        $('#exportStatusLabel').removeClass('text-success').addClass('text-danger').text('Terjadi kesalahan pada server');
                        btn.prop('disabled', false).html(originalContent);
                    }
                }
            });
        }, 1500);
    }

    $('#btnExportPdf').on('click', function(e) {
        e.preventDefault();

        const btn = $(this);
        const originalContent = btn.html();

        const search = $('#searchInput').val();
        const category = $('#categoryFilter').val();
        const status = $('#statusFilter').val();

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');
        $('#exportProgressContainer').removeClass('d-none');
        $('#exportProgressBar').css('width', '0%').attr('aria-valuenow', 0).removeClass('bg-danger').addClass('bg-success progress-bar-animated');
        $('#exportProgressText').text('0%');
        $('#exportDownloadContainer').html('');

        $.ajax({
            url: '/exports/pdf',
            type: 'POST',
            data: { search, category, status },
            success: function(response) {
                if (response.export_id) {
                    checkExportProgress(response.export_id, btn, originalContent);
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal memulai proses export.', 'error');
                btn.prop('disabled', false).html(originalContent);
                $('#exportProgressContainer').addClass('d-none');
            }
        });
    });

    $(document).ready(function() {
        loadMedicines();
    });
</script>
@endpush
