<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Kullanıcı Ekle</h5>
                <button type="button" class="btn btn-sm text-white" data-bs-dismiss="modal" aria-label="Kapat" style="border: none; background: none;">
            <i class="fal fa-times"></i>
        </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form action="{{ route('users.add') }}" method="POST" enctype="multipart/form-data" class="user-add-form">
                        @csrf
                        <br>


                        <!---username, name, email, phone, password, remember_token--->
                        <div class="mb-3">
                            <label for="name" class="form-label">Ad Soyad</label>
                            <input type="text" class="form-control" id="name" name="name" required maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefon</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required pattern="[0-9]+" maxlength="15" minlength="10" title="Lütfen geçerli bir telefon numarası girin.">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta (opsiyonel)</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Şifre</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        </div>
                         
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
            </form>
        </div>
    </div>
</div>