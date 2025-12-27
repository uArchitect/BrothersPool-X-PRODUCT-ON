@include('layouts.header')

<style>
/* Modern Restaurant Kitchen Theme */
:root {
    --primary: #2E7D32;      /* Deep Green - Restaurant theme */
    --primary-light: #4CAF50;
    --primary-dark: #1B5E20;
    --accent: #FF6F00;       /* Orange - Kitchen energy */
    --accent-light: #FFB74D;
    --warning: #FFA000;      /* Amber - Pending orders */
    --warning-light: #FFE082;
    --success: #388E3C;      /* Green - Ready orders */
    --success-light: #A5D6A7;
    --info: #1976D2;         /* Blue - Preparing orders */
    --info-light: #90CAF9;
    --danger: #D32F2F;       /* Red - Urgent */
    --danger-light: #EF9A9A;
    --dark: #212121;         /* Dark text */
    --light: #F5F5F5;        /* Light background */
    --white: #FFFFFF;
    --gray: #757575;
    --border: #E0E0E0;
    --shadow: rgba(0,0,0,0.1);
    --shadow-hover: rgba(0,0,0,0.15);
}

/* Global Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
}

/* Header Styles */
.kitchen-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: var(--white);
    padding: 1.5rem 0;
    box-shadow: 0 4px 20px var(--shadow);
    position: relative;
    overflow: hidden;
}

.kitchen-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.kitchen-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    position: relative;
    z-index: 1;
}

.kitchen-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0.5rem 0 0 0;
    position: relative;
    z-index: 1;
}

/* Control Buttons */
.control-btn {
    background: rgba(255,255,255,0.2);
    border: 2px solid rgba(255,255,255,0.3);
    color: var(--white);
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    position: relative;
    z-index: 1;
}

.control-btn:hover {
    background: rgba(255,255,255,0.3);
    border-color: rgba(255,255,255,0.5);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    color: var(--white);
}

.control-btn.active {
    background: var(--accent);
    border-color: var(--accent);
    box-shadow: 0 4px 15px rgba(255,111,0,0.4);
}

/* Stats Cards */
.stats-container {
    margin: 2rem 0;
}

.stat-card {
    background: var(--white);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 8px 32px var(--shadow);
    border: 1px solid var(--border);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: var(--accent);
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 16px 48px var(--shadow-hover);
}

.stat-card.pending::before { background: var(--warning); }
.stat-card.preparing::before { background: var(--info); }
.stat-card.ready::before { background: var(--success); }
.stat-card.total::before { background: var(--primary); }

.stat-number {
    font-size: 3.5rem;
    font-weight: 800;
    margin: 0;
    line-height: 1;
}

.stat-label {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0.5rem 0 0 0;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.stat-card.pending .stat-number { color: var(--warning); }
.stat-card.preparing .stat-number { color: var(--info); }
.stat-card.ready .stat-number { color: var(--success); }
.stat-card.total .stat-number { color: var(--primary); }

/* Filter Buttons */
.filter-container {
    margin: 2rem 0;
    text-align: center;
}

.filter-btn {
    background: var(--white);
    border: 2px solid var(--border);
    color: var(--dark);
    padding: 0.75rem 2rem;
    margin: 0 0.5rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.filter-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}

.filter-btn:hover::before {
    left: 100%;
}

.filter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px var(--shadow);
    color: var(--dark);
}

.filter-btn.active {
    background: var(--primary);
    border-color: var(--primary);
    color: var(--white);
    box-shadow: 0 4px 15px rgba(46,125,50,0.4);
}

/* Order Cards - Compact Design */
.orders-container {
    margin: 2rem 0;
}

.order-card {
    background: var(--white);
    border-radius: 15px;
    box-shadow: 0 4px 16px var(--shadow);
    border: 1px solid var(--border);
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    height: 100%;
}

.order-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--accent);
}

.order-card[data-status="pending"]::before { background: var(--warning); }
.order-card[data-status="preparing"]::before { background: var(--info); }
.order-card[data-status="ready"]::before { background: var(--success); }

.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px var(--shadow-hover);
}

.order-header {
    padding: 1rem;
    border-bottom: 1px solid var(--border);
    background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
}

.order-id {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--dark);
    margin: 0;
}

.order-time {
    font-size: 0.85rem;
    color: var(--gray);
    font-weight: 500;
}

.status-badge {
    padding: 0.3rem 0.7rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending {
    background: var(--warning-light);
    color: var(--warning);
    border: 1px solid var(--warning);
}

.status-preparing {
    background: var(--info-light);
    color: var(--info);
    border: 1px solid var(--info);
}

.status-ready {
    background: var(--success-light);
    color: var(--success);
    border: 1px solid var(--success);
}

.order-body {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    height: calc(100% - 80px);
}

.order-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.info-item {
    text-align: center;
    padding: 0.75rem 0.5rem;
    background: var(--light);
    border-radius: 10px;
    border: 1px solid var(--border);
}

.info-value {
    font-size: 1.3rem;
    font-weight: 800;
    margin: 0;
    line-height: 1;
}

.info-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray);
    margin: 0.25rem 0 0 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.order-items {
    margin-bottom: 1rem;
    flex-grow: 1;
}

.item-card {
    background: var(--light);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0.5rem;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.item-card:hover {
    background: var(--white);
    border-color: var(--primary);
    transform: translateX(3px);
}

.item-name {
    font-weight: 700;
    color: var(--dark);
    margin: 0 0 0.25rem 0;
    font-size: 0.85rem;
}

.item-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.item-quantity {
    background: var(--primary);
    color: var(--white);
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.75rem;
}

.item-time {
    color: var(--gray);
    font-weight: 600;
    font-size: 0.75rem;
}

.item-notes {
    background: var(--warning-light);
    border: 1px solid var(--warning);
    border-radius: 6px;
    padding: 0.5rem;
    margin-top: 0.5rem;
    font-style: italic;
    color: var(--warning);
    font-weight: 600;
    font-size: 0.75rem;
}

.action-btn {
    width: 100%;
    padding: 0.75rem;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    margin-top: auto;
}

.action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}

.action-btn:hover::before {
    left: 100%;
}

.action-btn.start {
    background: linear-gradient(135deg, var(--warning) 0%, var(--accent) 100%);
    color: var(--white);
    box-shadow: 0 2px 8px rgba(255,160,0,0.4);
}

.action-btn.ready {
    background: linear-gradient(135deg, var(--success) 0%, var(--primary) 100%);
    color: var(--white);
    box-shadow: 0 2px 8px rgba(56,142,60,0.4);
}

.action-btn.served {
    background: linear-gradient(135deg, var(--info) 0%, var(--primary) 100%);
    color: var(--white);
    box-shadow: 0 2px 8px rgba(25,118,210,0.4);
}

.action-btn.completed {
    background: linear-gradient(135deg, var(--primary) 0%, var(--success) 100%);
    color: var(--white);
    box-shadow: 0 2px 8px rgba(46,125,50,0.4);
}

.action-btn.finished {
    background: var(--light);
    color: var(--gray);
    border: 1px solid var(--border);
    cursor: not-allowed;
}

.action-btn.unknown {
    background: var(--light);
    color: var(--gray);
    border: 1px solid var(--border);
    cursor: not-allowed;
}

.action-btn:hover:not(.finished):not(.unknown) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px var(--shadow-hover);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--white);
    border-radius: 20px;
    box-shadow: 0 8px 32px var(--shadow);
    border: 1px solid var(--border);
}

.empty-icon {
    font-size: 4rem;
    color: var(--gray);
    margin-bottom: 1.5rem;
}

.empty-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 1rem;
}

.empty-text {
    font-size: 1.1rem;
    color: var(--gray);
    margin-bottom: 2rem;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .order-card {
        margin-bottom: 0.75rem;
    }
    
    .order-header {
        padding: 0.75rem;
    }
    
    .order-body {
        padding: 0.75rem;
    }
    
    .info-value {
        font-size: 1.1rem;
    }
    
    .item-name {
        font-size: 0.8rem;
    }
}

@media (max-width: 768px) {
    .kitchen-title {
        font-size: 2rem;
    }
    
    .stat-card {
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .stat-number {
        font-size: 2.5rem;
    }
    
    .order-info {
        grid-template-columns: 1fr;
    }
    
    .filter-btn {
        margin: 0.25rem;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    
    .order-card {
        margin-bottom: 0.5rem;
    }
    
    .order-header {
        padding: 0.5rem;
    }
    
    .order-body {
        padding: 0.5rem;
    }
    
    .info-value {
        font-size: 1rem;
    }
    
    .item-name {
        font-size: 0.75rem;
    }
    
    .action-btn {
        padding: 0.5rem;
        font-size: 0.8rem;
    }
}

/* Animations */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.pulse {
    animation: pulse 2s infinite;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.slide-in {
    animation: slideIn 0.6s ease-out;
}
</style>

<main id="js-page-content" role="main" class="page-content">
    <!-- Modern Kitchen Header -->
    <div class="kitchen-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="kitchen-title">
                        <i class="fal fa-fire pulse me-3"></i>
                        Mutfak Ekranı
                    </h1>
                    <p class="kitchen-subtitle">Canlı sipariş takibi ve mutfak yönetimi</p>
                </div>
                <div class="d-flex gap-3">
                    <button class="control-btn" onclick="refreshKitchenOrders()">
                        <i class="fal fa-sync-alt me-2"></i> Yenile
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Dashboard -->
    <div class="container-fluid">
        <div class="stats-container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card pending slide-in">
                        <div class="stat-number" id="pendingOrdersCount">{{ $kitchen_stats->pending_count ?? 0 }}</div>
                        <div class="stat-label">Bekleyen Siparişler</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card preparing slide-in">
                        <div class="stat-number" id="preparingOrdersCount">{{ $kitchen_stats->preparing_count ?? 0 }}</div>
                        <div class="stat-label">Hazırlanan Siparişler</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card ready slide-in">
                        <div class="stat-number" id="readyOrdersCount">{{ $kitchen_stats->ready_count ?? 0 }}</div>
                        <div class="stat-label">Hazır Siparişler</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card total slide-in">
                        <div class="stat-number" id="totalOrdersCount">{{ $kitchen_stats->total_count ?? 0 }}</div>
                        <div class="stat-label">Toplam Sipariş</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div class="filter-container">
            <button class="filter-btn active" data-filter="all">Tüm Siparişler</button>
            <button class="filter-btn" data-filter="pending">Bekleyen</button>
            <button class="filter-btn" data-filter="preparing">Hazırlanan</button>
            <button class="filter-btn" data-filter="ready">Hazır</button>
        </div>
        <!-- Orders Container -->
        <div class="orders-container">
            <div class="row g-3" id="kitchenOrdersGrid">
                @if($orders->count() > 0)
                    @foreach($orders as $order)
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="order-card slide-in" data-status="{{ $order->kitchen_status }}" data-order-id="{{ $order->order_id }}">
                                <!-- Order Header -->
                                <div class="order-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h3 class="order-id">#{{ $order->order_id }}</h3>
                                            <div class="order-time">{{ \Carbon\Carbon::parse($order->order_time)->format('H:i') }}</div>
                                        </div>
                                        <span class="status-badge status-{{ $order->kitchen_status }}">
                                            {{ $order->kitchen_status == 'pending' ? 'Bekliyor' : ($order->kitchen_status == 'preparing' ? 'Hazırlanıyor' : 'Hazır') }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Order Body -->
                                <div class="order-body">
                                    <!-- Order Info -->
                                    <div class="order-info">
                                        <div class="info-item">
                                            <div class="info-value text-primary">{{ $order->table_number }}</div>
                                            <div class="info-label">Masa</div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-value text-success">{{ number_format($order->total_amount, 0) }}₺</div>
                                            <div class="info-label">Tutar</div>
                                        </div>
                                    </div>
                                    
                                    @if($order->customer_name)
                                        <div class="mb-3 p-3 bg-light rounded-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fal fa-user text-primary me-2"></i>
                                                <div>
                                                    <div class="fw-700 text-dark">{{ $order->customer_name }}</div>
                                                    <small class="text-muted">Müşteri</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Order Items -->
                                    <div class="order-items">
                                        <h6 class="fw-700 mb-3 text-dark">
                                            <i class="fal fa-utensils text-primary me-2"></i>Sipariş Detayları
                                        </h6>
                                        @foreach($order->items as $item)
                                            <div class="item-card">
                                                <div class="item-name">{{ $item->item_name }}</div>
                                                <div class="item-details">
                                                    <span class="item-quantity">{{ $item->quantity }}x</span>
                                                    <span class="item-time">{{ $item->prep_time ?? 15 }} dakika</span>
                                                </div>
                                                @if($item->item_notes)
                                                    <div class="item-notes">
                                                        <i class="fal fa-sticky-note me-1"></i>{{ $item->item_notes }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    @if($order->order_notes)
                                        <div class="mb-3 p-3 bg-info bg-opacity-10 rounded-3 border border-info">
                                            <div class="d-flex align-items-center">
                                                <i class="fal fa-info-circle text-info me-2"></i>
                                                <div>
                                                    <div class="fw-700 text-dark">Sipariş Notu</div>
                                                    <div class="text-muted">{{ $order->order_notes }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Action Button -->
                                    @if($order->kitchen_status == 'pending')
                                        <button class="action-btn start" onclick="startOrderPreparation({{ $order->order_id }})">
                                            <i class="fal fa-play me-2"></i> Hazırlamaya Başla
                                        </button>
                                    @elseif($order->kitchen_status == 'preparing')
                                        <button class="action-btn ready" onclick="markOrderReady({{ $order->order_id }})">
                                            <i class="fal fa-check me-2"></i> Hazırlandı
                                        </button>
                                    @elseif($order->kitchen_status == 'ready')
                                        <button class="action-btn served" onclick="markOrderServed({{ $order->order_id }})">
                                            <i class="fal fa-hand-holding me-2"></i> Garsona Teslim Edildi
                                        </button>
                                    @elseif($order->kitchen_status == 'served')
                                        <button class="action-btn completed" onclick="completeOrder({{ $order->order_id }})">
                                            <i class="fal fa-check-circle me-2"></i> Siparişi Bitir
                                        </button>
                                    @elseif($order->kitchen_status == 'completed')
                                        <button class="action-btn finished" disabled>
                                            <i class="fal fa-check-double me-2"></i> Tamamlandı
                                        </button>
                                    @else
                                        <button class="action-btn unknown" disabled>
                                            <i class="fal fa-question me-2"></i> Bilinmeyen Durum
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fal fa-utensils"></i>
                            </div>
                            <h3 class="empty-title">Hazırlanacak Sipariş Yok</h3>
                            <p class="empty-text">Şu anda mutfakta hazırlanacak sipariş bulunmuyor.</p>
                            <button class="control-btn" onclick="refreshKitchenOrders()">
                                <i class="fal fa-sync-alt me-2"></i> Yenile
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')

<script>
// Modern Kitchen Management System - Correct Order Flow
$(document).ready(function() {
    initializeKitchenSystem();
});

function initializeKitchenSystem() {
    // Initialize filter buttons
    $('[data-filter]').on('click', function() {
        const filter = $(this).data('filter');
        filterOrders(filter);
        
        // Update active button
        $('[data-filter]').removeClass('active');
        $(this).addClass('active');
    });
    
    // Initialize sound notification
    window.kitchenAudio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT');
}

// Refresh kitchen orders
function refreshKitchenOrders() {
    $.ajax({
        url: '/orders/api/kitchen',
        type: 'GET',
        cache: false,
        beforeSend: function() {
            $('#kitchenOrdersGrid').addClass('loading');
        },
        success: function(response) {
            if (response.success) {
                updateKitchenDisplay(response.data);
                updateKitchenStatistics(response.data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Kitchen orders refresh failed:', error);
            showNotification('Siparişler yüklenirken hata oluştu', 'error');
        },
        complete: function() {
            $('#kitchenOrdersGrid').removeClass('loading');
        }
    });
}

// Update kitchen display
function updateKitchenDisplay(orders) {
    const grid = $('#kitchenOrdersGrid');
    
    if (orders.length === 0) {
        grid.html(`
            <div class="col-12">
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fal fa-utensils"></i>
                    </div>
                    <h3 class="empty-title">Hazırlanacak Sipariş Yok</h3>
                    <p class="empty-text">Şu anda mutfakta hazırlanacak sipariş bulunmuyor.</p>
                    <button class="control-btn" onclick="refreshKitchenOrders()">
                        <i class="fal fa-sync-alt me-2"></i> Yenile
                    </button>
                </div>
            </div>
        `);
        return;
    }

    // Remove duplicates
    const uniqueOrders = [];
    const seenOrderIds = [];
    
    orders.forEach(function(order) {
        if (!seenOrderIds.includes(order.order_id)) {
            uniqueOrders.push(order);
            seenOrderIds.push(order.order_id);
        }
    });

    let ordersHtml = '';
    uniqueOrders.forEach(function(order) {
        ordersHtml += generateOrderCard(order);
    });

    grid.html(ordersHtml);
}

// Generate order card HTML
function generateOrderCard(order) {
    const statusInfo = getStatusInfo(order.kitchen_status);
    const orderTime = new Date(order.order_time).toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
    
    return `
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="order-card slide-in" data-status="${order.kitchen_status}" data-order-id="${order.order_id}">
                <div class="order-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="order-id">#${order.order_id}</h3>
                            <div class="order-time">${orderTime}</div>
                        </div>
                        <span class="status-badge status-${order.kitchen_status}">${statusInfo.text}</span>
                    </div>
                </div>
                <div class="order-body">
                    <div class="order-info">
                        <div class="info-item">
                            <div class="info-value text-primary">${order.table_number}</div>
                            <div class="info-label">Masa</div>
                        </div>
                        <div class="info-item">
                            <div class="info-value text-success">${parseFloat(order.total_amount).toFixed(0)}₺</div>
                            <div class="info-label">Tutar</div>
                        </div>
                    </div>
                    ${order.customer_name ? generateCustomerInfo(order.customer_name) : ''}
                    ${generateOrderItems(order.items)}
                    ${order.order_notes ? generateOrderNotes(order.order_notes) : ''}
                    ${generateActionButton(order.kitchen_status, order.order_id)}
                </div>
            </div>
        </div>
    `;
}

// Get status information
function getStatusInfo(status) {
    const statusMap = {
        'pending': { text: 'Bekliyor', class: 'warning' },
        'preparing': { text: 'Hazırlanıyor', class: 'info' },
        'ready': { text: 'Hazır', class: 'success' },
        'served': { text: 'Servis Edildi', class: 'primary' },
        'completed': { text: 'Tamamlandı', class: 'secondary' }
    };
    return statusMap[status] || { text: 'Bilinmiyor', class: 'secondary' };
}

// Generate customer info
function generateCustomerInfo(customerName) {
    return `
        <div class="mb-3 p-3 bg-light rounded-3">
            <div class="d-flex align-items-center">
                <i class="fal fa-user text-primary me-2"></i>
                <div>
                    <div class="fw-700 text-dark">${customerName}</div>
                    <small class="text-muted">Müşteri</small>
                </div>
            </div>
        </div>
    `;
}

// Generate order items
function generateOrderItems(items) {
    if (!items || items.length === 0) return '';
    
    let itemsHtml = `
        <div class="order-items">
            <h6 class="fw-700 mb-3 text-dark">
                <i class="fal fa-utensils text-primary me-2"></i>Sipariş Detayları
            </h6>
    `;
    
    items.forEach(item => {
        itemsHtml += `
            <div class="item-card">
                <div class="item-name">${item.item_name}</div>
                <div class="item-details">
                    <span class="item-quantity">${item.quantity}x</span>
                    <span class="item-time">${item.prep_time || 15} dakika</span>
                </div>
                ${item.item_notes ? `
                    <div class="item-notes">
                        <i class="fal fa-sticky-note me-1"></i>${item.item_notes}
                    </div>
                ` : ''}
            </div>
        `;
    });
    
    itemsHtml += '</div>';
    return itemsHtml;
}

// Generate order notes
function generateOrderNotes(notes) {
    return `
        <div class="mb-3 p-3 bg-info bg-opacity-10 rounded-3 border border-info">
            <div class="d-flex align-items-center">
                <i class="fal fa-info-circle text-info me-2"></i>
                <div>
                    <div class="fw-700 text-dark">Sipariş Notu</div>
                    <div class="text-muted">${notes}</div>
                </div>
            </div>
        </div>
    `;
}

// Generate action button based on status
function generateActionButton(status, orderId) {
    switch(status) {
        case 'pending':
            return `
                <button class="action-btn start" onclick="startOrderPreparation(${orderId})">
                    <i class="fal fa-play me-2"></i> Hazırlamaya Başla
                </button>
            `;
        case 'preparing':
            return `
                <button class="action-btn ready" onclick="markOrderReady(${orderId})">
                    <i class="fal fa-check me-2"></i> Hazırlandı
                </button>
            `;
        case 'ready':
            return `
                <button class="action-btn served" onclick="markOrderServed(${orderId})">
                    <i class="fal fa-hand-holding me-2"></i> Garsona Teslim Edildi
                </button>
            `;
        case 'served':
            return `
                <button class="action-btn completed" onclick="completeOrder(${orderId})">
                    <i class="fal fa-check-circle me-2"></i> Siparişi Bitir
                </button>
            `;
        case 'completed':
            return `
                <button class="action-btn finished" disabled>
                    <i class="fal fa-check-double me-2"></i> Tamamlandı
                </button>
            `;
        default:
            return `
                <button class="action-btn unknown" disabled>
                    <i class="fal fa-question me-2"></i> Bilinmeyen Durum
                </button>
            `;
    }
}

// Update kitchen statistics
function updateKitchenStatistics(orders) {
    const stats = {
        pending: 0,
        preparing: 0,
        ready: 0,
        total: orders.length
    };

    orders.forEach(order => {
        const status = order.kitchen_status || 'pending';
        if (stats.hasOwnProperty(status)) {
            stats[status]++;
        }
    });

    $('#pendingOrdersCount').text(stats.pending);
    $('#preparingOrdersCount').text(stats.preparing);
    $('#readyOrdersCount').text(stats.ready);
    $('#totalOrdersCount').text(stats.total);
}

// Filter orders
function filterOrders(filter) {
    const cards = $('.order-card');
    
    cards.each(function() {
        const status = $(this).data('status');
        const shouldShow = filter === 'all' || status === filter;
        
        if (shouldShow) {
            $(this).closest('.col-lg-4').show();
        } else {
            $(this).closest('.col-lg-4').hide();
        }
    });
}

// Order status management functions - CORRECT FLOW
function startOrderPreparation(orderId) {
    updateOrderStatus(orderId, 'preparing', 'Hazırlamaya başlandı');
}

function markOrderReady(orderId) {
    updateOrderStatus(orderId, 'ready', 'Sipariş hazırlandı');
}

function markOrderServed(orderId) {
    updateOrderStatus(orderId, 'served', 'Garsona teslim edildi');
}

function completeOrder(orderId) {
    updateOrderStatus(orderId, 'completed', 'Sipariş tamamlandı');
}

// Update order status
function updateOrderStatus(orderId, status, message) {
    $.ajax({
        url: `/orders/${orderId}/update-status`,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            status: status
        },
        success: function(response) {
            if (response.success) {
                showNotification(message, 'success');
                refreshKitchenOrders();
            } else {
                showNotification(response.message || 'Güncelleme başarısız', 'error');
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Güncelleme sırasında hata oluştu';
            showNotification(errorMessage, 'error');
        }
    });
}

// Show notification
function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fal fa-${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(() => {
        notification.alert('close');
    }, 5000);
}
</script>
 
 