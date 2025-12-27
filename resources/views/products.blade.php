@include('layouts.header')
@include('layouts.module.categories_add_modal', ['stockType' => 1])
@include('layouts.module.services_add_modal', ['isProduct' => true])
@include('layouts.module.services_edit_modal')
@include('layouts.module.stock_movements_modal')

<main id="js-page-content" role="main" class="page-content">
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr d-flex justify-content-between align-items-center">
                    <h2>Ürünler</h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-primary me-2" id="openCategoryModal">
                            <i class="fal fa-layer-plus"></i> Ürün Kategorisi Ekle
                        </button>
                        &nbsp;
                        <button class="btn btn-success" id="openServiceModal">
                            <i class="fal fa-cogs"></i> Ürün Ekle
                        </button>
                    </div>
                </div>

                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="panel-tag mb-4 bg-primary-500 text-white p-3 rounded shadow-sm">
                            <i class="fal fa-info-circle me-2"></i>
                            Bu tablonun amacı, salonunuzda sunulan stoklu ürünlerinizi kolayca yönetebilmenizdir.
                            Ürünlerinizin detaylarına, düzenlemelere ve silme işlemlerine kolayca erişebilirsiniz.
                        </div>
                        <div class="table-responsive">
                            <table id="dt-products" class="table table-bordered table-hover w-100">
                                <thead>
                                    <tr class="bg-primary text-white">
                                        <th class="fw-500">Ürün Adı</th>
                                        <th class="fw-500">Açıklama</th>
                                        <th class="fw-500">Fiyat</th>
                                        <th class="fw-500">KDV</th>
                                        <th class="fw-500">Stok Durumu</th>
                                        <th class="fw-500">Depo</th>
                                        <th class="fw-500">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($services as $service)
                                        @if($service->is_stock)
                                        <tr data-id="{{ $service->id }}" class="border-bottom">
                                            <td class="fw-500">{{ $service->name }}</td>
                                            <td>{{ Str::limit($service->description, 50) }}</td>
                                            <td class="fw-500 text-success">{{ $service->total_price }} ₺</td>
                                            <td class="text-primary">{{ $service->tax_rate }} %</td>
                                            <td>
                                                <span class="badge bg-success rounded-pill px-3 py-2">Stoklu</span>
                                            </td>
                                            <td>
                                                @if($service->warehouse_name)
                                                    <span class="badge bg-info rounded-pill px-3 py-2">
                                                        <i class="fal fa-warehouse me-1"></i>{{ $service->warehouse_name }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning rounded-pill px-3 py-2">
                                                        <i class="fal fa-exclamation-triangle me-1"></i>Depo Yok
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-info btn-sm rounded-pill px-3 shadow-sm stock-movements-btn" 
                                                        data-product-id="{{ $service->id }}" 
                                                        data-product-name="{{ $service->name }}"
                                                        title="Stok Hareketleri">
                                                    <i class="fal fa-chart-line me-1"></i> Stok
                                                </button>
                                                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm edit-service-modal-btn" data-service-id="{{ $service->id }}">
                                                    <i class="fal fa-edit me-1"></i> Düzenle
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm delete-service-btn" data-id="{{ $service->id }}">
                                                    <i class="fal fa-trash me-1"></i> Sil
                                                </button>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> <!-- end panel -->
        </div> <!-- end col -->
    </div> <!-- end row -->

</main>

@include('layouts.footer')

<!-- DataTables Bundle Script - Required for this page -->
<script src="{{ asset('js/datagrid/datatables/datatables.bundle.js') }}"></script>

<script>
    // Safe DOM ready function
    function safeReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    safeReady(function() {
        try {
            // Modal açma butonları için event listener'lar
            const openCategoryBtn = document.getElementById('openCategoryModal');
            const openServiceBtn = document.getElementById('openServiceModal');
            
            if (openCategoryBtn) {
                openCategoryBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const categoryModal = document.getElementById('addCategoryModal');
                    if (categoryModal) {
                        try {
                            // Önce mevcut modal instance'ını kontrol et
                            let modalInstance = bootstrap.Modal.getInstance(categoryModal);
                            if (modalInstance) {
                                modalInstance.dispose();
                            }
                            // Yeni modal instance'ı oluştur
                            const modal = new bootstrap.Modal(categoryModal);
                            modal.show();
                        } catch (error) {
                            console.warn('Bootstrap modal error, trying jQuery:', error);
                            try {
                                $('#addCategoryModal').modal('show');
                            } catch (jqError) {
                                console.error('Both modal methods failed:', jqError);
                            }
                        }
                    } else {
                        console.error('Category modal not found');
                    }
                });
            }

            if (openServiceBtn) {
                openServiceBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const serviceModal = document.getElementById('addServiceModal');
                    if (serviceModal) {
                        try {
                            // Önce mevcut modal instance'ını kontrol et
                            let modalInstance = bootstrap.Modal.getInstance(serviceModal);
                            if (modalInstance) {
                                modalInstance.dispose();
                            }
                            // Yeni modal instance'ı oluştur
                            const modal = new bootstrap.Modal(serviceModal);
                            modal.show();
                        } catch (error) {
                            console.warn('Bootstrap modal error, trying jQuery:', error);
                            try {
                                $('#addServiceModal').modal('show');
                            } catch (jqError) {
                                console.error('Both modal methods failed:', jqError);
                            }
                        }
                    } else {
                        console.error('Service modal not found');
                    }
                });
            }

            // DataTable initialization (yeni tablo id'leri)
            if (typeof $ !== 'undefined' && $.fn && $.fn.DataTable) {
                window.productsTable = $('#dt-products').DataTable({
                    orderCellsTop: true,
                    fixedHeader: true,
                    responsive: true,
                    language: {
                        paginate: {
                            first: "İlk",
                            last: "Son", 
                            next: "Sonraki",
                            previous: "Önceki"
                        },
                        info: "Gösterilen: _START_ - _END_ / _TOTAL_",
                        infoEmpty: "Gösterilecek kayıt yok",
                        zeroRecords: "Eşleşen kayıt bulunamadı"
                    }
                });
            }

            // Hizmet Ekleme Form
            const addServiceForm = document.getElementById('addServiceModal');
            if (addServiceForm) {
                const form = addServiceForm.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        const submitBtn = form.querySelector('button[type="submit"]');
                        const originalText = submitBtn.innerHTML;
                        
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fal fa-spinner fa-spin mr-1"></i> Kaydediliyor...';
                        
                        if (typeof axios !== 'undefined') {
                            const formData = new FormData(form);
                            axios.post("{{ route('products.add') }}", formData)
                                .then(res => {
                                    if (res.data && res.data.service) {
                                        addServiceToTable(res.data.service);
                                        showSuccess('Ürün başarıyla eklendi');
                                        form.reset();
                                        const modal = bootstrap.Modal.getInstance(addServiceForm);
                                        if (modal) modal.hide();
                                    }
                                })
                                .catch(err => {
                                    let msg = 'Ürün eklenirken bir hata oluştu';
                                    if (err.response?.data?.errors) {
                                        msg = Object.values(err.response.data.errors).flat().join('<br>');
                                    } else if (err.response?.data?.message) {
                                        msg = err.response.data.message;
                                    }
                                    showError(msg);
                                })
                                .finally(() => {
                                    if (submitBtn) {
                                        submitBtn.disabled = false;
                                        submitBtn.innerHTML = originalText;
                                    }
                                });
                        } else {
                            showError('Axios kütüphanesi yüklenemedi. Lütfen sayfayı yenileyin.');
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalText;
                            }
                        }
                    });
                }
            }

            // Hizmet Güncelleme Form
            document.addEventListener('submit', function(e) {
                if (e.target.classList.contains('edit-service-form')) {
                    e.preventDefault();
                    
                    const form = e.target;
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fal fa-spinner fa-spin mr-1"></i> Güncelleniyor...';
                    
                    if (typeof axios !== 'undefined') {
                        const formData = new FormData(form);
                        axios.post("{{ route('products.update') }}", formData)
                            .then(res => {
                                if (res.data && res.data.service) {
                                    updateServiceInTable(res.data.service);
                                    showSuccess('Ürün başarıyla güncellendi');
                                    const modal = form.closest('.modal');
                                    if (modal) {
                                        const bsModal = bootstrap.Modal.getInstance(modal);
                                        if (bsModal) bsModal.hide();
                                    }
                                }
                            })
                            .catch(err => {
                                let msg = 'Ürün güncellenirken bir hata oluştu';
                                if (err.response?.data?.errors) {
                                    msg = Object.values(err.response.data.errors).flat().join('<br>');
                                } else if (err.response?.data?.message) {
                                    msg = err.response.data.message;
                                }
                                showError(msg);
                            })
                            .finally(() => {
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = originalText;
                                }
                            });
                    } else {
                        showError('Axios kütüphanesi yüklenemedi. Lütfen sayfayı yenileyin.');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    }
                }
            });

            // Ürün Silme
            document.addEventListener('click', function(e) {
                if (e.target.closest('.delete-service-btn')) {
                    e.preventDefault();
                    
                    const btn = e.target.closest('.delete-service-btn');
                    const id = btn.dataset.id;
                    
                    showConfirm('Bu ürünü silmek istediğinize emin misiniz?', 'Ürün Sil', 'Evet, Sil', 'Vazgeç')
                    .then((result) => {
                        if (result.isConfirmed) {
                            btn.disabled = true;
                            btn.innerHTML = '<i class="fal fa-spinner fa-spin"></i>';
                            
                            if (typeof axios !== 'undefined') {
                                axios.post("{{ route('products.delete') }}", {
                                    id: id,
                                    _token: document.querySelector('meta[name="csrf-token"]').content
                                })
                                .then(res => {
                                    showSuccess('Ürün başarıyla silindi');
                                    removeServiceFromTable(id);
                                })
                                .catch(err => {
                                    let msg = 'Ürün silinirken bir hata oluştu';
                                    if (err.response?.data?.message) {
                                        msg = err.response.data.message;
                                    }
                                    showError(msg);
                                })
                                .finally(() => {
                                    if (btn) {
                                        btn.disabled = false;
                                        btn.innerHTML = '<i class="fal fa-trash me-1"></i> Sil';
                                    }
                                });
                            } else {
                                showError('Axios kütüphanesi yüklenemedi. Lütfen sayfayı yenileyin.');
                                if (btn) {
                                    btn.disabled = false;
                                    btn.innerHTML = '<i class="fal fa-trash me-1"></i> Sil';
                                }
                            }
                        }
                    });
                }
            });

            // Stok Hareketleri Modal Açma
            document.addEventListener('click', function(e) {
                if (e.target.closest('.stock-movements-btn')) {
                    const btn = e.target.closest('.stock-movements-btn');
                    const productId = btn.dataset.productId;
                    const productName = btn.dataset.productName;
                    
                    if (typeof window.openStockMovementsModal === 'function') {
                        window.openStockMovementsModal(productId, productName);
                    } else {
                        showError('Stok hareketleri modal\'ı yüklenemedi');
                    }
                }
            });

            // Edit Modal Açma
            document.addEventListener('click', function(e) {
                if (e.target.closest('.edit-service-modal-btn')) {
                    const btn = e.target.closest('.edit-service-modal-btn');
                    const serviceId = btn.dataset.serviceId;
                    
                    if (document.getElementById('editServiceModal' + serviceId)) {
                        const modal = new bootstrap.Modal(document.getElementById('editServiceModal' + serviceId));
                        modal.show();
                    } else {
                        showInfo('Modal yükleniyor...');
                        if (typeof axios !== 'undefined') {
                            axios.get("{{ route('services.edit', '') }}/" + serviceId)
                                .then(res => {
                                    if (res.data && res.data.view) {
                                        document.body.insertAdjacentHTML('beforeend', res.data.view);
                                        const modal = new bootstrap.Modal(document.getElementById('editServiceModal' + serviceId));
                                        modal.show();
                                        showSuccess('Modal başarıyla yüklendi');
                                    } else {
                                        showError('Modal yüklenemedi');
                                    }
                                })
                                .catch(err => {
                                    showError('Modal yüklenirken bir hata oluştu');
                                });
                        }
                    }
                }
            });

            // Modal Temizleme - Sadece dinamik yüklenen edit modal'ları için
            document.addEventListener('hidden.bs.modal', function(e) {
                const modal = e.target;
                const modalId = modal.id;
                
                // Sadece dinamik yüklenen edit modal'larını kaldır
                if (modalId && modalId.startsWith('editServiceModal') && !modal.classList.contains('static-modal')) {
                    // Modal'ı kaldırmadan önce kısa bir gecikme ekle
                    setTimeout(() => {
                        if (modal && modal.parentNode) {
                            // Modal instance'ını temizle
                            try {
                                const modalInstance = bootstrap.Modal.getInstance(modal);
                                if (modalInstance) {
                                    modalInstance.dispose();
                                }
                            } catch (error) {
                                console.warn('Modal instance cleanup error:', error);
                            }
                            // DOM'dan kaldır
                            modal.remove();
                        }
                    }, 300);
                }
            });

            // Modal açıldığında form reset
            document.addEventListener('shown.bs.modal', function(e) {
                const modal = e.target;
                const modalId = modal.id;
                
                // Services add modal için form reset
                if (modalId === 'addServiceModal') {
                    const form = modal.querySelector('form');
                    if (form) {
                        form.reset();
                        // Vergi hesaplama fonksiyonunu çağır
                        const calculateEvent = new Event('input');
                        const priceInput = form.querySelector('#productPrice');
                        if (priceInput) {
                            priceInput.dispatchEvent(calculateEvent);
                        }
                    }
                }
            });

        } catch (error) {
            console.warn('Products page initialization error:', error);
        }
    });

    // Tabloya yeni ürün ekle
    function addServiceToTable(service) {
        if (!window.productsTable) return;
        
        const description = service.description || '';
        const shortDesc = description.length > 50 ? description.substring(0, 50) + '...' : description;
        
        // Depo bilgisi için
        let warehouseHtml = '<span class="text-muted">-</span>';
        if (service.is_stock) {
            if (service.warehouse_name) {
                warehouseHtml = '<span class="badge bg-info rounded-pill px-3 py-2"><i class="fal fa-warehouse me-1"></i>' + service.warehouse_name + '</span>';
            } else {
                warehouseHtml = '<span class="badge bg-warning rounded-pill px-3 py-2"><i class="fal fa-exclamation-triangle me-1"></i>Depo Yok</span>';
            }
        }
        
        const rowData = [
            service.name,
            shortDesc,
            (service.total_price || service.price) + ' ₺',
            (service.tax_rate || 0) + ' %',
            service.is_stock ? '<span class="badge bg-success rounded-pill px-3 py-2">Stoklu</span>' : '<span class="badge bg-warning rounded-pill px-3 py-2">Stoksuz</span>',
            warehouseHtml,
            getServiceActions(service)
        ];
        
        const rowNode = window.productsTable.row.add(rowData).draw().node();
        rowNode.setAttribute('data-id', service.id);
        rowNode.setAttribute('data-stock', service.is_stock ? 'stocked' : 'stockless');
    }

    // Tablo satırını güncelle
    function updateServiceInTable(service) {
        if (!window.productsTable) return;
        
        const row = document.querySelector(`#dt-products tbody tr[data-id="${service.id}"]`);
        if (row) {
            const description = service.description || '';
            const shortDesc = description.length > 50 ? description.substring(0, 50) + '...' : description;
            
            // Depo bilgisi için
            let warehouseHtml = '<span class="text-muted">-</span>';
            if (service.is_stock) {
                if (service.warehouse_name) {
                    warehouseHtml = '<span class="badge bg-info rounded-pill px-3 py-2"><i class="fal fa-warehouse me-1"></i>' + service.warehouse_name + '</span>';
                } else {
                    warehouseHtml = '<span class="badge bg-warning rounded-pill px-3 py-2"><i class="fal fa-exclamation-triangle me-1"></i>Depo Yok</span>';
                }
            }
            
            const rowData = [
                service.name,
                shortDesc,
                (service.total_price || service.price) + ' ₺',
                (service.tax_rate || 0) + ' %',
                service.is_stock ? '<span class="badge bg-success rounded-pill px-3 py-2">Stoklu</span>' : '<span class="badge bg-warning rounded-pill px-3 py-2">Stoksuz</span>',
                warehouseHtml,
                getServiceActions(service)
            ];
            
            window.productsTable.row(row).data(rowData).draw();
            row.setAttribute('data-stock', service.is_stock ? 'stocked' : 'stockless');
        }
    }

    // Tablo satırını sil
    function removeServiceFromTable(id) {
        if (!window.productsTable) return;
        
        const row = document.querySelector(`#dt-products tbody tr[data-id="${id}"]`);
        if (row) {
            window.productsTable.row(row).remove().draw();
        }
    }

    // Aksiyon butonları
    function getServiceActions(service) {
        return `
            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-info btn-sm rounded-pill px-3 shadow-sm stock-movements-btn" 
                        data-product-id="${service.id}" 
                        data-product-name="${service.name}"
                        title="Stok Hareketleri">
                    <i class="fal fa-chart-line me-1"></i> Stok
                </button>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm edit-service-modal-btn" data-service-id="${service.id}">
                    <i class="fal fa-edit me-1"></i> Düzenle
                </button>
                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm delete-service-btn" data-id="${service.id}">
                    <i class="fal fa-trash me-1"></i> Sil
                </button>
            </div>
        `;
    }
</script>

