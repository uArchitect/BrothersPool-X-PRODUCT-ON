@include('layouts.header')

<style>
    #dt-basic-example {
        width: 100% !important;
    }
    
    #dt-basic-example th, #dt-basic-example td {
        white-space: nowrap;
        vertical-align: middle;
    }
    
    .dataTables_wrapper .row .col-md-6 {
        display: flex;
        align-items: center;
    }
    
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 0;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        width: 300px !important;
        margin-left: 0.5rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
        padding: 0.375rem 0.75rem;
    }
    
    .table-responsive {
        border: none;
        border-radius: 0;
        overflow: hidden;
    }
    
    .dataTables_processing {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 200px;
        margin-left: -100px;
        margin-top: -26px;
        text-align: center;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        font-weight: bold;
    }
    
    .sale-row {
        cursor: pointer;
    }
    
    .sale-row:hover {
        background-color: #f8f9fa;
    }
</style>

<main id="js-page-content" role="main" class="page-content">
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr">
                    <h2>Satış Yönetimi <span class="fw-300"><i>Yönetim</i></span></h2>
                    <div class="panel-toolbar">
                        <!-- Tarih Filtreleri -->
                        <div class="btn-group" role="group" aria-label="Tarih Filtreleri">
                            <button type="button" class="btn btn-sm btn-outline-primary active" id="filter-today" data-filter="today">
                                <i class="fal fa-calendar-day mr-1"></i> Bugün
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="filter-week" data-filter="week">
                                <i class="fal fa-calendar-week mr-1"></i> Bu Hafta
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="filter-month" data-filter="month">
                                <i class="fal fa-calendar mr-1"></i> Bu Ay
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="filter-all" data-filter="all">
                                <i class="fal fa-list mr-1"></i> Tümü
                            </button>
                        </div>
                    </div>
                </div>

                <div class="panel-container show">
                    <div class="panel-content">
                        <!-- Üst Bilgi Kartı -->
                        <div class="row mb-4">
                            <div class="col-xl-12">
                                <div class="p-3 bg-white border-left border-primary border-3 shadow-sm rounded">
                                    <div class="d-flex align-items-center">
                                        <div class="d-inline-block flex-1">
                                            <h5 class="mb-2 text-primary">
                                                <i class="fal fa-shopping-cart mr-2"></i> 
                                                Satış Faturaları
                                            </h5>
                                            <p class="mb-0 fs-md text-dark">
                                                Bu panelden tüm satış faturalarını görüntüleyebilir, detaylarını inceleyebilir ve 
                                                satış raporlarınızı yönetebilirsiniz. Satış verilerinizi analiz edebilir ve 
                                                müşteri bazlı satış geçmişine erişebilirsiniz.
                                            </p>
                                        </div>
                                        <div class="d-inline-block">
                                            <div class="btn-group">
                                              
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tablo -->
                        <div class="table-responsive">
                            <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
                                <thead class="bg-highlight">
                                    <tr>
                                        <th>Satış ID</th>
                                        <th>Müşteri</th>
                                        <th>Toplam</th>
                                        <th>Ödenen</th>
                                        <th>Kalan</th>
                                        <th>Durum</th>
                                    </tr>
                                </thead>
                                <thead>
                                    <tr>
                                        <th><input type="text" class="form-control form-control-sm" placeholder="Satış ID"></th>
                                        <th><input type="text" class="form-control form-control-sm" placeholder="Müşteri"></th>
                                        <th><input type="text" class="form-control form-control-sm" placeholder="Toplam"></th>
                                        <th><input type="text" class="form-control form-control-sm" placeholder="Ödenen"></th>
                                        <th><input type="text" class="form-control form-control-sm" placeholder="Kalan"></th>
                                        <th><input type="text" class="form-control form-control-sm" placeholder="Durum"></th>
                                    </tr>
                                </thead>
                                <tbody id="sales-tbody">
                                    <!-- AJAX ile yüklenecek -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Satış ID</th>
                                        <th>Müşteri</th>
                                        <th>Toplam</th>
                                        <th>Ödenen</th>
                                        <th>Kalan</th>
                                        <th>Durum</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')

<!-- Gerekli JS dosyaları -->
<script src="{{ asset('js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('js/notifications/sweetalert2/sweetalert2.bundle.js') }}"></script>

<script>
    let salesTable;
    let isLoading = false;
    
    $(document).ready(() => {
        initDataTable();
        setupEvents();
    });
    
    /* -------------------- DATATABLE -------------------- */
    function initDataTable() {
        salesTable = $('#dt-basic-example').DataTable({
            responsive: true,
            pageLength: 25,
            processing: true,
            order: [[0, 'desc']],
            language: getDataTableLang(),
            columnDefs: [
                { className: "text-center", targets: [0, 5] },
                { className: "text-end", targets: [2, 3, 4] }
            ],
            ajax: {
                url: '{{ route('sales.data') }}?date_filter=today',
                type: 'GET',
                dataSrc: json => json.success ? json.data : [],
                error: () => showError('Veri yükleme hatası oluştu')
            },
            columns: getTableColumns(),
            rowCallback: setRowAttributes
        });
    
        // Column search
        salesTable.columns().every(function() {
            $('input', this.header()).on('keyup change', e => {
                if (this.search() !== e.target.value) {
                    this.search(e.target.value).draw();
                }
            });
        });
    }
    
    function getDataTableLang() {
        return { 
            url: '{{ asset('media/data/tr.json') }}',
            processing: "İşleniyor...",
            lengthMenu: "Sayfa başına _MENU_ kayıt göster",
            info: "_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor",
            infoEmpty: "Kayıt yok",
            paginate: { first: "İlk", last: "Son", next: "Sonraki", previous: "Önceki" }
        };
    }
    
    function getTableColumns() {
        return [
            { data: 'id', title: 'Satış ID' },
            { data: 'customer', title: 'Müşteri' },
            { data: 'total', title: 'Toplam', render: formatCurrency },
            { data: 'total', title: 'Ödenen', render: formatCurrency }, // All sales are fully paid
            { 
                data: 'remaining', title: 'Kalan',
                render: data => {
                    const badgeClass = data > 0 ? 'danger' : 'success';
                    return `<span class="badge badge-${badgeClass}">${formatCurrency(data)}</span>`;
                }
            },
            { 
                data: 'status', title: 'Durum',
                render: (data, type, row) => {
                    const badgeClass = data === 'completed' ? 'success' : 
                                       (data === 'pending' ? 'warning' : 'danger');
                    return `<span class="badge badge-${badgeClass}">${data}</span>`;
                }
            }
        ];
    }
    
    function setRowAttributes(row, data) {
        $(row).addClass('sale-row').data({
            id: data.id,
            customer: data.customer,
            total: data.total,
            paid: data.total, // All sales are fully paid
            remaining: data.remaining,
            status: data.status,
            date: data.created_at,
            services: data.product_names || '',
            subtotals: data.quantities || '',
            identity: data.customer_phone || '',
            address_line1: '',
            address_line2: ''
        });
    }
    
    /* -------------------- EVENTS -------------------- */
    function setupEvents() {
        $(document).on('click', '#dt-basic-example tbody tr.sale-row', e => {
            if (!isLoading) showSaleModal($(e.currentTarget));
        });
    
        $(document).on('click', '.closeModal', () => $('#saleDetailsModal').modal('hide'));
        $('#saleDetailsModal').on('hidden.bs.modal', () => { isLoading = false; });
        $(document).on('click', '#generateEInvoiceBtn', generateEInvoice);
    }
    
    /* -------------------- MODAL -------------------- */
    function showSaleModal(row) {
        isLoading = true;
        const data = extractRowData(row);
    
        fillModal(data);
        $('#saleDetailsModal').modal('show');
    
        $('#saleDetailsModal').off('shown.bs.modal').on('shown.bs.modal', () => {
            setTimeout(() => setHiddenSaleIdFromModal(data.id), 100);
        });
    }
    
    function extractRowData(row) {
        return {
            id: row.data('id'),
            customer: row.data('customer') || 'Misafir Müşteri',
            date: row.data('date'),
            total: parseFloat(row.data('total') || 0),
            paid: parseFloat(row.data('paid') || 0),
            remaining: parseFloat(row.data('remaining') || 0),
            status: row.data('status') || 'completed',
            services: row.data('services') || '',
            identity: row.data('identity') || '',
            address: `${row.data('address_line1') || ''} ${row.data('address_line2') || ''}`.trim()
        };
    }
    
    function fillModal(data) {
        $('#modalInvoiceNo').text(`#INV-${data.id}`);
        $('#modalCustomer').text(data.customer);
        $('#modalDate').text(formatDate(data.date));
        $('#modalStatus').text(data.status);
        $('#modalUUID').text('Henüz kesilmedi');
        $('#modalTotal').text(formatCurrency(data.total));
        $('#modalPaid').text(formatCurrency(data.paid));
        $('#modalRemaining').text(formatCurrency(data.remaining));
        $('#modalCustomerTax').text(data.identity || 'Belirtilmemiş');
        $('#modalCustomerAddress').text(data.address || 'Adres belirtilmemiş');
        $('#modalProductsList').html(buildServicesTable(data));
    }
    
    function buildServicesTable(data) {
        if (!data.services) {
            return `
                <tr>
                    <td>1</td><td>Genel Hizmet</td>
                    <td class="text-center">1</td>
                    <td class="text-right">${formatCurrency(data.total)}</td>
                    <td class="text-right">${formatCurrency(data.total)}</td>
                </tr>`;
        }
    
        const services = data.services.split(',').map(s => s.trim()).filter(Boolean);
        const subtotals = $('#dt-basic-example tbody tr.sale-row[data-id="'+data.id+'"]').data('subtotals')?.split(',').map(s => parseFloat(s.trim()) || 0) || [];
    
        return services.map((service, i) => `
            <tr>
                <td>${i + 1}</td>
                <td>${service}</td>
                <td class="text-center">1</td>
                <td class="text-right">${formatCurrency(subtotals[i] || (data.total / services.length))}</td>
                <td class="text-right">${formatCurrency(subtotals[i] || (data.total / services.length))}</td>
            </tr>`).join('');
    }
    
    /* -------------------- HELPERS -------------------- */
    function showError(message) {
        Swal.fire({ title: 'Hata!', text: message, icon: 'error', confirmButtonText: 'Tamam' });
    }
    
    function formatCurrency(amount) {
        const num = parseFloat(amount);
        return isNaN(num) ? '0,00 ₺' : new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(num);
    }
    
    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return isNaN(date.getTime()) ? dateStr :
            new Intl.DateTimeFormat('tr-TR', { year:'numeric', month:'2-digit', day:'2-digit', hour:'2-digit', minute:'2-digit' }).format(date);
    }
    
    function generateEInvoice() {
        const saleId = $('#modalInvoiceNo').text().replace('#INV-', '');
        if (!saleId) return showError('Satış ID bulunamadı!');
        // TODO: e-fatura işlemi
    }
    
    function setHiddenSaleIdFromModal(saleId) {
        const hiddenSaleId = document.getElementById('hiddenSaleId');
        const hiddenCancelSaleId = document.getElementById('hiddenCancelSaleId');
        if (hiddenSaleId && hiddenCancelSaleId) {
            hiddenSaleId.value = hiddenCancelSaleId.value = saleId;
            document.getElementById('invoiceForm')?.setAttribute('data-sale-id', saleId);
        }
    }
    
    /* -------------------- DATE FILTERING -------------------- */
    function setupDateFilters() {
        $('.btn-group button[data-filter]').on('click', function() {
            const filter = $(this).data('filter');
            
            // Update button states
            $('.btn-group button').removeClass('active btn-primary').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('active btn-primary');
            
            // Apply filter to DataTable
            applyDateFilter(filter);
        });
    }
    
    function applyDateFilter(filter) {
        // Reload the table with the new filter
        loadSalesData(filter);
    }
    
    function loadSalesData(dateFilter = 'today') {
        const table = $('#dt-basic-example').DataTable();
        
        // Update the AJAX URL with the filter parameter
        table.ajax.url('{{ route('sales.data') }}?date_filter=' + dateFilter).load();
    }
    
    // Initialize date filters when document is ready
    $(document).ready(function() {
        setupDateFilters();
    });
    </script>
    
@include('layouts.module.sales_details_modal')
