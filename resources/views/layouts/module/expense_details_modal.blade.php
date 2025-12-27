<style>
    .modal .modal-dialog {
        max-width: 90%;
    }

    .modal .modal-dialog .modal-content {
        border: 0;
        border-radius: 0;
    }

    .modal .modal-dialog .modal-content .modal-header {
        border-bottom: 0;
    }
</style>
@foreach ($expenses as $expense)
    <div class="modal fade" id="detailsModal{{ $expense->id }}" tabindex="-1" role="dialog"
        aria-labelledby="detailsModalLabel{{ $expense->id }}" aria-hidden="true" data-expense-id="{{ $expense->id }}">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content border-0 shadow-lg rounded-lg">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-light py-3">
                                    <h6 class="mb-0 d-flex align-items-center">
                                        <i class="fal fa-info-circle mr-2"></i>
                                        Genel Bilgiler
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="text-muted"><i
                                                    class="fal fa-calendar-alt mr-2"></i>Tarih</span>
                                            <strong>{{ date('d.m.Y', strtotime($expense->date)) }}</strong>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="text-muted"><i class="fal fa-file-alt mr-2"></i>Belge No</span>
                                        {{ $expense->expense_number }}</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="text-muted"><i class="fal fa-tags mr-2"></i>Gider Tipi</span>
                                             {{ $expense->expense_type_name }}
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="text-muted"><i class="fal fa-wallet mr-2"></i>Hesap</span>
                                            <strong>{{ $expense->account_name }}</strong>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="text-muted"><i class="fal fa-user mr-2"></i>Personel</span>
                                            <strong>{{ $expense->employee_name ?? '-' }}</strong>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="text-muted"><i class="fal fa-user mr-2"></i>Notlar</span>
                                            <strong>{!!  $expense->note ?? '-' !!}</strong>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="text-muted"><i class="fal fa-file-image mr-2"></i>Fatura
                                                Fotoğrafı</span>
                                            @if($expense->invoice_photo)
                                                <img src="{{ asset('images/' . $expense->invoice_photo) }}" alt="Fatura Fotoğrafı"
                                                    class="img-fluid" style="max-width: 200px;">
                                            @else
                                                <span class="text-muted">Yüklenmedi</span>
                                            @endif
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kalemler -->
                        <div class="col-md-12 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-light py-3">
                                    <!--Kalemler-->
                                    <h6 class="mb-0 d-flex align-items-center">
                                        <i class="fal fa-list-alt mr-2"></i>
                                        Kalemler
                                    </h6>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover w-100">
                                                <thead>
                                                    <tr>
                                                        <th>Kategori</th>
                                                        <th>Kalem</th>
                                                        <th>Tutar</th>
                                                        <th>Açıklama</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="expenseDetails">
                                                    <tr>
                                                        <td colspan="4" class="text-center">Yükleniyor...</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-light border-top-0">
                    <div class="w-100 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                            data-dismiss="modal">
                            <i class="fal fa-times mr-1"></i> Kapat
                        </button>
                        <div>
                            <button type="button" class="btn btn-info btn-sm rounded-pill px-3 mr-2">
                                <i class="fal fa-print mr-1"></i> Yazdır
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script>
// Debounce function for performance optimization
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

$(document).ready(function() {
    // Modal script initialized
    
    // Her modal için event listener - debounced for performance
    $('[id^="detailsModal"]').on('show.bs.modal', debounce(function() {
        var expenseId = $(this).data('expense-id');
        var tbody = $(this).find('#expenseDetails');
        
        // Loading göster
        tbody.html('<tr><td colspan="4" class="text-center">Yükleniyor...</td></tr>');
        
        // AJAX isteği with timeout and optimized DOM manipulation
        $.ajax({
            url: '/ajax/expense-items/' + expenseId,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            cache: true,
            success: function(data) {
                tbody.empty();
                
                if (data && data.length > 0) {
                    // Optimize DOM manipulation by building HTML string first
                    var rowsHtml = data.map(function(item) {
                        return '<tr>' +
                            '<td>' + (item.category_name || '-') + '</td>' +
                            '<td>' + (item.expense || '-') + '</td>' +
                            '<td>' + parseFloat(item.amount || 0).toFixed(2) + ' ₺</td>' +
                            '<td>' + (item.description || '-') + '</td>' +
                            '</tr>';
                    }).join('');
                    tbody.html(rowsHtml);
                } else {
                    tbody.html('<tr><td colspan="4" class="text-center">Veri bulunamadı</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                tbody.html('<tr><td colspan="4" class="text-center text-danger">Yükleme hatası</td></tr>');
            }
        });
    }, 300));

    // Yazdırma fonksiyonu - optimized with event delegation
    $(document).on('click', '.modal .btn-info', function(e) {
        e.preventDefault();
        window.print();
    });

    // Memory cleanup on page unload
    $(window).on('beforeunload', function() {
        // Remove event listeners
        $('[id^="detailsModal"]').off('show.bs.modal');
        $(document).off('click', '.modal .btn-info');
        
        // Clear any timers
        if (window.debounceTimeouts) {
            Object.values(window.debounceTimeouts).forEach(clearTimeout);
        }
    });
});
</script>
