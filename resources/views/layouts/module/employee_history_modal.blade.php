<style>
    /* Modern CSS Custom Properties with Enhanced Design System - Scoped to Employee History Modal */
    #employeeHistoryModal {
        /* Primary Colors - Modern Gradient System */
        --primary-50: #e3f2fd;
        --primary-100: #bbdefb;
        --primary-200: #90caf9;
        --primary-300: #64b5f6;
        --primary-400: #42a5f5;
        --primary-500: #2196f3;
        --primary-600: #1e88e5;
        --primary-700: #1976d2;
        --primary-800: #1565c0;
        --primary-900: #0d47a1;
        
        /* Success Colors */
        --success-50: #e8f5e8;
        --success-100: #c8e6c9;
        --success-500: #4caf50;
        --success-700: #388e3c;
        
        /* Warning Colors */
        --warning-50: #fff8e1;
        --warning-100: #ffecb3;
        --warning-500: #ff9800;
        --warning-700: #f57c00;
        
        /* Error Colors */
        --error-50: #ffebee;
        --error-100: #ffcdd2;
        --error-500: #f44336;
        --error-700: #d32f2f;
        
        /* Neutral Colors */
        --gray-50: #fafafa;
        --gray-100: #f5f5f5;
        --gray-200: #eeeeee;
        --gray-300: #e0e0e0;
        --gray-400: #bdbdbd;
        --gray-500: #9e9e9e;
        --gray-600: #757575;
        --gray-700: #616161;
        --gray-800: #424242;
        --gray-900: #212121;
        
        /* Semantic Colors */
        --text-primary: var(--gray-900);
        --text-secondary: var(--gray-600);
        --text-disabled: var(--gray-400);
        --background-default: #ffffff;
        --background-paper: #ffffff;
        --background-neutral: var(--gray-50);
        
        /* Shadows - Layered Design System */
        --shadow-1: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        --shadow-2: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
        --shadow-3: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
        --shadow-4: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
        --shadow-5: 0 19px 38px rgba(0, 0, 0, 0.30), 0 15px 12px rgba(0, 0, 0, 0.22);
        
        /* Border Radius */
        --radius-xs: 4px;
        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --radius-xl: 20px;
        --radius-2xl: 24px;
        
        /* Spacing Scale */
        --space-1: 0.25rem;  /* 4px */
        --space-2: 0.5rem;   /* 8px */
        --space-3: 0.75rem;  /* 12px */
        --space-4: 1rem;     /* 16px */
        --space-5: 1.25rem;  /* 20px */
        --space-6: 1.5rem;   /* 24px */
        --space-8: 2rem;     /* 32px */
        --space-10: 2.5rem;  /* 40px */
        --space-12: 3rem;    /* 48px */
        --space-16: 4rem;    /* 64px */
        
        /* Typography */
        --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        --font-weight-normal: 400;
        --font-weight-medium: 500;
        --font-weight-semibold: 600;
        --font-weight-bold: 700;
        
        /* Transitions */
        --transition-fast: 0.15s ease-out;
        --transition-base: 0.2s ease-out;
        --transition-slow: 0.3s ease-out;

        /* Base Styles */
        font-family: var(--font-family);
        color: var(--text-primary);
        line-height: 1.6;
        font-size: 14px;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Enhanced Modal Styling */
    #employeeHistoryModal .modal-xl {
        max-width: 90vw !important;
        width: 90vw !important;
    }

    #employeeHistoryModal .modal-content {
        border-radius: var(--radius-xl);
        border: none;
        box-shadow: var(--shadow-5);
        overflow: hidden;
        background: var(--background-paper);
    }

    /* Modern Header with Gradient */
    #employeeHistoryModal .panel-hdr {
        background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-800) 100%);
        color: white;
        padding: var(--space-6) var(--space-8);
        position: relative;
        overflow: hidden;
    }

    #employeeHistoryModal .panel-hdr::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%);
        pointer-events: none;
    }

    #employeeHistoryModal .panel-hdr h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: var(--font-weight-bold);
        display: flex;
        align-items: center;
        gap: var(--space-3);
        position: relative;
        z-index: 1;
    }

    #employeeHistoryModal .panel-hdr .close {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition-base);
        backdrop-filter: blur(10px);
        position: relative;
        z-index: 1;
    }

    #employeeHistoryModal .panel-hdr .close:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.05);
    }

    /* Enhanced Summary Cards */
    #employeeHistoryModal .summary-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--space-6);
        padding: var(--space-8);
        background: var(--background-neutral);
    }

    #employeeHistoryModal .summary-card {
        background: var(--background-paper);
        border-radius: var(--radius-lg);
        padding: var(--space-6);
        box-shadow: var(--shadow-1);
        border: 1px solid var(--gray-200);
        transition: var(--transition-base);
        position: relative;
        overflow: hidden;
    }

    #employeeHistoryModal .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-500), var(--primary-700));
        transition: var(--transition-base);
    }

    #employeeHistoryModal .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-3);
        border-color: var(--primary-200);
    }

    #employeeHistoryModal .summary-card:hover::before {
        height: 6px;
    }

    #employeeHistoryModal .summary-card-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        background: linear-gradient(135deg, var(--primary-500), var(--primary-700));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin-bottom: var(--space-4);
        box-shadow: var(--shadow-2);
    }

    #employeeHistoryModal .summary-card-value {
        font-size: 2rem;
        font-weight: var(--font-weight-bold);
        color: var(--text-primary);
        margin-bottom: var(--space-2);
        line-height: 1.2;
    }

    #employeeHistoryModal .summary-card-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
        font-weight: var(--font-weight-medium);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Modern Tab Design */
    #employeeHistoryModal .nav-tabs-modern {
        border: none;
        background: var(--background-paper);
        padding: var(--space-2);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-1);
        margin: var(--space-6) var(--space-8) 0;
    }

    #employeeHistoryModal .nav-tabs-modern .nav-item {
        margin: 0;
    }

    #employeeHistoryModal .nav-tabs-modern .nav-link {
        border: none;
        border-radius: var(--radius-md);
        padding: var(--space-3) var(--space-5);
        color: var(--text-secondary);
        font-weight: var(--font-weight-medium);
        transition: var(--transition-base);
        background: transparent;
        margin: 0 var(--space-1);
    }

    #employeeHistoryModal .nav-tabs-modern .nav-link:hover {
        background: var(--gray-100);
        color: var(--text-primary);
    }

    #employeeHistoryModal .nav-tabs-modern .nav-link.active {
        background: var(--primary-500);
        color: white;
        box-shadow: var(--shadow-2);
    }

    /* Enhanced Table Design */
    #employeeHistoryModal .table-container {
        padding: var(--space-8);
        background: var(--background-paper);
    }

    #employeeHistoryModal .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: var(--background-paper);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-2);
        border: 1px solid var(--gray-200);
    }

    #employeeHistoryModal .modern-table thead th {
        background: var(--gray-50);
        color: var(--text-primary);
        font-weight: var(--font-weight-semibold);
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: var(--space-4) var(--space-5);
        border-bottom: 2px solid var(--gray-200);
        position: sticky;
        top: 0;
        z-index: 10;
    }

    #employeeHistoryModal .modern-table tbody tr {
        transition: var(--transition-fast);
        border-bottom: 1px solid var(--gray-100);
    }

    #employeeHistoryModal .modern-table tbody tr:hover {
        background: var(--primary-50);
        transform: scale(1.001);
    }

    #employeeHistoryModal .modern-table tbody tr:last-child {
        border-bottom: none;
    }

    #employeeHistoryModal .modern-table tbody td {
        padding: var(--space-5);
        vertical-align: middle;
        font-size: 0.875rem;
    }

    /* Modern Badge System */
    #employeeHistoryModal .badge-modern {
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        padding: var(--space-2) var(--space-3);
        border-radius: var(--radius-md);
        font-size: 0.75rem;
        font-weight: var(--font-weight-semibold);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid transparent;
        transition: var(--transition-fast);
    }

    #employeeHistoryModal .badge-primary {
        background: var(--primary-100);
        color: var(--primary-700);
        border-color: var(--primary-200);
    }

    #employeeHistoryModal .badge-success {
        background: var(--success-100);
        color: var(--success-700);
        border-color: var(--success-200);
    }

    #employeeHistoryModal .badge-warning {
        background: var(--warning-100);
        color: var(--warning-700);
        border-color: var(--warning-200);
    }

    #employeeHistoryModal .badge-error {
        background: var(--error-100);
        color: var(--error-700);
        border-color: var(--error-200);
    }

    #employeeHistoryModal .badge-gray {
        background: var(--gray-100);
        color: var(--gray-700);
        border-color: var(--gray-200);
    }

    /* Service Items Design */
    #employeeHistoryModal .service-items-list {
        display: flex;
        flex-direction: column;
        gap: var(--space-2);
        max-width: 300px;
    }

    #employeeHistoryModal .service-item-modern {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--space-3) var(--space-4);
        background: var(--gray-50);
        border-radius: var(--radius-sm);
        border: 1px solid var(--gray-200);
        transition: var(--transition-fast);
    }

    #employeeHistoryModal .service-item-modern:hover {
        background: var(--primary-50);
        border-color: var(--primary-200);
    }

    #employeeHistoryModal .service-name {
        font-weight: var(--font-weight-medium);
        color: var(--text-primary);
        font-size: 0.875rem;
    }

    #employeeHistoryModal .service-details {
        font-size: 0.75rem;
        color: var(--text-secondary);
        margin-top: var(--space-1);
    }

    #employeeHistoryModal .service-price {
        font-weight: var(--font-weight-bold);
        color: var(--primary-600);
        background: var(--primary-100);
        padding: var(--space-1) var(--space-2);
        border-radius: var(--radius-xs);
        font-size: 0.75rem;
    }

    /* Enhanced Footer */
    #employeeHistoryModal .panel-footer {
        background: var(--background-paper);
        border-top: 1px solid var(--gray-200);
        padding: var(--space-6) var(--space-8);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--space-4);
    }

    #employeeHistoryModal .footer-stats {
        display: flex;
        gap: var(--space-4);
        align-items: center;
    }

    #employeeHistoryModal .footer-badge {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        padding: var(--space-2) var(--space-4);
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        font-weight: var(--font-weight-medium);
        background: var(--gray-100);
        color: var(--gray-700);
        border: 1px solid var(--gray-200);
    }

    #employeeHistoryModal .footer-badge.success {
        background: var(--success-100);
        color: var(--success-700);
        border-color: var(--success-200);
    }

    #employeeHistoryModal .footer-badge.warning {
        background: var(--warning-100);
        color: var(--warning-700);
        border-color: var(--warning-200);
    }

    /* Modern Button Design */
    #employeeHistoryModal .btn-modern {
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        padding: var(--space-3) var(--space-5);
        border-radius: var(--radius-md);
        font-weight: var(--font-weight-semibold);
        font-size: 0.875rem;
        border: none;
        cursor: pointer;
        transition: var(--transition-base);
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    #employeeHistoryModal .btn-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: var(--transition-slow);
    }

    #employeeHistoryModal .btn-modern:hover::before {
        left: 100%;
    }

    #employeeHistoryModal .btn-primary {
        background: linear-gradient(135deg, var(--primary-500), var(--primary-700));
        color: white;
        box-shadow: var(--shadow-2);
    }

    #employeeHistoryModal .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-3);
    }

    #employeeHistoryModal .btn-secondary {
        background: var(--gray-500);
        color: white;
        box-shadow: var(--shadow-1);
    }

    #employeeHistoryModal .btn-secondary:hover {
        background: var(--gray-600);
        transform: translateY(-1px);
        box-shadow: var(--shadow-2);
    }

    /* Checkbox Styling */
    #employeeHistoryModal .modern-checkbox {
        width: 20px;
        height: 20px;
        border: 2px solid var(--gray-300);
        border-radius: var(--radius-xs);
        cursor: pointer;
        transition: var(--transition-fast);
        position: relative;
    }

    #employeeHistoryModal .modern-checkbox:checked {
        background: var(--primary-500);
        border-color: var(--primary-500);
    }

    #employeeHistoryModal .modern-checkbox:checked::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 12px;
        font-weight: bold;
    }

    /* Loading State */
    #employeeHistoryModal .loading-skeleton {
        background: linear-gradient(90deg, var(--gray-200) 25%, var(--gray-100) 50%, var(--gray-200) 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: var(--radius-sm);
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Empty State */
    #employeeHistoryModal .empty-state {
        text-align: center;
        padding: var(--space-16) var(--space-8);
        color: var(--text-secondary);
    }

    #employeeHistoryModal .empty-state-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto var(--space-6);
        background: var(--gray-100);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: var(--gray-400);
    }

    #employeeHistoryModal .empty-state h3 {
        margin-bottom: var(--space-2);
        color: var(--text-primary);
        font-weight: var(--font-weight-semibold);
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        #employeeHistoryModal .modal-xl {
            max-width: 95vw !important;
            width: 95vw !important;
        }
    }

    @media (max-width: 992px) {
        #employeeHistoryModal .summary-cards-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--space-4);
            padding: var(--space-6);
        }
        
        #employeeHistoryModal .panel-hdr {
            padding: var(--space-5) var(--space-6);
        }
        
        #employeeHistoryModal .panel-hdr h2 {
            font-size: 1.25rem;
        }
    }

    @media (max-width: 768px) {
        #employeeHistoryModal .modal-xl {
            max-width: 100vw !important;
            width: 100vw !important;
            margin: 0 !important;
            height: 100vh !important;
        }
        
        #employeeHistoryModal .modal-content {
            border-radius: 0;
            height: 100vh;
            overflow-y: auto;
        }
        
        #employeeHistoryModal .summary-cards-grid {
            grid-template-columns: 1fr 1fr;
            gap: var(--space-3);
            padding: var(--space-4);
        }
        
        #employeeHistoryModal .panel-footer {
            flex-direction: column;
            align-items: stretch;
            gap: var(--space-4);
        }
        
        #employeeHistoryModal .footer-stats {
            justify-content: center;
            flex-wrap: wrap;
        }
        
        #employeeHistoryModal .btn-modern {
            width: 100%;
            justify-content: center;
        }
        
        /* Mobile Table */
        #employeeHistoryModal .modern-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
        
        #employeeHistoryModal .modern-table thead {
            display: none;
        }
        
        #employeeHistoryModal .modern-table tbody,
        #employeeHistoryModal .modern-table tr {
            display: block;
        }
        
        #employeeHistoryModal .modern-table tr {
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            margin-bottom: var(--space-4);
            padding: var(--space-4);
            background: var(--background-paper);
            box-shadow: var(--shadow-1);
        }
        
        #employeeHistoryModal .modern-table td {
            border: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--space-2) 0;
            border-bottom: 1px solid var(--gray-100);
        }
        
        #employeeHistoryModal .modern-table td:last-child {
            border-bottom: none;
        }
        
        #employeeHistoryModal .modern-table td::before {
            content: attr(data-label);
            font-weight: var(--font-weight-semibold);
            color: var(--text-secondary);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    }

    @media (max-width: 576px) {
        #employeeHistoryModal .summary-cards-grid {
            grid-template-columns: 1fr;
        }
        
        #employeeHistoryModal .panel-hdr {
            padding: var(--space-4) var(--space-5);
        }
        
        #employeeHistoryModal .panel-hdr h2 {
            font-size: 1.125rem;
        }
        
        #employeeHistoryModal .table-container {
            padding: var(--space-4);
        }
    }

    /* Accessibility */
    @media (prefers-reduced-motion: reduce) {
        #employeeHistoryModal * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        #employeeHistoryModal {
            --background-default: #121212;
            --background-paper: #1e1e1e;
            --background-neutral: #0a0a0a;
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
            --gray-50: #2a2a2a;
            --gray-100: #3a3a3a;
            --gray-200: #4a4a4a;
        }
    }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="modal fade" id="employeeHistoryModal" tabindex="-1" role="dialog" aria-labelledby="employeeHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <!-- Modern Header -->
            <div class="panel-hdr">
                <h2>
                    <i class="fal fa-percentage"></i>
                    Prim Geçmişi - <span id="employeeName"></span>
                </h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times"></i>
                </button>
            </div>
            
            <!-- Summary Cards -->
            <div class="summary-cards-grid" id="summaryCards">
                <!-- Dynamic content will be inserted here -->
            </div>
            
            <!-- Modern Tabs -->
            <ul class="nav nav-tabs-modern" id="historyTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-details" data-toggle="tab" href="#tabDetails" role="tab">
                        <i class="fal fa-list"></i> Prim Detayları
                    </a>
                </li>
            </ul>
            
            <!-- Tab Content -->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="tabDetails" role="tabpanel">
                    <div class="table-container">
                        <div id="historyTableContainer">
                            <!-- Loading skeleton -->
                            <div class="loading-skeleton" style="height: 200px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modern Footer -->
            <div class="panel-footer">
                <div class="footer-stats" id="footerBadges">
                    <!-- Dynamic badges will be inserted here -->
                </div>
                <div style="display: flex; gap: var(--space-3);">
                    <button type="button" class="btn-modern btn-secondary" data-dismiss="modal">
                        <i class="fal fa-times"></i>
                        Kapat
                    </button>
                    <form id="commissionPaymentForm" method="POST" action="/personeller/add-commission" style="display: inline;">
                        @csrf
                        <div id="commissionIdsContainer">
                            <!-- Dynamic commission ID inputs will be added here -->
                        </div>
                        <button type="submit" class="btn-modern btn-primary" id="addCommission">
                            <i class="fal fa-credit-card"></i>
                            Ödeme Yap
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
/**
 * Employee Commission History Modal - Modular Architecture
 * Handles the new data structure with sale_id, reservation_id, employee_id, total_commission, items array, and other fields
 */

// ============================================================================
// DATA MODELS & TYPES
// ============================================================================

/**
 * Commission Item Model
 * @typedef {Object} CommissionItem
 * @property {string} service_name - Name of the service
 * @property {string} service_price - Total price of the service
 * @property {number} quantity - Quantity of the service
 * @property {string} unit_price - Price per unit
 * @property {string} commission_amount - Commission amount for this item
 * @property {number} commission_status - Status of commission (0/1)
 */

/**
 * Commission Record Model
 * @typedef {Object} CommissionRecord
 * @property {number} sale_id - Sale ID
 * @property {number} reservation_id - Reservation ID
 * @property {number} employee_id - Employee ID
 * @property {string} total_commission - Total commission amount
 * @property {string} created_at - Creation timestamp
 * @property {string} updated_at - Last update timestamp
 * @property {number} status - Overall status (0/1)
 * @property {string} sale_date - Date of sale
 * @property {number} payment_status - Payment status (0/1)
 * @property {string} payment_date - Payment date
 * @property {string} customer_first_name - Customer first name
 * @property {string} customer_last_name - Customer last name
 * @property {CommissionItem[]} items - Array of service items
 */

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

const CommissionUtils = {
    /**
     * Format currency values
     * @param {number|string} amount - Amount to format
     * @param {string} currency - Currency symbol
     * @returns {string} Formatted currency string
     */
    formatCurrency: (amount, currency = '₺') => {
        const numAmount = parseFloat(amount) || 0;
        return numAmount.toFixed(2) + currency;
    },

    /**
     * Format date to Turkish locale
     * @param {string} dateString - Date string to format
     * @returns {string} Formatted date string
     */
    formatDate: (dateString) => {
        return new Date(dateString).toLocaleDateString('tr-TR');
    },

    /**
     * Calculate total from items array
     * @param {CommissionItem[]} items - Array of items
     * @param {string} field - Field to sum (service_price, unit_price, commission_amount)
     * @returns {number} Total amount
     */
    calculateItemsTotal: (items, field = 'service_price') => {
        if (!items || !Array.isArray(items)) return 0;
        return items.reduce((sum, item) => sum + parseFloat(item[field] || 0), 0);
    },

    /**
     * Calculate commission rate
     * @param {string} commissionAmount - Commission amount
     * @param {CommissionItem[]} items - Items array
     * @returns {number} Commission rate percentage
     */
    calculateCommissionRate: (commissionAmount, items) => {
        const totalServicePrice = CommissionUtils.calculateItemsTotal(items, 'service_price');
        if (totalServicePrice <= 0) return 0;
        return ((parseFloat(commissionAmount || 0) / totalServicePrice) * 100);
    },

    /**
     * Get customer full name
     * @param {CommissionRecord} record - Commission record
     * @returns {string} Full customer name
     */
    getCustomerName: (record) => {
        const firstName = record.customer_first_name || '';
        const lastName = record.customer_last_name || '';
        const fullName = `${firstName} ${lastName}`.trim();
        return fullName || 'Bilinmeyen Müşteri';
    },

    /**
     * Get status badge configuration
     * @param {number} status - Status value
     * @returns {Object} Badge configuration
     */
    getStatusBadge: (status) => {
        return status === 1 
            ? { class: 'badge-success', icon: 'fa-check', text: 'Ödendi' }
            : { class: 'badge-warning', icon: 'fa-clock', text: 'Bekliyor' };
    }
};

// ============================================================================
// DATA PROCESSING SERVICE
// ============================================================================

const CommissionDataService = {
    /**
     * Process commission data and calculate summary statistics
     * @param {CommissionRecord[]} commissionData - Raw commission data
     * @returns {Object} Processed summary data
     */
    processSummaryData: (commissionData) => {
        let totalEarnings = 0;
        let totalCommission = 0;
        let paidCommission = 0;
        let unpaidCommission = 0;
        let completedCount = 0;
        let pendingCount = 0;

        commissionData.forEach(record => {
            // Calculate total earnings from items
            const itemsTotal = CommissionUtils.calculateItemsTotal(record.items, 'service_price');
            totalEarnings += itemsTotal;
            
            // Add commission amounts
            const commissionAmount = parseFloat(record.total_commission || 0);
            totalCommission += commissionAmount;
            
            // Categorize by status
            if (record.status === 1) {
                paidCommission += commissionAmount;
                completedCount++;
            } else {
                unpaidCommission += commissionAmount;
                pendingCount++;
            }
        });

        return {
            totalEarnings,
            totalCommission,
            paidCommission,
            unpaidCommission,
            completedCount,
            pendingCount
        };
    },

    /**
     * Validate commission data structure
     * @param {any} data - Data to validate
     * @returns {boolean} Is valid
     */
    validateData: (data) => {
        return Array.isArray(data) && data.every(record => 
            record && 
            typeof record.sale_id === 'number' &&
            typeof record.employee_id === 'number' &&
            record.total_commission !== undefined &&
            Array.isArray(record.items)
        );
    }
};

// ============================================================================
// UI RENDERER SERVICE
// ============================================================================

const CommissionUIRenderer = {
    /**
     * Render summary cards
     * @param {Object} summaryData - Summary statistics
     */
    renderSummaryCards: (summaryData) => {
        const cards = [
            {
                label: 'Toplam Kazanç',
                value: CommissionUtils.formatCurrency(summaryData.totalEarnings),
                icon: 'fa-money-bill-wave'
            },
            {
                label: 'Toplam Prim',
                value: CommissionUtils.formatCurrency(summaryData.totalCommission),
                icon: 'fa-percentage'
            },
            {
                label: 'Ödenen Prim',
                value: CommissionUtils.formatCurrency(summaryData.paidCommission),
                icon: 'fa-check-circle'
            },
            {
                label: 'Ödenecek Prim',
                value: CommissionUtils.formatCurrency(summaryData.unpaidCommission),
                icon: 'fa-clock'
            }
        ];

        const cardsHTML = cards.map(card => `
            <div class="summary-card">
                <div class="summary-card-icon">
                    <i class="fal ${card.icon}"></i>
                </div>
                <div class="summary-card-value">${card.value}</div>
                <div class="summary-card-label">${card.label}</div>
            </div>
        `).join('');

        document.getElementById('summaryCards').innerHTML = cardsHTML;
    },

    /**
     * Render commission table
     * @param {CommissionRecord[]} commissionData - Commission data
     */
    renderCommissionTable: (commissionData) => {
        if (!commissionData.length) {
            document.getElementById('historyTableContainer').innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fal fa-inbox"></i>
                    </div>
                    <h3>Prim Bilgisi Bulunamadı</h3>
                    <p>Bu çalışan için henüz prim bilgisi bulunmamaktadır.</p>
                </div>`;
            return;
        }

        const tableRows = commissionData.map(record => {
            const customerName = CommissionUtils.getCustomerName(record);
            const services = record.items.map(item => item.service_name).join(', ');
            const commissionRate = CommissionUtils.calculateCommissionRate(record.total_commission, record.items);
            const statusBadge = CommissionUtils.getStatusBadge(record.status);

            return `
                <tr>
                    <td data-label="">
                        ${record.status === 0 ? 
                            `<input type="checkbox" class="modern-checkbox commission-checkbox" 
                                value="${record.sale_id}" 
                                data-commission="${record.total_commission}"
                                data-reservation="${record.reservation_id}">` : 
                            ''
                        }
                    </td>
                    <td data-label="Tarih">
                        <span class="badge-modern badge-primary">
                            <i class="fal fa-calendar-alt"></i> 
                            ${CommissionUtils.formatDate(record.sale_date)}
                        </span>
                    </td>
                    <td data-label="Müşteri">
                        <span class="badge-modern badge-gray">
                            <i class="fal fa-user"></i> 
                            ${customerName}
                        </span>
                    </td>
                    <td data-label="Hizmetler">
                        <div class="service-items-list">
                            ${record.items.map(item => `
                                <div class="service-item-modern">
                                    <div>
                                        <div class="service-name">${item.service_name}</div>
                                        <div class="service-details">
                                            ${CommissionUtils.formatCurrency(item.service_price)} 
                                            (${item.quantity || 0} adet)
                                        </div>
                                    </div>
                                    <div class="service-price">${CommissionUtils.formatCurrency(item.unit_price)}</div>
                                </div>
                            `).join('')}
                        </div>
                    </td>
                    <td data-label="Prim Oranı">
                        <span class="badge-modern badge-primary">
                            <i class="fal fa-percentage"></i> 
                            ${commissionRate.toFixed(1)}%
                        </span>
                    </td>
                    <td data-label="Prim Tutarı">
                        <span class="badge-modern badge-warning">
                            <i class="fal fa-money-bill-wave"></i> 
                            ${CommissionUtils.formatCurrency(record.total_commission)}
                        </span>
                    </td>
                    <td data-label="Durum">
                        <span class="badge-modern ${statusBadge.class}">
                            <i class="fal ${statusBadge.icon}"></i>
                            ${statusBadge.text}
                        </span>
                    </td>
                </tr>
            `;
        }).join('');

        const tableHTML = `
            <table class="modern-table">
                <thead>
                    <tr>
                        <th style="width: 50px;"></th>
                        <th><i class='fal fa-calendar-alt'></i> Tarih</th>
                        <th><i class='fal fa-user'></i> Müşteri</th>
                        <th><i class='fal fa-concierge-bell'></i> Hizmetler</th>
                        <th><i class='fal fa-percentage'></i> Prim Oranı</th>
                        <th><i class='fal fa-money-bill-wave'></i> Prim Tutarı</th>
                        <th><i class='fal fa-badge-check'></i> Durum</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>
            </table>
        `;

        document.getElementById('historyTableContainer').innerHTML = tableHTML;
    },

    /**
     * Render footer badges
     * @param {Object} summaryData - Summary statistics
     */
    renderFooterBadges: (summaryData) => {
        const badgesHTML = `
            <div class="footer-badge success">
                <i class="fal fa-check"></i>
                Ödenen: ${summaryData.completedCount}
            </div>
            <div class="footer-badge warning">
                <i class="fal fa-clock"></i>
                Bekleyen: ${summaryData.pendingCount}
            </div>
        `;

        document.getElementById('footerBadges').innerHTML = badgesHTML;
    },

    /**
     * Show loading state
     */
    showLoading: () => {
        document.getElementById('summaryCards').innerHTML = `
            <div class="loading-skeleton" style="height: 100px; border-radius: var(--radius-lg);"></div>
            <div class="loading-skeleton" style="height: 100px; border-radius: var(--radius-lg);"></div>
            <div class="loading-skeleton" style="height: 100px; border-radius: var(--radius-lg);"></div>
            <div class="loading-skeleton" style="height: 100px; border-radius: var(--radius-lg);"></div>
        `;
        
        document.getElementById('historyTableContainer').innerHTML = `
            <div class="loading-skeleton" style="height: 300px; border-radius: var(--radius-lg);"></div>
        `;
    },

    /**
     * Show error state
     * @param {string} message - Error message
     */
    showError: (message = 'Prim geçmişi yüklenirken bir hata oluştu. Lütfen tekrar deneyin.') => {
        document.getElementById('historyTableContainer').innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fal fa-exclamation-triangle"></i>
                </div>
                <h3>Hata Oluştu</h3>
                <p>${message}</p>
            </div>
        `;
    }
};

// ============================================================================
// API SERVICE
// ============================================================================

const CommissionAPIService = {
    /**
     * Fetch employee commission history
     * @param {number} employeeId - Employee ID
     * @returns {Promise<CommissionRecord[]>} Commission data
     */
    fetchEmployeeHistory: async (employeeId) => {
        try {
            const response = await fetch(`/ajax/getEmployeeHistoryModal/${employeeId}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            
            // Validate data structure
            if (!CommissionDataService.validateData(data)) {
                throw new Error('Invalid data structure received from server');
            }
            
            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }
};

// ============================================================================
// PAYMENT SERVICE
// ============================================================================

const CommissionPaymentService = {
    /**
     * Get selected commissions for payment
     * @returns {Array} Selected commission data
     */
    getSelectedCommissions: () => {
        return Array.from(document.querySelectorAll('.commission-checkbox:checked')).map(cb => ({
            saleId: cb.value,
            reservationId: cb.getAttribute('data-reservation'),
            commissionAmount: cb.getAttribute('data-commission')
        }));
    },

    /**
     * Prepare commission IDs for form submission
     * @param {Array} selectedCommissions - Selected commission data
     */
    prepareCommissionIds: (selectedCommissions) => {
        const container = document.getElementById('commissionIdsContainer');
        container.innerHTML = '';
        
        selectedCommissions.forEach(commission => {

            alert(commission.saleId);
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'commission_ids[]';
            input.value = commission.saleId;
            container.appendChild(input);
        });
    },

    /**
     * Validate payment form
     * @param {number} employeeId - Employee ID
     * @param {Array} selectedCommissions - Selected commission data
     * @returns {boolean} Is valid
     */
    validatePaymentForm: (employeeId, selectedCommissions) => {
        if (!employeeId) {
            alert('Çalışan ID bulunamadı! Lütfen modal\'ı kapatıp tekrar açın.');
            return false;
        }
        
        if (!selectedCommissions.length) {
            alert('Lütfen ödeme yapılacak commission(lar)ı seçin!');
            return false;
        }

        return true;
    }
};

// ============================================================================
// MAIN CONTROLLER
// ============================================================================

const EmployeeCommissionController = {
    // State management
    state: {
        currentEmployeeId: null,
        currentCommissionData: null,
        currentEmployeeName: null
    },

    /**
     * Initialize the controller
     */
    init: () => {
        EmployeeCommissionController.bindEvents();
    },

    /**
     * Bind event listeners
     */
    bindEvents: () => {
        // Form submission
        $(document).off('submit', '#commissionPaymentForm').on('submit', '#commissionPaymentForm', function(e) {
            e.preventDefault();
            EmployeeCommissionController.handlePaymentSubmission(this);
        });

        // Checkbox change events
        $(document).on('change', '.commission-checkbox', function() {
            EmployeeCommissionController.updatePaymentButton();
        });
    },

    /**
     * Open employee history modal
     * @param {number} employeeId - Employee ID
     */
    openModal: async (employeeId) => {
        console.log('Opening modal for Employee ID:', employeeId);
        
        try {
            // Show loading state
            CommissionUIRenderer.showLoading();
            
            // Fetch data
            const commissionData = await CommissionAPIService.fetchEmployeeHistory(employeeId);
            
            // Update state
            EmployeeCommissionController.state.currentEmployeeId = employeeId;
            EmployeeCommissionController.state.currentCommissionData = commissionData;
            EmployeeCommissionController.state.currentEmployeeName = `Çalışan #${employeeId}`;
            
            // Render modal
            EmployeeCommissionController.renderModal();
            
            // Show modal
            $('#employeeHistoryModal').modal('show');
            
        } catch (error) {
            console.error('Modal opening error:', error);
            CommissionUIRenderer.showError();
            alert('Prim geçmişi yüklenirken hata oluştu!');
        }
    },

    /**
     * Render the complete modal
     */
    renderModal: () => {
        const { currentCommissionData, currentEmployeeName } = EmployeeCommissionController.state;
        
        // Process data
        const summaryData = CommissionDataService.processSummaryData(currentCommissionData);
        
        // Render UI components
        CommissionUIRenderer.renderSummaryCards(summaryData);
        CommissionUIRenderer.renderCommissionTable(currentCommissionData);
        CommissionUIRenderer.renderFooterBadges(summaryData);
        
        // Update employee name
        document.getElementById('employeeName').textContent = currentEmployeeName;
        
        // Update payment button
        EmployeeCommissionController.updatePaymentButton();
    },

    /**
     * Handle payment form submission
     * @param {HTMLFormElement} form - Form element
     */
    handlePaymentSubmission: (form) => {
        const employeeId = EmployeeCommissionController.state.currentEmployeeId;
        const selectedCommissions = CommissionPaymentService.getSelectedCommissions();
        
        console.log('Payment submission - Employee ID:', employeeId);
        console.log('Selected commissions:', selectedCommissions);
        
        // Validate form
        if (!CommissionPaymentService.validatePaymentForm(employeeId, selectedCommissions)) {
            return false;
        }
        
        // Prepare form data
        CommissionPaymentService.prepareCommissionIds(selectedCommissions);
        
        // Submit form
        form.submit();
    },

    /**
     * Update payment button state
     */
    updatePaymentButton: () => {
        const selectedCommissions = CommissionPaymentService.getSelectedCommissions();
        const paymentButton = document.getElementById('addCommission');
        
        if (selectedCommissions.length > 0) {
            paymentButton.disabled = false;
            paymentButton.innerHTML = `
                <i class="fal fa-credit-card"></i>
                Ödeme Yap (${selectedCommissions.length})
            `;
        } else {
            paymentButton.disabled = true;
            paymentButton.innerHTML = `
                <i class="fal fa-credit-card"></i>
                Ödeme Yap
            `;
        }
    }
};

// ============================================================================
// LEGACY COMPATIBILITY FUNCTIONS
// ============================================================================

// Keep legacy function for backward compatibility
function renderEmployeeHistoryModal(employeeName, commissionData, employeeId) {
    EmployeeCommissionController.state.currentEmployeeId = employeeId;
    EmployeeCommissionController.state.currentCommissionData = commissionData;
    EmployeeCommissionController.state.currentEmployeeName = employeeName;
    EmployeeCommissionController.renderModal();
}

// Keep legacy function for backward compatibility
function openEmployeeHistoryModal(employeeId) {
    EmployeeCommissionController.openModal(employeeId);
}

// Keep legacy helper functions for backward compatibility
function getSelectedCommissions() {
    return CommissionPaymentService.getSelectedCommissions();
}

function getCsrfToken() {
    const csrfMetaTag = document.querySelector('meta[name="csrf-token"]');
    return csrfMetaTag ? csrfMetaTag.getAttribute('content') : null;
}

function getEmployeeIdFromModal() {
    return EmployeeCommissionController.state.currentEmployeeId;
}

// ============================================================================
// INITIALIZATION
// ============================================================================

// Initialize when DOM is ready
$(document).ready(() => {
    EmployeeCommissionController.init();
});

// Export for global access if needed
window.EmployeeCommissionController = EmployeeCommissionController;
window.CommissionUtils = CommissionUtils;
window.CommissionDataService = CommissionDataService;
</script> 