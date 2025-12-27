@foreach($users as $user)
<div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary-700">
                <h5 class="modal-title text-white" id="editModalLabel{{ $user->id }}">
                    <i class="fal fa-user-edit width-1"></i>
                    <span>{{ $user->name }}</span>
                </h5>
                <button type="button" class="btn btn-sm text-white" data-bs-dismiss="modal" aria-label="Kapat" style="border: none; background: none;">
            <i class="fal fa-times"></i>
        </button>
            </div>
            <div class="modal-body p-0">
                <div class="row no-gutters">
                    <div class="col-12">
                        <ul class="nav nav-tabs nav-tabs-clean nav-justified" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active px-4 py-3 fs-md fw-500" id="userInfoTab{{ $user->id }}" data-bs-toggle="tab" href="#userInfo{{ $user->id }}" role="tab">
                                    <i class="fal fa-user-circle width-1"></i>
                                    <span>Kullanıcı Bilgileri</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link px-4 py-3 fs-md fw-500" id="userPermissionsTab{{ $user->id }}" data-bs-toggle="tab" href="#userPermissions{{ $user->id }}" role="tab">
                                    <i class="fal fa-shield-check width-1"></i>
                                    <span>Kullanıcı Yetkileri</span>
                                </a>
                            </li>
                        </ul>
                        
                        <div class="tab-content p-4">
                            <div class="tab-pane fade show active" id="userInfo{{ $user->id }}" role="tabpanel">
                                <form action="{{ route('users.update') }}" method="POST" enctype="multipart/form-data" class="user-edit-form">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Ad Soyad</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required maxlength="255">
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Telefon</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" value="{{ $user->phone }}" required pattern="[0-9]+" maxlength="15" minlength="10" title="Lütfen geçerli bir telefon numarası girin.">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">E-posta (opsiyonel)</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Şifre (değiştirmek için doldurun)</label>
                                        <input type="password" class="form-control" id="password" name="password" minlength="6" placeholder="Yeni şifre (boş bırakılırsa değişmez)">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Güncelle</button>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="userPermissions{{ $user->id }}" role="tabpanel">
                                <div class="panel-content">


                                 <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label fw-500">Yetki Seçiniz</label>
                                            <select class="form-control form-control-lg" name="permission" id="permission{{ $user->id }}" data-user-id="{{ $user->id }}">
                                                <option value="patron">Patron</option>
                                                <option value="estetist">Estetist</option>
                                                <option value="receptionist">Receptionist</option>
                                                <option value="admin">Admin</option>
                                                <option value="super_admin">Super Admin</option>
                                            </select>
                                        </div>
                                    </div>
                                 </div>

                                 
                                 <script>
                                    /*Hazır yetkileri buraya ekleyeceğiz*/
                                    
                                 </script>



                                    <div class="section-title fw-500 text-primary pt-2 pb-2">Rezervasyon İzinleri</div>
                                    
                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-calendar-check fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Tüm Rezervasyonları Görüntüleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="all_reservations" value="all_reservations" id="allReservations{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="allReservations{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-calendar-plus fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Rezervasyon Oluşturma</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="create_reservations" value="create_reservations" id="createReservations{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="createReservations{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-calendar-edit fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Rezervasyon Güncelleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="update_reservations" value="update_reservations" id="updateReservations{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="updateReservations{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-calendar-times fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Rezervasyon Silme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="delete_reservations" value="delete_reservations" id="deleteReservations{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="deleteReservations{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <!-- Müşteri İzinleri -->
                                    <div class="section-title fw-500 text-primary pt-4 pb-2">Müşteri İzinleri</div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-users fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Tüm Müşterileri Görüntüleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="delete_reservations" value="all_customers" id="allCustomers{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="allCustomers{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-user-plus fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Müşteri Oluşturma</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="create_customers" value="create_customers" id="createCustomers{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="createCustomers{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-user-edit fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Müşteri Güncelleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="update_customers" value="update_customers" id="updateCustomers{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="updateCustomers{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-user-times fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Müşteri Silme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="delete_customers" value="delete_customers" id="deleteCustomers{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="deleteCustomers{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <!-- Çalışan İzinleri -->
                                    <div class="section-title fw-500 text-primary pt-4 pb-2">Personel Yönetimi</div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-users-cog fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Tüm Çalışanları Görüntüleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="all_employees" value="all_employees" id="allEmployees{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="allEmployees{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-user-plus fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Çalışan Oluşturma</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="update_customers" value="create_employees" id="createEmployees{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="createEmployees{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-user-edit fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Çalışan Güncelleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="update_employees" value="update_employees" id="updateEmployees{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="updateEmployees{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-user-times fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Çalışan Silme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="delete_employees" value="delete_employees" id="deleteEmployees{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="deleteEmployees{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <!-- Hizmet İzinleri -->
                                    <div class="section-title fw-500 text-primary pt-4 pb-2">Hizmet İzinleri</div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-concierge-bell fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Tüm Hizmetleri Görüntüleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="all_services" value="all_services" id="allServices{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="allServices{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-plus-circle fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Hizmet Oluşturma</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="create_services" value="create_services" id="createServices{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="createServices{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-edit fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Hizmet Güncelleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="update_services" value="update_services" id="updateServices{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="updateServices{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-trash-alt fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Hizmet Silme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="delete_services" value="delete_services" id="deleteServices{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="deleteServices{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <!-- Finans İzinleri -->
                                    <div class="section-title fw-500 text-primary pt-4 pb-2">Finans İzinleri</div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-file-invoice-dollar fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Tüm Faturaları Görüntüleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="all_invoices" value="all_invoices" id="allInvoices{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="allInvoices{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-money-check-alt fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Tüm Ödemeleri Görüntüleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="all_payments" value="all_payments" id="allPayments{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="allPayments{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-chart-line fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Tüm Satışları Görüntüleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="all_sales" value="all_sales" id="allSales{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="allSales{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-wallet fs-xl me-3 text-primary"></i>
                                           <span class="fw-500">&nbsp Tüm Giderleri Görüntüleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="all_expenses" value="all_expenses" id="allExpenses{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="allExpenses{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <!-- Kampanya İzinleri -->
                                    <div class="section-title fw-500 text-primary pt-4 pb-2">Kampanya İzinleri</div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-bullhorn fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Tüm Kampanyaları Görüntüleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="all_campaigns" value="all_campaigns" id="allCampaigns{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="allCampaigns{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-plus-circle fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Kampanya Oluşturma</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="create_campaigns" value="create_campaigns" id="createCampaigns{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="createCampaigns{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-edit fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Kampanya Güncelleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="update_campaigns" value="update_campaigns" id="updateCampaigns{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="updateCampaigns{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-trash-alt fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Kampanya Silme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="delete_campaigns" value="delete_campaigns" id="deleteCampaigns{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="deleteCampaigns{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <!-- Geri Bildirim İzinleri -->
                                    <div class="section-title fw-500 text-primary pt-4 pb-2">Geri Bildirim İzinleri</div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-comments fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Tüm Geri Bildirimleri Görüntüleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="all_feedbacks" value="all_feedbacks" id="allFeedbacks{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="allFeedbacks{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-comment-plus fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Geri Bildirim Oluşturma</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="create_feedbacks" value="create_feedbacks" id="createFeedbacks{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="createFeedbacks{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-comment-edit fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Geri Bildirim Güncelleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="update_feedbacks" value="update_feedbacks" id="updateFeedbacks{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="updateFeedbacks{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-comment-times fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Geri Bildirim Silme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="delete_feedbacks" value="delete_feedbacks" id="deleteFeedbacks{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="deleteFeedbacks{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <!-- Sistem Ayarları -->
                                    <div class="section-title fw-500 text-primary pt-4 pb-2">Sistem Ayarları</div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-cogs fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Sistem Ayarlarını Görüntüleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="all_settings" value="all_settings" id="allSettings{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="allSettings{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-cog fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Sistem Ayarlarını Güncelleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="update_settings" value="update_settings" id="updateSettings{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="updateSettings{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <!-- Kullanıcı Yönetimi -->
                                    <div class="section-title fw-500 text-primary pt-4 pb-2">Kullanıcı Yönetimi</div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-users fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Tüm Kullanıcıları Görüntüleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="all_users" value="all_users" id="allUsers{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="allUsers{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-user-plus fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Kullanıcı Oluşturma</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="create_users" value="create_users" id="createUsers{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="createUsers{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-user-edit fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Kullanıcı Güncelleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="update_users" value="update_users" id="updateUsers{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="updateUsers{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-user-times fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Kullanıcı Silme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="delete_users" value="delete_users" id="deleteUsers{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="deleteUsers{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="section-title fw-500 text-primary pt-4 pb-2">Kategori Yönetimi</div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-folders fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Tüm Kategorileri Görüntüleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="all_categories" value="all_categories" id="allCategories{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="allCategories{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-folder-plus fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Kategori Oluşturma</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="create_categories" value="create_categories" id="createCategories{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="createCategories{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-folder-open fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Kategori Güncelleme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="update_categories" value="update_categories" id="updateCategories{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="updateCategories{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-folder-minus fs-xl me-3 text-primary"></i>
                                            <span class="fw-500">&nbsp Kategori Silme</span>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="delete_categories" value="delete_categories" id="deleteCategories{{ $user->id }}" data-user-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="deleteCategories{{ $user->id }}"></label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
    $('#js_change_tab_direction input').on('change', function()
    {
        var newclass = $('input[name=radioNameTabDirection]:checked', '#js_change_tab_direction').val();
        $('#js_change_tab_direction').parent('.panel-tag').next('.nav.nav-tabs').removeClass().addClass(newclass);
    });
    $('#js_change_pill_direction input').on('change', function()
    {
        var newclass = $('input[name=radioNamePillDirection]:checked', '#js_change_pill_direction').val();
        $('#js_change_pill_direction').parent('.panel-tag').next('.nav.nav-pills').removeClass().addClass(newclass);
    });

</script>

<!---axios-->
<script src="https://cdn.jsdelivr.net/npm/axios@1.1.2/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal içindeki tüm checkbox'ları seç
    const modals = document.querySelectorAll('[id^="editModal"]');
    
    modals.forEach(modal => {
        // Her modal için izin değişikliklerini dinle
        const checkboxes = modal.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const user_id = this.dataset.userId;
                const permission_name = this.value;
                const status = this.checked ? 1 : 0;

                // Toastr ayarları
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "timeOut": "3000"
                };

                axios.post('/users/update-permissions', {
                    user_id: user_id,
                    permission_name: permission_name,
                    status: status
                })
                .then(function(response) {
                    toastr.success('İzin başarıyla güncellendi');
                })
                .catch(function(error) {
                    console.error('Hata:', error);
                    
                    let errorMessage = 'İzin güncellenirken bir hata oluştu';
                    if (error.response && error.response.data && error.response.data.message) {
                        errorMessage = error.response.data.message;
                    }
                    
                    toastr.error(errorMessage);
                    checkbox.checked = !status;
                });
            });
        });

        // Rol izinlerini tanımlayan nesne
        const rolePermissions = {
            patron: 'all',
            super_admin: 'all',
            admin: [
                'all_reservations', 'create_reservations', 'update_reservations',
                'all_customers', 'create_customers', 'update_customers',
                'all_employees', 'update_employees',
                'all_services', 'update_services',
                'all_invoices', 'all_payments', 'all_sales',
                'all_campaigns', 'create_campaigns', 'update_campaigns',
                'all_feedbacks', 'create_feedbacks', 'update_feedbacks'
            ],
            receptionist: [
                'all_reservations', 'create_reservations', 'update_reservations',
                'all_customers', 'create_customers', 'update_customers',
                'all_services',
                'create_feedbacks'
            ],
            estetist: [
                'all_reservations',
                'all_customers',
                'all_services',
                'create_feedbacks'
            ]
        };

        // İzinleri güncelleyen fonksiyon
        function updatePermissions(userId, permissions) {
            const promises = permissions.map(permission => {
                return axios.post('/users/update-permissions', {
                    user_id: userId,
                    permission_name: permission,
                    status: 1
                });
            });

            Promise.all(promises)
                .then(() => {
                    toastr.success('İzinler başarıyla güncellendi');
                })
                .catch(error => {
                    console.error('Hata:', error);
                    toastr.error('İzinler güncellenirken bir hata oluştu');
                });
        }

        // Tüm izinleri sıfırlayan fonksiyon
        function resetPermissions(userId) {
            modal.querySelectorAll(`input[type="checkbox"]`).forEach(checkbox => {
                checkbox.checked = false;
            });
        }

        // Seçili izinleri işaretleyen fonksiyon
        function setPermissions(userId, permissions) {
            if (permissions === 'all') {
                modal.querySelectorAll(`input[type="checkbox"]`).forEach(checkbox => {
                    checkbox.checked = true;
                });
                return Array.from(modal.querySelectorAll(`input[type="checkbox"]:checked`))
                    .map(checkbox => checkbox.value);
            }
            
            permissions.forEach(permission => {
                const checkbox = modal.querySelector(`input[value="${permission}"]`);
                if (checkbox) checkbox.checked = true;
            });
            return permissions;
        }

        // Rol değişikliğini dinle
        const permissionSelect = modal.querySelector('select[id^="permission"]');
        if (permissionSelect) {
            permissionSelect.addEventListener('change', function() {
                const userId = this.dataset.userId;
                const selectedRole = this.value;
                
                // İzinleri sıfırla
                resetPermissions(userId);
                
                // Seçilen role göre izinleri ayarla
                const permissions = rolePermissions[selectedRole];
                if (!permissions) {
                    toastr.error('Geçersiz rol seçimi');
                    return;
                }
                
                // İzinleri işaretle ve güncelle
                const updatedPermissions = setPermissions(userId, permissions);
                updatePermissions(userId, updatedPermissions);
            });
        }
    });

    // Mevcut izinleri ayarla
    @foreach($users as $user)
        const permissions{{ $user->id }} = {!! json_encode($user->permissions) !!};
        const modal{{ $user->id }} = document.querySelector('#editModal{{ $user->id }}');
        
        permissions{{ $user->id }}.forEach(function(permission) {
            const checkbox = modal{{ $user->id }}.querySelector(`input[value="${permission.permission_name}"]`);
            if (checkbox) checkbox.checked = true;
        });
    @endforeach
});
</script>


<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
