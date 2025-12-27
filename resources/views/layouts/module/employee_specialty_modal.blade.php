<div class="modal fade" id="specialtyModal" tabindex="-1" aria-labelledby="specialtyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Uzmanlık Alanları Yönetimi</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Kapat" id="specialtyModalClose"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Yeni Uzmanlık Alanları Ekle</h6>
                    </div>
                    <div class="card-body">
                        <form id="addSpecialtyForm" action="{{ route('employees.specialties.add') }}" method="post">
                            @csrf
                            <div id="specialty-fields">
                                <div class="specialty-field row mb-3">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="specialty_name_1">Uzmanlık Alanı Adı <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="specialty_name_1" name="specialties[0][name]" required>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="specialty_description_1">Açıklama</label>
                                            <input type="text" class="form-control" id="specialty_description_1" name="specialties[0][description]">
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-success btn-sm" id="add-specialty-field" title="Yeni Alan Ekle">
                                            <i class="fal fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fal fa-plus mr-1"></i>
                                    Uzmanlık Alanları Ekle
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Mevcut Uzmanlık Alanları</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="specialtyTable">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="30%">Uzmanlık Alanı</th>
                                        <th width="45%">Açıklama</th>
                                        <th width="20%">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody id="specialtyTableBody">
                                    <!-- Dinamik içerik buraya yüklenecek -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div> 