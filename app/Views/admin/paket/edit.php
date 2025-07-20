<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Paket Wisata</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="/admin"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="/admin/paket">Paket Wisata</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Paket</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="card-title d-flex align-items-center">
            <h5 class="mb-0">Edit Paket Wisata</h5>
        </div>
        <hr>
        <form id="editPaketForm" enctype="multipart/form-data">
            <input type="hidden" name="idpaket" value="<?= $paket['idpaket'] ?>">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="namapaket" class="form-label">Nama Paket <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="namapaket" name="namapaket" value="<?= $paket['namapaket'] ?>" required>
                    <div class="invalid-feedback" id="namapaket-error"></div>
                </div>
                <div class="col-md-6">
                    <label for="idkategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select class="form-select" id="idkategori" name="idkategori" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($kategori as $k) : ?>
                            <option value="<?= $k['idkategori'] ?>" <?= ($k['idkategori'] == $paket['idkategori']) ? 'selected' : '' ?>><?= $k['namakategori'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback" id="idkategori-error"></div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="harga" class="form-label">Harga <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="harga" name="harga" min="0" step="1000" value="<?= $paket['harga'] ?>" required>
                    </div>
                    <div class="invalid-feedback" id="harga-error"></div>
                </div>
                <div class="col-md-6">
                    <label for="statuspaket" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" id="statuspaket" name="statuspaket" required>
                        <option value="">Pilih Status</option>
                        <option value="active" <?= ($paket['statuspaket'] == 'active') ? 'selected' : '' ?>>Aktif</option>
                        <option value="inactive" <?= ($paket['statuspaket'] == 'inactive') ? 'selected' : '' ?>>Tidak Aktif</option>
                    </select>
                    <div class="invalid-feedback" id="statuspaket-error"></div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="minimalpeserta" class="form-label">Minimal Peserta</label>
                    <input type="number" class="form-control" id="minimalpeserta" name="minimalpeserta" min="1" value="<?= $paket['minimalpeserta'] ?? 1 ?>">
                    <div class="invalid-feedback" id="minimalpeserta-error"></div>
                </div>
                <div class="col-md-4">
                    <label for="maximalpeserta" class="form-label">Maksimal Peserta</label>
                    <input type="number" class="form-control" id="maximalpeserta" name="maximalpeserta" min="1" value="<?= $paket['maximalpeserta'] ?? 10 ?>">
                    <div class="invalid-feedback" id="maximalpeserta-error"></div>
                </div>
                <div class="col-md-4">
                    <label for="durasi" class="form-label">Durasi (Hari)</label>
                    <input type="number" class="form-control" id="durasi" name="durasi" min="1" value="<?= $paket['durasi'] ?? 1 ?>">
                    <div class="invalid-feedback" id="durasi-error"></div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5"><?= $paket['deskripsi'] ?></textarea>
                    <div class="invalid-feedback" id="deskripsi-error"></div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="fasilitas" class="form-label">Fasilitas</label>
                    <textarea class="form-control" id="fasilitas" name="fasilitas" rows="4" placeholder="Masukkan fasilitas yang tersedia dalam paket wisata ini"><?= $paket['fasilitas'] ?? '' ?></textarea>
                    <small class="text-muted">Tuliskan semua fasilitas yang tersedia dalam paket ini. Gunakan tanda koma atau baris baru untuk memisahkan setiap fasilitas.</small>
                    <div class="invalid-feedback" id="fasilitas-error"></div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="foto" class="form-label">Foto</label>
                    <input type="file" class="form-control" id="foto" name="foto" accept="image/png, image/jpeg, image/jpg" onchange="validateAndPreviewImage(this)">
                    <div class="invalid-feedback" id="foto-error"></div>
                    <small class="text-muted">Format: JPG, JPEG, PNG. Ukuran maksimal: 2MB</small>

                    <div class="mt-2">
                        <img id="imagePreview" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px; display: none;">
                    </div>

                    <?php if (!empty($paket['foto'])) : ?>
                        <div class="mt-2" id="currentImage">
                            <p>Foto saat ini:</p>
                            <img src="/uploads/paket/<?= $paket['foto'] ?>" alt="Current" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                    <a href="/admin/paket" class="btn btn-secondary px-4 ms-2">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function validateAndPreviewImage(input) {
        const file = input.files[0];
        const preview = document.getElementById('imagePreview');
        const fotoError = $('#foto-error');
        const maxSize = 2 * 1024 * 1024; // 2MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

        // Reset
        preview.src = '';
        preview.style.display = 'none';
        $(input).removeClass('is-invalid');
        fotoError.html('');

        if (file) {
            // Validasi tipe file
            if (!allowedTypes.includes(file.type)) {
                $(input).addClass('is-invalid');
                fotoError.html('Format file tidak valid. Gunakan JPG, JPEG, atau PNG.');
                return;
            }

            // Validasi ukuran file
            if (file.size > maxSize) {
                $(input).addClass('is-invalid');
                fotoError.html('Ukuran file terlalu besar. Maksimal 2MB.');
                return;
            }

            // Tampilkan preview
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }

    $(document).ready(function() {
        $('#editPaketForm').submit(function(e) {
            e.preventDefault();

            // Reset error messages
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');

            // Create form data
            const formData = new FormData(this);
            const id = $('input[name="idpaket"]').val();

            // Show loading
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
            submitBtn.prop('disabled', true);

            // Submit form
            $.ajax({
                url: `/admin/paket/update/${id}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Sukses!',
                            text: response.message,
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    if (response.errors) {
                        // Show validation errors
                        Object.keys(response.errors).forEach(function(key) {
                            $(`#${key}`).addClass('is-invalid');
                            $(`#${key}-error`).html(response.errors[key]);
                        });
                        Swal.fire('Error', 'Ada kesalahan pada form. Silakan periksa kembali.', 'error');
                    } else {
                        Swal.fire('Error', response.message || 'Terjadi kesalahan saat menyimpan data', 'error');
                    }
                },
                complete: function() {
                    // Restore button
                    submitBtn.html(originalText);
                    submitBtn.prop('disabled', false);
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>