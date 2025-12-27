<div class="modal fade" id="addGroupModal" tabindex="-1" aria-labelledby="addGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addGroupModalLabel">Yeni Grup Ekle</h5>
                <button type="button" class="btn btn-sm text-white" data-bs-dismiss="modal" aria-label="Kapat" style="border: none; background: none;">
            <i class="fal fa-times"></i>
        </button>
            </div>
            <div class="modal-body">
                <form id="addGroupForm" action="{{ route('users.groups.add') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="group_name">Grup Adı</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fal fa-users"></i>
                                </span>
                                <input type="text" class="form-control" id="group_name" name="name" required 
                                    placeholder="Grup adını giriniz">
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="group_description">Açıklama</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fal fa-info-circle"></i>
                                </span>
                                <textarea class="form-control" id="group_description" name="description" 
                                    rows="3" placeholder="Grup açıklamasını giriniz"></textarea>
                            </div>
                        </div>

                        <!-- Yeni Yetki Kontrolleri -->
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-calendar-check fs-xl me-3 text-primary"></i>
                                   <span class="fw-500">&nbsp Tüm Rezervasyonları Görüntüleme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="all_reservations" value="all_reservations" id="allReservations" data-user-id="">
                                    <label class="custom-control-label" for="allReservations"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-calendar-plus fs-xl me-3 text-primary"></i>
                                   <span class="fw-500">&nbsp Rezervasyon Oluşturma</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="create_reservations" value="create_reservations" id="createReservations" data-user-id="">
                                    <label class="custom-control-label" for="createReservations"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-calendar-edit fs-xl me-3 text-primary"></i>
                                   <span class="fw-500">&nbsp Rezervasyon Güncelleme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="update_reservations" value="update_reservations" id="updateReservations" data-user-id="">
                                    <label class="custom-control-label" for="updateReservations"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-calendar-times fs-xl me-3 text-primary"></i>
                                   <span class="fw-500">&nbsp Rezervasyon Silme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="delete_reservations" value="delete_reservations" id="deleteReservations" data-user-id="">
                                    <label class="custom-control-label" for="deleteReservations"></label>
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
                                    <input type="checkbox" class="custom-control-input" name="delete_reservations" value="all_customers" id="allCustomers" data-user-id="">
                                    <label class="custom-control-label" for="allCustomers"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-user-plus fs-xl me-3 text-primary"></i>
                                   <span class="fw-500">&nbsp Müşteri Oluşturma</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="create_customers" value="create_customers" id="createCustomers" data-user-id="">
                                    <label class="custom-control-label" for="createCustomers"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-user-edit fs-xl me-3 text-primary"></i>
                                   <span class="fw-500">&nbsp Müşteri Güncelleme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="update_customers" value="update_customers" id="updateCustomers" data-user-id="">
                                    <label class="custom-control-label" for="updateCustomers"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-user-times fs-xl me-3 text-primary"></i>
                                   <span class="fw-500">&nbsp Müşteri Silme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="delete_customers" value="delete_customers" id="deleteCustomers" data-user-id="">
                                    <label class="custom-control-label" for="deleteCustomers"></label>
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
                                    <input type="checkbox" class="custom-control-input" name="all_employees" value="all_employees" id="allEmployees" data-user-id="">
                                    <label class="custom-control-label" for="allEmployees"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-user-plus fs-xl me-3 text-primary"></i>
                                   <span class="fw-500">&nbsp Çalışan Oluşturma</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="update_customers" value="create_employees" id="createEmployees" data-user-id="">
                                    <label class="custom-control-label" for="createEmployees"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-user-edit fs-xl me-3 text-primary"></i>
                                   <span class="fw-500">&nbsp Çalışan Güncelleme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="update_employees" value="update_employees" id="updateEmployees" data-user-id="">
                                    <label class="custom-control-label" for="updateEmployees"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-user-times fs-xl me-3 text-primary"></i>
                                   <span class="fw-500">&nbsp Çalışan Silme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="delete_employees" value="delete_employees" id="deleteEmployees" data-user-id="">
                                    <label class="custom-control-label" for="deleteEmployees"></label>
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
                                    <input type="checkbox" class="custom-control-input" name="all_services" value="all_services" id="allServices" data-user-id="">
                                    <label class="custom-control-label" for="allServices"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-plus-circle fs-xl me-3 text-primary"></i>
                                   <span class="fw-500">&nbsp Hizmet Oluşturma</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="create_services" value="create_services" id="createServices" data-user-id="">
                                    <label class="custom-control-label" for="createServices"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-edit fs-xl me-3 text-primary"></i>
                                   <span class="fw-500">&nbsp Hizmet Güncelleme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="update_services" value="update_services" id="updateServices" data-user-id="">
                                    <label class="custom-control-label" for="updateServices"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-trash-alt fs-xl me-3 text-primary"></i>
                                   <span class="fw-500">&nbsp Hizmet Silme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="delete_services" value="delete_services" id="deleteServices" data-user-id="">
                                    <label class="custom-control-label" for="deleteServices"></label>
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
                                    <input type="checkbox" class="custom-control-input" name="all_invoices" value="all_invoices" id="allInvoices" data-user-id="">
                                    <label class="custom-control-label" for="allInvoices"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-money-check-alt fs-xl me-3 text-primary"></i>
                                   <span class="fw-500">&nbsp Tüm Ödemeleri Görüntüleme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="all_payments" value="all_payments" id="allPayments" data-user-id="">
                                    <label class="custom-control-label" for="allPayments"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-chart-line fs-xl me-3 text-primary"></i>
                                   <span class="fw-500">&nbsp Tüm Satışları Görüntüleme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="all_sales" value="all_sales" id="allSales" data-user-id="">
                                    <label class="custom-control-label" for="allSales"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-wallet fs-xl me-3 text-primary"></i>
                                   <span class="fw-500">&nbsp Tüm Giderleri Görüntüleme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="all_expenses" value="all_expenses" id="allExpenses" data-user-id="">
                                    <label class="custom-control-label" for="allExpenses"></label>
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
                                    <input type="checkbox" class="custom-control-input" name="all_campaigns" value="all_campaigns" id="allCampaigns" data-user-id="">
                                    <label class="custom-control-label" for="allCampaigns"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-plus-circle fs-xl me-3 text-primary"></i>
                                    <span class="fw-500">&nbsp Kampanya Oluşturma</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="create_campaigns" value="create_campaigns" id="createCampaigns" data-user-id="">
                                    <label class="custom-control-label" for="createCampaigns"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-edit fs-xl me-3 text-primary"></i>
                                    <span class="fw-500">&nbsp Kampanya Güncelleme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="update_campaigns" value="update_campaigns" id="updateCampaigns" data-user-id="">
                                    <label class="custom-control-label" for="updateCampaigns"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-trash-alt fs-xl me-3 text-primary"></i>
                                    <span class="fw-500">&nbsp Kampanya Silme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="delete_campaigns" value="delete_campaigns" id="deleteCampaigns" data-user-id="">
                                    <label class="custom-control-label" for="deleteCampaigns"></label>
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
                                    <input type="checkbox" class="custom-control-input" name="all_feedbacks" value="all_feedbacks" id="allFeedbacks" data-user-id="">
                                    <label class="custom-control-label" for="allFeedbacks"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-comment-plus fs-xl me-3 text-primary"></i>
                                    <span class="fw-500">&nbsp Geri Bildirim Oluşturma</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="create_feedbacks" value="create_feedbacks" id="createFeedbacks" data-user-id="">
                                    <label class="custom-control-label" for="createFeedbacks"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-comment-edit fs-xl me-3 text-primary"></i>
                                    <span class="fw-500">&nbsp Geri Bildirim Güncelleme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="update_feedbacks" value="update_feedbacks" id="updateFeedbacks" data-user-id="">
                                    <label class="custom-control-label" for="updateFeedbacks"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-comment-times fs-xl me-3 text-primary"></i>
                                    <span class="fw-500">&nbsp Geri Bildirim Silme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="delete_feedbacks" value="delete_feedbacks" id="deleteFeedbacks" data-user-id="">
                                    <label class="custom-control-label" for="deleteFeedbacks"></label>
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
                                    <input type="checkbox" class="custom-control-input" name="all_settings" value="all_settings" id="allSettings" data-user-id="">
                                    <label class="custom-control-label" for="allSettings"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-cog fs-xl me-3 text-primary"></i>
                                    <span class="fw-500">&nbsp Sistem Ayarlarını Güncelleme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="update_settings" value="update_settings" id="updateSettings" data-user-id="">
                                    <label class="custom-control-label" for="updateSettings"></label>
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
                                    <input type="checkbox" class="custom-control-input" name="all_users" value="all_users" id="allUsers" data-user-id="">
                                    <label class="custom-control-label" for="allUsers"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-user-plus fs-xl me-3 text-primary"></i>
                                    <span class="fw-500">&nbsp Kullanıcı Oluşturma</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="create_users" value="create_users" id="createUsers" data-user-id="">
                                    <label class="custom-control-label" for="createUsers"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-user-edit fs-xl me-3 text-primary"></i>
                                    <span class="fw-500">&nbsp Kullanıcı Güncelleme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="update_users" value="update_users" id="updateUsers" data-user-id="">
                                    <label class="custom-control-label" for="updateUsers"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-user-times fs-xl me-3 text-primary"></i>
                                    <span class="fw-500">&nbsp Kullanıcı Silme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="delete_users" value="delete_users" id="deleteUsers" data-user-id="">
                                    <label class="custom-control-label" for="deleteUsers"></label>
                                </div>
                            </div>

                            <div class="section-title fw-500 text-primary pt-4 pb-2">Kategori Yönetimi</div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-folders fs-xl me-3 text-primary"></i>
                                    <span class="fw-500">&nbsp Tüm Kategorileri Görüntüleme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="all_categories" value="all_categories" id="allCategories" data-user-id="">
                                    <label class="custom-control-label" for="allCategories"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-folder-plus fs-xl me-3 text-primary"></i>
                                    <span class="fw-500">&nbsp Kategori Oluşturma</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="create_categories" value="create_categories" id="createCategories" data-user-id="">
                                    <label class="custom-control-label" for="createCategories"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-folder-open fs-xl me-3 text-primary"></i>
                                    <span class="fw-500">&nbsp Kategori Güncelleme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="update_categories" value="update_categories" id="updateCategories" data-user-id="">
                                    <label class="custom-control-label" for="updateCategories"></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fal fa-folder-minus fs-xl me-3 text-primary"></i>
                                    <span class="fw-500">&nbsp Kategori Silme</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="delete_categories" value="delete_categories" id="deleteCategories" data-user-id="">
                                    <label class="custom-control-label" for="deleteCategories"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fal fa-times me-1"></i>İptal
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveGroupBtn">
                        <i class="fal fa-save me-1"></i>Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    //eğer change edilirse valuesu 1 olsun
    $('input[type="checkbox"]').on('change', function() {
        $(this).val(1);
    });
    
</script>