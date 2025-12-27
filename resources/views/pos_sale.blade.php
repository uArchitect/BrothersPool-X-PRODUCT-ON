<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS - Sipariş Masanda</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        :root {
            /* Professional Color System */
            --primary: #1e40af; /* Deep Blue - Professional, trustworthy */
            --primary-dark: #1e3a8a; /* Dark Blue - Headers, emphasis */
            --primary-light: #3b82f6; /* Bright Blue - Interactive elements */
            --primary-50: #eff6ff; /* Very light blue - Backgrounds */
            --primary-100: #dbeafe; /* Light blue - Subtle backgrounds */
            
            /* Status Colors - Clear State Distinction */
            --success: #059669; /* Green - Available, completed, success */
            --success-light: #10b981; /* Light green - Hover states */
            --success-dark: #047857; /* Dark green - Active states */
            --success-50: #ecfdf5; /* Very light green - Success backgrounds */
            
            --warning: #d97706; /* Amber - Occupied, pending, caution */
            --warning-light: #f59e0b; /* Light amber - Warning highlights */
            --warning-dark: #b45309; /* Dark amber - Strong warnings */
            --warning-50: #fffbeb; /* Very light amber - Warning backgrounds */
            
            --danger: #dc2626; /* Red - Error, cancelled, critical */
            --danger-light: #ef4444; /* Light red - Error highlights */
            --danger-dark: #b91c1c; /* Dark red - Critical states */
            --danger-50: #fef2f2; /* Very light red - Error backgrounds */
            
            --info: #0284c7; /* Sky blue - Information, neutral actions */
            --info-light: #0ea5e9; /* Light sky blue - Info highlights */
            --info-dark: #0369a1; /* Dark sky blue - Info emphasis */
            --info-50: #f0f9ff; /* Very light sky blue - Info backgrounds */
            
            /* Neutral Colors - Professional Grays */
            --gray-50: #f8fafc; /* Almost white - Main backgrounds */
            --gray-100: #f1f5f9; /* Very light gray - Card backgrounds */
            --gray-200: #e2e8f0; /* Light gray - Borders, dividers */
            --gray-300: #cbd5e1; /* Medium light gray - Disabled elements */
            --gray-400: #94a3b8; /* Medium gray - Placeholder text */
            --gray-500: #64748b; /* Medium dark gray - Secondary text */
            --gray-600: #475569; /* Dark gray - Primary text */
            --gray-700: #334155; /* Darker gray - Headers, emphasis */
            --gray-800: #1e293b; /* Very dark gray - Dark mode text */
            --gray-900: #0f172a; /* Almost black - High contrast text */
            
            /* Semantic Colors for Tables */
            --table-available: var(--success); /* Green for available tables */
            --table-occupied: var(--warning); /* Amber for occupied tables */
            --table-reserved: var(--info); /* Blue for reserved tables */
            --table-maintenance: var(--gray-500); /* Gray for maintenance */
            
            /* Order Status Colors */
            --order-pending: var(--warning); /* Amber for pending orders */
            --order-confirmed: var(--info); /* Blue for confirmed orders */
            --order-preparing: var(--primary); /* Primary blue for preparing */
            --order-ready: var(--success); /* Green for ready orders */
            --order-served: var(--gray-600); /* Gray for served orders */
            --order-cancelled: var(--danger); /* Red for cancelled orders */
            
            /* Spacing */
            --space-1: 0.25rem;
            --space-2: 0.5rem;
            --space-3: 0.75rem;
            --space-4: 1rem;
            --space-5: 1.25rem;
            --space-6: 1.5rem;
            --space-8: 2rem;
            --space-10: 2.5rem;
            --space-12: 3rem;
            --space-16: 4rem;
            
            /* Border Radius */
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --radius-2xl: 1.5rem;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            color: var(--gray-900);
            line-height: 1.5;
            overflow: hidden;
        }

        /* Main Container */
        .pos-container {
            display: flex;
            height: 100vh;
            background: white;
        }

        /* Left Panel - Products */
        .products-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
            border-right: 1px solid var(--gray-200);
        }

        /* Header */
        .pos-header {
            padding: var(--space-6);
            border-bottom: 1px solid var(--gray-200);
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pos-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: var(--space-3);
        }

        .pos-title i {
            color: var(--primary);
            font-size: 1.75rem;
        }

        /* Table Selector */
        .table-selector {
            display: flex;
            align-items: center;
            gap: var(--space-4);
        }

        .table-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: var(--space-3) var(--space-6);
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: var(--space-2);
        }

        .table-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .table-btn i {
            font-size: 1rem;
        }

        /* Categories */
        .categories-section {
            padding: var(--space-4) var(--space-6);
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
        }

        .categories-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-600);
            margin-bottom: var(--space-3);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .categories-grid {
            display: flex;
            gap: var(--space-2);
            overflow-x: auto;
            padding-bottom: var(--space-2);
        }

        .category-btn {
            background: white;
            border: 1px solid var(--gray-200);
            color: var(--gray-700);
            padding: var(--space-2) var(--space-4);
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .category-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary);
            color: white;
        }

        .category-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Products Grid */
        .products-section {
            flex: 1;
            padding: var(--space-6);
            overflow-y: auto;
            background: white;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: var(--space-4);
        }

        .product-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-xl);
            padding: var(--space-4);
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }

        .product-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .product-image {
            width: 100%;
            height: 120px;
            background: var(--gray-100);
            border-radius: var(--radius-lg);
            margin-bottom: var(--space-3);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: var(--radius-lg);
        }

        .placeholder-image {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--gray-400);
            font-size: 2rem;
        }

        .product-name {
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: var(--space-2);
            font-size: 0.875rem;
            line-height: 1.4;
        }

        .product-price {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--primary);
        }

        .product-category {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-top: var(--space-1);
        }

        /* Right Panel - Intelligent Sidebar */
        .cart-panel {
            width: 450px;
            background: var(--gray-900);
            display: flex;
            flex-direction: column;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .cart-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            z-index: 0;
        }

        .cart-panel > * {
            position: relative;
            z-index: 1;
        }

        /* Cart Header */
        .cart-header {
            padding: var(--space-6);
            border-bottom: 1px solid var(--gray-700);
            background: var(--gray-800);
        }

        .cart-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: var(--space-4);
            display: flex;
            align-items: center;
            gap: var(--space-2);
        }

        .cart-title i {
            color: var(--primary-light);
        }

        .table-info {
            background: var(--gray-700);
            padding: var(--space-3) var(--space-4);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            gap: var(--space-2);
            margin-bottom: var(--space-4);
        }

        .table-info i {
            color: var(--success);
        }

        .table-info span {
            font-weight: 500;
        }

        .table-status {
            display: flex;
            align-items: center;
            gap: var(--space-2);
            font-size: 0.875rem;
            color: var(--gray-300);
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Cart Items */
        .cart-items {
            flex: 1;
            padding: var(--space-4);
            overflow-y: auto;
        }

        .cart-item {
            background: var(--gray-800);
            border-radius: var(--radius-lg);
            padding: var(--space-4);
            margin-bottom: var(--space-3);
            display: flex;
            align-items: center;
            gap: var(--space-3);
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-name {
            font-weight: 600;
            margin-bottom: var(--space-1);
            font-size: 0.875rem;
        }

        .cart-item-price {
            color: var(--gray-400);
            font-size: 0.75rem;
        }

        .cart-item-controls {
            display: flex;
            align-items: center;
            gap: var(--space-2);
        }

        .quantity-btn {
            background: var(--gray-700);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .quantity-btn:hover {
            background: var(--gray-600);
        }

        .quantity-display {
            background: var(--gray-700);
            color: white;
            padding: var(--space-1) var(--space-3);
            border-radius: var(--radius-md);
            font-weight: 600;
            min-width: 40px;
            text-align: center;
            font-size: 0.875rem;
        }

        .remove-btn {
            background: var(--danger);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .remove-btn:hover {
            background: #dc2626;
        }

        /* Empty State */
        .empty-cart {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 200px;
            color: var(--gray-400);
            text-align: center;
        }

        .empty-cart i {
            font-size: 3rem;
            margin-bottom: var(--space-3);
        }

        .empty-cart h3 {
            font-size: 1.125rem;
            margin-bottom: var(--space-2);
        }

        .empty-cart p {
            font-size: 0.875rem;
        }

        /* Cart Footer */
        .cart-footer {
            padding: var(--space-6);
            border-top: 1px solid var(--gray-700);
            background: var(--gray-800);
        }

        .cart-totals {
            margin-bottom: var(--space-6);
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: var(--space-2);
            font-size: 0.875rem;
        }

        .total-row.final {
            font-size: 1.125rem;
            font-weight: 700;
            padding-top: var(--space-3);
            border-top: 1px solid var(--gray-700);
            margin-top: var(--space-3);
        }

        .checkout-btn {
            width: 100%;
            background: var(--success);
            color: white;
            border: none;
            padding: var(--space-4);
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-2);
        }

        .checkout-btn:hover:not(:disabled) {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
        }

        .checkout-btn:disabled {
            background: var(--gray-600);
            cursor: not-allowed;
            transform: none;
        }

        /* Scrollbars */
        .products-section::-webkit-scrollbar,
        .cart-items::-webkit-scrollbar {
            width: 6px;
        }

        .products-section::-webkit-scrollbar-track,
        .cart-items::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: var(--radius-sm);
        }

        .products-section::-webkit-scrollbar-thumb,
        .cart-items::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: var(--radius-sm);
        }

        .products-section::-webkit-scrollbar-thumb:hover,
        .cart-items::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }

        .cart-items::-webkit-scrollbar-track {
            background: var(--gray-800);
        }

        .cart-items::-webkit-scrollbar-thumb {
            background: var(--gray-600);
        }

        .cart-items::-webkit-scrollbar-thumb:hover {
            background: var(--gray-500);
        }

        /* Loading States */
        .loading {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--space-8);
            color: var(--gray-500);
        }

        .spinner {
            width: 24px;
            height: 24px;
            border: 2px solid var(--gray-300);
            border-top: 2px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: var(--space-2);
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .cart-panel {
                width: 350px;
            }
        }

        @media (max-width: 768px) {
            .pos-container {
                flex-direction: column;
            }
            
            .cart-panel {
                width: 100%;
                height: 50vh;
                order: -1;
            }
            
            .products-panel {
                height: 50vh;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: var(--space-3);
            }
        }

        @media (max-width: 480px) {
            .pos-header {
                padding: var(--space-4);
            }
            
            .pos-title {
                font-size: 1.25rem;
            }
            
            .products-section {
                padding: var(--space-4);
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
            
            .cart-header,
            .cart-footer {
                padding: var(--space-4);
            }
        }

        /* Table Card Status Styles */
        .table-card {
            transition: all 0.3s ease;
            border: 2px solid var(--gray-200);
            cursor: pointer;
        }

        .table-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .table-card.available {
            border-color: var(--table-available);
            background: var(--success-50);
        }

        .table-card.available:hover {
            border-color: var(--success-dark);
            background: var(--success-100);
        }

        .table-card.occupied {
            border-color: var(--table-occupied);
            background: var(--warning-50);
        }

        .table-card.occupied:hover {
            border-color: var(--warning-dark);
            background: var(--warning-100);
        }

        .table-card.reserved {
            border-color: var(--table-reserved);
            background: var(--info-50);
        }

        .table-card.reserved:hover {
            border-color: var(--info-dark);
            background: var(--info-100);
        }

        .table-card.maintenance {
            border-color: var(--table-maintenance);
            background: var(--gray-100);
            opacity: 0.6;
        }

        .table-card.maintenance:hover {
            opacity: 0.8;
        }

        /* Table Status Indicators */
        .table-status-indicator {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
        }

        .table-status-indicator.available {
            background: var(--table-available);
        }

        .table-status-indicator.occupied {
            background: var(--table-occupied);
        }

        .table-status-indicator.reserved {
            background: var(--table-reserved);
        }

        .table-status-indicator.maintenance {
            background: var(--table-maintenance);
        }

        /* Order Status Badges */
        .order-status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .order-status-badge.pending {
            background: var(--warning-50);
            color: var(--warning-dark);
            border: 1px solid var(--warning-200);
        }

        .order-status-badge.confirmed {
            background: var(--info-50);
            color: var(--info-dark);
            border: 1px solid var(--info-200);
        }

        .order-status-badge.preparing {
            background: var(--primary-50);
            color: var(--primary-dark);
            border: 1px solid var(--primary-200);
        }

        .order-status-badge.ready {
            background: var(--success-50);
            color: var(--success-dark);
            border: 1px solid var(--success-200);
        }

        .order-status-badge.served {
            background: var(--gray-100);
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
        }

        .order-status-badge.cancelled {
            background: var(--danger-50);
            color: var(--danger-dark);
            border: 1px solid var(--danger-200);
        }

        /* Intelligent Sidebar Components */
        .sidebar-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-lg);
            margin: var(--space-3);
            padding: var(--space-4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .sidebar-section-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--primary-light);
            margin-bottom: var(--space-3);
            display: flex;
            align-items: center;
            gap: var(--space-2);
        }

        .sidebar-section-title i {
            font-size: 1rem;
        }

        .order-summary-card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-md);
            padding: var(--space-3);
            margin-bottom: var(--space-2);
            border-left: 3px solid var(--primary);
            transition: all 0.3s ease;
        }

        .order-summary-card:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(2px);
        }

        .order-summary-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--space-2);
        }

        .order-number {
            font-weight: 600;
            color: white;
            font-size: 0.875rem;
        }

        .order-time {
            font-size: 0.75rem;
            color: var(--gray-400);
        }

        .order-items-preview {
            font-size: 0.75rem;
            color: var(--gray-300);
            margin-bottom: var(--space-2);
        }

        .order-total {
            font-weight: 700;
            color: var(--success);
            font-size: 1rem;
        }

        .payment-section {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: var(--space-2);
            margin-bottom: var(--space-4);
        }

        .payment-method-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: var(--space-3);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .payment-method-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--primary);
        }

        .payment-method-btn.active {
            background: var(--primary);
            border-color: var(--primary);
        }

        .payment-amount-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: var(--space-3);
            border-radius: var(--radius-md);
            width: 100%;
            font-size: 1.125rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: var(--space-3);
        }

        .payment-amount-input::placeholder {
            color: var(--gray-400);
        }

        .payment-amount-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .change-amount {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
            padding: var(--space-2) var(--space-3);
            border-radius: var(--radius-md);
            font-weight: 600;
            text-align: center;
            margin-bottom: var(--space-3);
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-2);
        }

        .action-btn {
            padding: var(--space-3);
            border-radius: var(--radius-md);
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .action-btn.primary {
            background: var(--primary);
            color: white;
        }

        .action-btn.primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .action-btn.secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .action-btn.secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .action-btn.danger {
            background: var(--danger);
            color: white;
        }

        .action-btn.danger:hover {
            background: var(--danger-dark);
        }

        .table-status-indicator {
            display: inline-flex;
            align-items: center;
            gap: var(--space-1);
            padding: var(--space-1) var(--space-2);
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .table-status-indicator.available {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
        }

        .table-status-indicator.occupied {
            background: rgba(217, 119, 6, 0.2);
            color: var(--warning);
        }

        .table-status-indicator.reserved {
            background: rgba(2, 132, 199, 0.2);
            color: var(--info);
        }

        .sales-summary {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .sales-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: var(--space-2);
        }

        .sales-stat {
            text-align: center;
            padding: var(--space-2);
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-md);
        }

        .sales-stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--success);
        }

        .sales-stat-label {
            font-size: 0.75rem;
            color: var(--gray-400);
            margin-top: var(--space-1);
        }
    </style>
</head>

<body>
    <div class="pos-container">
        <!-- Products Panel -->
        <div class="products-panel">
            <!-- Header -->
            <div class="pos-header">
                <div class="pos-title">
                    <i class='bx bx-store'></i>
                    POS Sistemi
                </div>
                <div class="table-selector">
                    <button class="table-btn" onclick="openTableModal()">
                        <i class='bx bx-table'></i>
                        <span id="selectedTableText">Masa Seç</span>
                    </button>
                    <button class="table-btn" id="viewOrdersBtn" onclick="viewTableOrders()" style="display: none;">
                        <i class='bx bx-receipt'></i>
                        <span>Siparişleri Gör</span>
                    </button>
                    <button class="table-btn" id="clearTableBtn" onclick="clearTable()" style="display: none;">
                        <i class='bx bx-x'></i>
                        <span>Masayı Boşalt</span>
                    </button>
                </div>
            </div>

            <!-- Categories -->
            <div class="categories-section">
                <div class="categories-title">Kategoriler</div>
                <div class="categories-grid" id="categoriesContainer">
                    <!-- Categories will be loaded dynamically from database -->
                </div>
            </div>

            <!-- Products -->
            <div class="products-section" id="productsSection">
                <div class="loading">
                    <div class="spinner"></div>
                    Ürünler yükleniyor...
                            </div>
            </div>
        </div>

        <!-- Intelligent Sidebar Panel -->
        <div class="cart-panel">
            <!-- Sidebar Header -->
            <div class="cart-header">
                <div class="cart-title">
                    <i class='bx bx-shopping-cart'></i>
                    <span id="sidebarTitle">Masa Seçin</span>
                </div>
                <div class="table-info" id="tableInfo">
                    <i class='bx bx-table'></i>
                    <span>Masa seçilmedi</span>
                </div>
                <div class="table-status" id="tableStatus" style="display: none;">
                    <div class="status-indicator"></div>
                    <span id="statusText">Müsait</span>
                </div>
            </div>

            <!-- Intelligent Sidebar Content -->
            <div class="cart-items" id="sidebarContent">
                <!-- Default State - No Table Selected -->
                <div id="noTableSelected" class="empty-cart">
                    <i class='bx bx-table'></i>
                    <h3>Masa Seçin</h3>
                    <p>Başlamak için bir masa seçin</p>
                </div>

                <!-- New Order State -->
                <div id="newOrderState" style="display: none;">
                    <!-- Current Cart Items -->
                    <div class="sidebar-section">
                        <div class="sidebar-section-title">
                            <i class='bx bx-shopping-bag'></i>
                            Sepet İçeriği
                        </div>
                        <div id="cartItemsList">
                            <!-- Cart items will be rendered here -->
                        </div>
                    </div>

                    <!-- Order Totals -->
                    <div class="sidebar-section">
                        <div class="sidebar-section-title">
                            <i class='bx bx-calculator'></i>
                            Sipariş Özeti
                        </div>
                        <div class="cart-totals">
                            <div class="total-row">
                                <span>Ara Toplam:</span>
                                <span id="subtotal">₺0.00</span>
                            </div>
                            <div class="total-row">
                                <span>KDV (%18):</span>
                                <span id="tax">₺0.00</span>
                            </div>
                            <div class="total-row final">
                                <span>Toplam:</span>
                                <span id="total">₺0.00</span>
                            </div>
                        </div>
                        <button class="checkout-btn" id="checkoutBtn" onclick="checkout()" disabled>
                            <i class='bx bx-credit-card'></i>
                            Siparişi Tamamla
                        </button>
                    </div>
                </div>

                <!-- Active Orders State -->
                <div id="activeOrdersState" style="display: none;">
                    <!-- Active Orders List -->
                    <div class="sidebar-section">
                        <div class="sidebar-section-title">
                            <i class='bx bx-receipt'></i>
                            Aktif Siparişler
                        </div>
                        <div id="activeOrdersList">
                            <!-- Active orders will be rendered here -->
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div class="sidebar-section payment-section">
                        <div class="sidebar-section-title">
                            <i class='bx bx-credit-card'></i>
                            Ödeme İşlemi
                        </div>
                        
                        <!-- Payment Methods -->
                        <div class="payment-methods" id="paymentMethods">
                            <button class="payment-method-btn" data-method="cash">
                                <i class='bx bx-money'></i>
                                Nakit
                            </button>
                            <button class="payment-method-btn" data-method="card">
                                <i class='bx bx-credit-card'></i>
                                Kart
                            </button>
                            <button class="payment-method-btn" data-method="online">
                                <i class='bx bx-wifi'></i>
                                Online
                            </button>
                            <button class="payment-method-btn" data-method="wallet">
                                <i class='bx bx-wallet'></i>
                                Cüzdan
                            </button>
                        </div>

                        <!-- Payment Amount Input -->
                        <input type="number" 
                               class="payment-amount-input" 
                               id="paymentAmount" 
                               placeholder="Ödeme Tutarı"
                               step="0.01"
                               min="0"
                               value="">

                        <!-- Change Amount Display -->
                        <div class="change-amount" id="changeAmount" style="display: none;">
                            Para Üstü: <span id="changeValue">₺0.00</span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button class="action-btn primary" id="processPaymentBtn" onclick="processPayment()">
                                <i class='bx bx-check'></i>
                                Ödemeyi Tamamla
                            </button>
                            <button class="action-btn secondary" onclick="viewOrderDetails()">
                                <i class='bx bx-detail'></i>
                                Detayları Gör
                            </button>
                        </div>
                    </div>

                    <!-- Sales Summary -->
                    <div class="sidebar-section sales-summary">
                        <div class="sidebar-section-title">
                            <i class='bx bx-trending-up'></i>
                            Günlük Satış Özeti
                        </div>
                        <div class="sales-stats" id="salesStats">
                            <div class="sales-stat">
                                <div class="sales-stat-value" id="totalSales">₺0.00</div>
                                <div class="sales-stat-label">Toplam Satış</div>
                            </div>
                            <div class="sales-stat">
                                <div class="sales-stat-value" id="totalOrders">0</div>
                                <div class="sales-stat-label">Sipariş Sayısı</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Selection Modal -->
    <div class="modal fade" id="tableModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Masa Seç</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="tablesContainer">
                        <!-- Tables will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Masa Siparişleri</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderModalBody">
                    <!-- Order details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-warning" onclick="clearTable()">Masayı Boşalt</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Configuration
        const POS_CONFIG = {
            TAX_RATE: 0.18,
            API_BASE: '/orders/api',
            CURRENCY: '₺'
        };

        // State Management
        class POSState {
            constructor() {
                this.state = {
                    selectedTable: null,
                    cart: [],
                    currentCategory: 'all',
                    products: [],
                    categories: [],
                    isLoading: false,
                    error: null,
                    activeOrders: [],
                    salesSummary: null,
                    selectedPaymentMethod: 'cash',
                    paymentAmount: 0,
                    sidebarState: 'noTable' // noTable, newOrder, activeOrders
                };
                this.listeners = [];
            }

            setState(newState) {
                this.state = { ...this.state, ...newState };
                this.notifyListeners();
            }

            getState() {
                return { ...this.state };
            }

            subscribe(listener) {
                this.listeners.push(listener);
                return () => {
                    const index = this.listeners.indexOf(listener);
                    if (index > -1) {
                        this.listeners.splice(index, 1);
                    }
                };
            }

            notifyListeners() {
                this.listeners.forEach(listener => listener(this.state));
            }
        }

        // Utils
        class Utils {
            static formatCurrency(amount) {
                return `${POS_CONFIG.CURRENCY}${parseFloat(amount).toFixed(2)}`;
            }

            static toNumber(value) {
                const num = parseFloat(value);
                return isNaN(num) ? 0 : num;
            }

            static debounce(func, wait) {
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

            static showError(message) {
                Swal.fire({
                    title: 'Hata',
                    text: message,
                    icon: 'error',
                    confirmButtonText: 'Tamam'
                });
            }

            static showSuccess(message) {
            Swal.fire({
                    title: 'Başarılı',
                    text: message,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            }

            static escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        }

        // API Service
        class APIService {
            static async request(url, options = {}) {
                const defaultOptions = {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                };

                const config = { ...defaultOptions, ...options };
                
                try {
                    const response = await fetch(url, config);
                    
                    if (!response.ok) {
                        let errorMessage = `HTTP error! status: ${response.status}`;
                        try {
                            const errorData = await response.json();
                            errorMessage = errorData.message || errorData.error || errorMessage;
                        } catch (e) {
                            const errorText = await response.text();
                            errorMessage = errorText || errorMessage;
                        }
                        throw new Error(errorMessage);
                    }
                    
                    return await response.json();
                } catch (error) {
                    console.error('API request failed:', error);
                    if (error.message.includes('Failed to fetch')) {
                        Utils.showError('Sunucuya bağlanılamıyor. Lütfen internet bağlantınızı kontrol edin.');
                    } else {
                        Utils.showError(`API isteği başarısız oldu: ${error.message}`);
                    }
                    throw error;
                }
            }

            static async getProducts() {
                return this.request('/api/products');
            }

            static async getCategories() {
                return this.request('/api/categories');
            }

            static async getTables() {
                return this.request('/api/tables');
            }

            static async createOrder(orderData) {
                return this.request('/orders/pos', {
                    method: 'POST',
                    body: JSON.stringify(orderData)
                });
            }

            static async getAllTableStatuses() {
                return this.request('/orders/api/tables/status');
            }

            static async getTableContext(tableId) {
                return this.request(`/orders/api/table/${tableId}/context`);
            }

            static async completeTableOrders(tableId) {
                return this.request(`/orders/api/table/${tableId}/complete`, {
                    method: 'POST'
                });
            }

            static async processPayment(paymentData) {
                return this.request('/payments/api/process', {
                    method: 'POST',
                    body: JSON.stringify(paymentData)
                });
            }

            static async getPaymentMethods() {
                return this.request('/payments/api/methods');
            }

            static async getTableSalesSummary(tableId) {
                return this.request(`/payments/api/table/${tableId}/sales`);
            }
        }

        // UI Components
        class UIComponents {
            static renderProducts(products, category = 'all') {
                const container = document.getElementById('productsSection');
                
                if (!products || products.length === 0) {
                    container.innerHTML = `
                <div class="loading">
                            <i class='bx bx-package'></i>
                            <span>Ürün bulunamadı</span>
                </div>
            `;
                    return;
                }

                const filteredProducts = category === 'all' 
                    ? products 
                    : products.filter(product => product.category === category);

                const productsHtml = filteredProducts.map(product => `
                    <div class="product-card" onclick="addToCart(${product.id}, '${product.name}', ${product.price})">
                        <div class="product-image">
                            ${product.image ? 
                                `<img src="${product.image}" alt="${product.name}">` :
                                `<div class="placeholder-image">
                                    <i class='bx bx-package'></i>
                                </div>`
                            }
                        </div>
                        <div class="product-name">${product.name}</div>
                        <div class="product-price">${Utils.formatCurrency(product.price)}</div>
                        <div class="product-category">${product.category_name || 'Genel'}</div>
                    </div>
                `).join('');

                container.innerHTML = `
                    <div class="products-grid">
                        ${productsHtml}
                        </div>
                    `;
            }

            static renderCart(cart) {
                const container = document.getElementById('cartItems');
                
                if (!cart || cart.length === 0) {
                    container.innerHTML = `
                        <div class="empty-cart">
                            <i class='bx bx-shopping-bag'></i>
                            <h3>Sepet Boş</h3>
                            <p>Ürün eklemek için ürünlere tıklayın</p>
                    </div>
                `;
                return;
            }

                const itemsHtml = cart.map(item => `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">${Utils.formatCurrency(item.price)} x ${item.quantity}</div>
                            </div>
                        <div class="cart-item-controls">
                            <button class="quantity-btn" onclick="updateQuantity(${item.id}, -1)">
                                <i class='bx bx-minus'></i>
                            </button>
                            <div class="quantity-display">${item.quantity}</div>
                            <button class="quantity-btn" onclick="updateQuantity(${item.id}, 1)">
                                <i class='bx bx-plus'></i>
                            </button>
                            <button class="remove-btn" onclick="removeFromCart(${item.id})">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>
                `).join('');

                container.innerHTML = itemsHtml;
            }

            static renderTableInfo(table) {
                const tableInfo = document.getElementById('tableInfo');
                const tableStatus = document.getElementById('tableStatus');
                const checkoutBtn = document.getElementById('checkoutBtn');

                if (!table) {
                    tableInfo.innerHTML = `
                        <i class='bx bx-table'></i>
                        <span>Masa seçilmedi</span>
                    `;
                    tableStatus.style.display = 'none';
                    checkoutBtn.disabled = true;
                    checkoutBtn.innerHTML = `
                        <i class='bx bx-table'></i>
                        Önce Masa Seçin
                    `;
                    return;
                }

                tableInfo.innerHTML = `
                    <i class='bx bx-table'></i>
                    <span>Masa ${table.number} - ${table.location}</span>
                `;
                tableStatus.style.display = 'flex';
                checkoutBtn.disabled = false;
                checkoutBtn.innerHTML = `
                    <i class='bx bx-credit-card'></i>
                    Siparişi Tamamla
                `;
            }

            static updateTotals(cart) {
                const subtotal = cart.reduce((sum, item) => sum + (Utils.toNumber(item.price) * item.quantity), 0);
                const tax = subtotal * POS_CONFIG.TAX_RATE;
                const total = subtotal + tax;

                document.getElementById('subtotal').textContent = Utils.formatCurrency(subtotal);
                document.getElementById('tax').textContent = Utils.formatCurrency(tax);
                document.getElementById('total').textContent = Utils.formatCurrency(total);
            }

            static renderTables(tables) {
                const container = document.getElementById('tablesContainer');
                console.log('Rendering tables:', tables);
                
                const tablesHtml = tables.map(table => {
                    console.log('Table object:', table);
                    const statusClass = table.status || 'available';
                    const statusText = {
                        'available': 'Müsait',
                        'occupied': 'Dolu',
                        'reserved': 'Rezerve',
                        'maintenance': 'Bakım'
                    }[statusClass] || 'Müsait';
                    
                    return `
                        <div class="col-md-3 col-sm-4 col-6 mb-3">
                            <div class="card h-100 table-card ${statusClass}" onclick="selectTable(${table.id}, '${table.table_number}', '${table.location}', ${table.capacity})">
                                <div class="card-body text-center position-relative">
                                    <div class="table-status-indicator ${statusClass}"></div>
                                    <i class='bx bx-table text-primary' style="font-size: 2rem;"></i>
                                    <h6 class="card-title mt-2">Masa ${table.table_number}</h6>
                                    <p class="card-text text-muted">${table.location}</p>
                                    <small class="text-muted">${table.capacity} kişi</small>
                                    <div class="mt-2">
                                        <span class="order-status-badge ${statusClass}">${statusText}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                container.innerHTML = tablesHtml;
            }

            static renderIntelligentSidebar(state) {
                const sidebarContent = document.getElementById('sidebarContent');
                const noTableSelected = document.getElementById('noTableSelected');
                const newOrderState = document.getElementById('newOrderState');
                const activeOrdersState = document.getElementById('activeOrdersState');

                // Hide all states first
                noTableSelected.style.display = 'none';
                newOrderState.style.display = 'none';
                activeOrdersState.style.display = 'none';

                if (state.sidebarState === 'noTable') {
                    noTableSelected.style.display = 'block';
                } else if (state.sidebarState === 'newOrder') {
                    newOrderState.style.display = 'block';
                    this.renderCartItems(state.cart);
                } else if (state.sidebarState === 'activeOrders') {
                    activeOrdersState.style.display = 'block';
                    this.renderActiveOrders(state.activeOrders);
                    this.renderSalesSummary(state.salesSummary);
                }
            }

            static renderCategories(categories) {
                const container = document.getElementById('categoriesContainer');
                
                if (!categories || categories.length === 0) {
                    container.innerHTML = `
                        <button class="category-btn active" data-category="all">Tümü</button>
                    `;
                    return;
                }

                // Add "All" button first
                let categoriesHtml = `<button class="category-btn active" data-category="all">Tümü</button>`;
                
                // Filter out orphaned categories (those with no menu items)
                // and prioritize subcategories over main categories
                const categoriesWithItems = categories.filter(category => {
                    // Check if this category has menu items by looking at the products
                    const state = window.posApp?.state?.getState();
                    if (state && state.products) {
                        return state.products.some(product => product.category_id === category.id);
                    }
                    return true; // If we can't check, include it
                });

                // Only show categories that have products (filter out empty main categories)
                const categoriesWithProducts = categoriesWithItems.filter(category => {
                    const state = window.posApp?.state?.getState();
                    if (state && state.products) {
                        return state.products.some(product => product.category_id === category.id);
                    }
                    return false; // If we can't check, don't include it
                });

                // Sort categories: subcategories first, then main categories
                const sortedCategories = categoriesWithProducts.sort((a, b) => {
                    // If both have parent_id, sort by name
                    if (a.parent_id && b.parent_id) {
                        return a.name.localeCompare(b.name);
                    }
                    // Subcategories (with parent_id) come first
                    if (a.parent_id && !b.parent_id) return -1;
                    if (!a.parent_id && b.parent_id) return 1;
                    // Then sort by name
                    return a.name.localeCompare(b.name);
                });
                
                // Add categories from database
                sortedCategories.forEach(category => {
                    const categorySlug = category.slug || category.name.toLowerCase().replace(/\s+/g, '_');
                    categoriesHtml += `
                        <button class="category-btn" data-category="${categorySlug}" data-category-id="${category.id}">
                            ${category.name}
                        </button>
                    `;
                });

                container.innerHTML = categoriesHtml;
            }

            static renderCartItems(cart) {
                const container = document.getElementById('cartItemsList');
                
                if (!cart || cart.length === 0) {
                    container.innerHTML = `
                        <div class="empty-cart">
                            <i class='bx bx-shopping-bag'></i>
                            <p>Sepet boş</p>
                        </div>
                    `;
                    return;
                }

                const itemsHtml = cart.map(item => `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">${Utils.formatCurrency(item.price)} x ${item.quantity}</div>
                        </div>
                        <div class="cart-item-controls">
                            <button class="quantity-btn" onclick="updateQuantity(${item.id}, -1)">
                                <i class='bx bx-minus'></i>
                            </button>
                            <div class="quantity-display">${item.quantity}</div>
                            <button class="quantity-btn" onclick="updateQuantity(${item.id}, 1)">
                                <i class='bx bx-plus'></i>
                            </button>
                            <button class="remove-btn" onclick="removeFromCart(${item.id})">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>
                `).join('');

                container.innerHTML = itemsHtml;
            }

            static renderActiveOrders(orders) {
                const container = document.getElementById('activeOrdersList');
                
                if (!orders || orders.length === 0) {
                    container.innerHTML = `
                        <div class="empty-cart">
                            <i class='bx bx-receipt'></i>
                            <p>Aktif sipariş yok</p>
                        </div>
                    `;
                    return;
                }

                const ordersHtml = orders.map(order => `
                    <div class="order-summary-card">
                        <div class="order-summary-header">
                            <div class="order-number">Sipariş #${order.order_number}</div>
                            <div class="order-time">${new Date(order.order_time).toLocaleString('tr-TR')}</div>
                        </div>
                        <div class="order-items-preview">
                            ${order.items.slice(0, 2).map(item => `${item.item_name} x${item.quantity}`).join(', ')}
                            ${order.items.length > 2 ? ` +${order.items.length - 2} daha` : ''}
                        </div>
                        <div class="order-total">${Utils.formatCurrency(order.total_amount)}</div>
                    </div>
                `).join('');

                container.innerHTML = ordersHtml;
                
                // Auto-set payment amount to total
                const totalAmount = orders.reduce((sum, order) => sum + parseFloat(order.total_amount), 0);
                const paymentAmountInput = document.getElementById('paymentAmount');
                if (paymentAmountInput) {
                    paymentAmountInput.value = totalAmount.toFixed(2);
                }
            }

            static renderSalesSummary(summary) {
                if (!summary) return;

                document.getElementById('totalSales').textContent = Utils.formatCurrency(summary.total_sales);
                document.getElementById('totalOrders').textContent = summary.total_orders;
            }
        }

        // Main Application
        class POSApplication {
            constructor() {
                this.state = new POSState();
                this.initialize();
            }

            async initialize() {
                console.log('POS System v3.0.0 initializing...');
                
                this.setupEventListeners();
                this.subscribeToStateChanges();
                
                try {
                    await this.loadData();
                } catch (error) {
                    console.error('Initialization failed:', error);
                    Utils.showError('Sistem başlatılamadı. Lütfen sayfayı yenileyin.');
                }
            }

            setupEventListeners() {
                // Category buttons
                document.addEventListener('click', (e) => {
                    if (e.target.classList.contains('category-btn')) {
                        this.handleCategoryChange(e.target);
                    }
                });

                // Table modal
                document.getElementById('tableModal').addEventListener('show.bs.modal', () => {
                    this.loadTables();
                });
            }

            subscribeToStateChanges() {
                this.state.subscribe((state) => {
                    this.handleStateChange(state);
                });
            }

            handleStateChange(state) {
                UIComponents.renderProducts(state.products, state.currentCategory);
                UIComponents.renderCategories(state.categories);
                UIComponents.renderIntelligentSidebar(state);
                UIComponents.renderTableInfo(state.selectedTable);
                UIComponents.updateTotals(state.cart);
            }

            async loadData() {
                this.state.setState({ isLoading: true });

                try {
                    const [products, categories] = await Promise.all([
                        APIService.getProducts(),
                        APIService.getCategories()
                    ]);

                    this.state.setState({
                        products: products.data || products,
                        categories: categories.data || categories,
                        isLoading: false
                    });
                } catch (error) {
                    this.state.setState({
                        error: error.message,
                        isLoading: false
                    });
                    Utils.showError('Veriler yüklenemedi');
                }
            }

            async loadTables() {
                try {
                    // Load both tables and their order statuses
                    const [tablesResponse, ordersResponse] = await Promise.all([
                        APIService.getTables(),
                        APIService.getAllTableStatuses()
                    ]);
                    
                    console.log('Tables response:', tablesResponse);
                    console.log('Orders response:', ordersResponse);
                    
                    const tables = tablesResponse.data || tablesResponse;
                    const tableStatuses = ordersResponse.data || ordersResponse;
                    
                    // Ensure tableStatuses is an array
                    if (!Array.isArray(tableStatuses)) {
                        console.error('tableStatuses is not an array:', tableStatuses);
                        throw new Error('Invalid table statuses data format');
                    }
                    
                    // Create a map of table statuses for quick lookup
                    const statusMap = new Map();
                    tableStatuses.forEach(status => {
                        statusMap.set(status.id, status.status);
                    });
                    
                    // Enhance tables with order status information
                    const enhancedTables = tables.map(table => ({
                        ...table,
                        status: statusMap.get(table.id) || 'available',
                        hasActiveOrders: statusMap.get(table.id) === 'occupied'
                    }));
                    
                    console.log('Enhanced tables data:', enhancedTables);
                    UIComponents.renderTables(enhancedTables);
                } catch (error) {
                    console.error('Error loading tables:', error);
                    Utils.showError('Masalar yüklenemedi');
                }
            }

            handleCategoryChange(button) {
                // Remove active class from all buttons
                document.querySelectorAll('.category-btn').forEach(btn => {
                    btn.classList.remove('active');
                });

                // Add active class to clicked button
                button.classList.add('active');

                // Update state
                const category = button.dataset.category;
                this.state.setState({ currentCategory: category });
            }

            addToCart(productId, productName, productPrice) {
                // Validate input
                if (!productId || !productName || !productPrice) {
                    console.error('Invalid product data:', { productId, productName, productPrice });
                    Utils.showError('Ürün bilgileri eksik');
                    return;
                }

                const currentCart = [...this.state.getState().cart]; // Create a copy
                const existingItem = currentCart.find(item => item.id === productId);

                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    currentCart.push({
                        id: productId,
                        name: productName,
                        price: parseFloat(productPrice),
                        quantity: 1
                    });
                }

                this.state.setState({ cart: currentCart });
                console.log('Cart updated:', currentCart);
            }

            updateQuantity(itemId, change) {
                // Validate input
                if (!itemId || typeof change !== 'number') {
                    console.error('Invalid quantity update data:', { itemId, change });
                    return;
                }

                const currentCart = [...this.state.getState().cart];
                const item = currentCart.find(cartItem => cartItem.id === itemId);

                if (item) {
                    const newQuantity = item.quantity + change;
                    
                    if (newQuantity <= 0) {
                        this.removeFromCart(itemId);
                    } else if (newQuantity > 99) {
                        Utils.showError('Maksimum miktar 99 adet');
                        return;
                    } else {
                        item.quantity = newQuantity;
                        this.state.setState({ cart: currentCart });
                        console.log('Quantity updated:', item);
                    }
                } else {
                    console.error('Item not found in cart:', itemId);
                }
            }

            removeFromCart(itemId) {
                const currentCart = this.state.getState().cart.filter(item => item.id !== itemId);
                this.state.setState({ cart: currentCart });
            }

            async selectTable(tableId, tableNumber, location, capacity) {
                const table = {
                    id: tableId,
                    number: tableNumber,
                    location: location,
                    capacity: capacity
                };

                this.state.setState({ selectedTable: table });

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('tableModal'));
                if (modal) modal.hide();

                // Load table context to check for active orders
                try {
                    const context = await APIService.getTableContext(tableId);
                    const salesSummary = await APIService.getTableSalesSummary(tableId);
                    
                    if (context.has_active_orders && context.active_orders.length > 0) {
                        // Table has active orders - show payment interface
                        this.state.setState({
                            sidebarState: 'activeOrders',
                            activeOrders: context.active_orders,
                            salesSummary: salesSummary.data?.summary || null
                        });
                        
                        // Update sidebar title
                        document.getElementById('sidebarTitle').textContent = `Masa ${tableNumber} - Aktif Siparişler`;
                        
                        // Show view orders button
                        document.getElementById('viewOrdersBtn').style.display = 'inline-flex';
                        document.getElementById('clearTableBtn').style.display = 'inline-flex';
                        
                        Utils.showSuccess(`Masa ${tableNumber} seçildi. ${context.active_orders.length} aktif sipariş var.`);
                    } else {
                        // Table is empty - show new order interface
                        this.state.setState({
                            sidebarState: 'newOrder',
                            cart: [],
                            activeOrders: [],
                            salesSummary: salesSummary.data?.summary || null
                        });
                        
                        // Update sidebar title
                        document.getElementById('sidebarTitle').textContent = `Masa ${tableNumber} - Yeni Sipariş`;
                        
                        // Hide view orders button
                        document.getElementById('viewOrdersBtn').style.display = 'none';
                        document.getElementById('clearTableBtn').style.display = 'none';
                        
                        Utils.showSuccess(`Masa ${tableNumber} seçildi. Yeni sipariş için ürün ekleyin.`);
                    }
                } catch (error) {
                    console.error('Error loading table context:', error);
                    // Default to new order state
                    this.state.setState({
                        sidebarState: 'newOrder',
                        cart: []
                    });
                    document.getElementById('sidebarTitle').textContent = `Masa ${tableNumber} - Yeni Sipariş`;
                    Utils.showSuccess(`Masa ${tableNumber} seçildi`);
                }
            }

            async checkout() {
                const state = this.state.getState();

                // Validation
                if (!state.selectedTable) {
                    Utils.showError('Önce bir masa seçmelisiniz');
                    return;
                }

                if (!state.cart || state.cart.length === 0) {
                    Utils.showError('Sepetinize ürün eklemelisiniz');
                    return;
                }

                // Validate cart items
                const invalidItems = state.cart.filter(item => 
                    !item.id || !item.name || !item.price || item.quantity <= 0
                );
                
                if (invalidItems.length > 0) {
                    Utils.showError('Sepetinizde geçersiz ürünler var. Lütfen sepeti temizleyin.');
                    return;
                }

                try {
                    const subtotal = state.cart.reduce((sum, item) => sum + (Utils.toNumber(item.price) * item.quantity), 0);
                    const tax = subtotal * POS_CONFIG.TAX_RATE;
                    const total = subtotal + tax;

                    // Validate calculations
                    if (subtotal <= 0 || total <= 0) {
                        Utils.showError('Sipariş tutarı geçersiz');
                        return;
                    }

                    const orderData = {
                        table_id: state.selectedTable.id,
                        table_number: state.selectedTable.number,
                        items: state.cart.map(item => ({
                            menu_item_id: parseInt(item.id),
                            quantity: parseInt(item.quantity),
                            unit_price: parseFloat(item.price)
                        })),
                        subtotal: parseFloat(subtotal.toFixed(2)),
                        tax_rate: POS_CONFIG.TAX_RATE * 100,
                        total: parseFloat(total.toFixed(2))
                    };

                    console.log('Creating order with data:', orderData);

                    const result = await APIService.createOrder(orderData);

                    if (result.success) {
                        Utils.showSuccess('Sipariş başarıyla oluşturuldu');
                        
                        // Switch to active orders state
                        this.state.setState({ 
                            cart: [],
                            sidebarState: 'activeOrders'
                        });
                        
                        // Load updated table context
                        const context = await APIService.getTableContext(state.selectedTable.id);
                        const salesSummary = await APIService.getTableSalesSummary(state.selectedTable.id);
                        
                        this.state.setState({
                            activeOrders: context.active_orders || [],
                            salesSummary: salesSummary.data?.summary || null
                        });
                        
                        // Update sidebar title
                        document.getElementById('sidebarTitle').textContent = `Masa ${state.selectedTable.number} - Aktif Siparişler`;
                        
                        // Show view orders button
                        document.getElementById('viewOrdersBtn').style.display = 'inline-flex';
                        document.getElementById('clearTableBtn').style.display = 'inline-flex';
                        
                        // Refresh table status
                        this.loadTables();
                    } else {
                        throw new Error(result.message || 'Sipariş oluşturulamadı');
                    }
                } catch (error) {
                    console.error('Checkout failed:', error);
                    Utils.showError(error.message || 'Sipariş oluşturulamadı');
                }
            }

            async viewTableOrders() {
                const state = this.state.getState();
                if (!state.selectedTable) {
                    Utils.showError('Önce bir masa seçmelisiniz');
                    return;
                }

                try {
                    const context = await APIService.getTableContext(state.selectedTable.id);
                    this.renderOrderModal(context, state.selectedTable);
                } catch (error) {
                    console.error('Error loading table orders:', error);
                    Utils.showError('Siparişler yüklenirken hata oluştu');
                }
            }

            renderOrderModal(context, table) {
                const modalBody = document.getElementById('orderModalBody');
                let ordersHtml = `<h4>Masa ${table.number} - ${table.location}</h4>`;

                if (context.has_active_orders && context.active_orders.length > 0) {
                    ordersHtml += `<div class="order-items-list">`;
                    context.active_orders.forEach(order => {
                        const statusClass = order.status || 'pending';
                        const statusText = {
                            'pending': 'Bekliyor',
                            'confirmed': 'Onaylandı',
                            'preparing': 'Hazırlanıyor',
                            'ready': 'Hazır',
                            'served': 'Servis Edildi',
                            'cancelled': 'İptal Edildi'
                        }[statusClass] || 'Bilinmiyor';

                        ordersHtml += `
                            <div class="order-summary mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Sipariş #${order.id}</h6>
                                    <span class="order-status-badge ${statusClass}">${statusText}</span>
                                </div>
                                <div class="order-summary-item">
                                    <span>Sipariş Zamanı:</span>
                                    <span>${new Date(order.order_time).toLocaleString('tr-TR')}</span>
                                </div>
                                <div class="order-summary-item">
                                    <span>Toplam Tutar:</span>
                                    <span class="fw-bold">${Utils.formatCurrency(order.total_amount)}</span>
                                </div>
                                <div class="order-items-list mt-2">
                                    ${order.items.map(item => `
                                        <div class="order-item-card">
                                            <div class="order-item-details">
                                                <div class="order-item-name">${Utils.escapeHtml(item.item_name)}</div>
                                                <div class="order-item-quantity-price">${item.quantity} x ${Utils.formatCurrency(item.unit_price)}</div>
                                            </div>
                                            <div class="order-item-total">${Utils.formatCurrency(item.quantity * item.unit_price)}</div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `;
                    });
                    ordersHtml += `</div>`;
                } else {
                    ordersHtml += `<p class="text-muted">Bu masada aktif sipariş bulunmamaktadır.</p>`;
                }

                modalBody.innerHTML = ordersHtml;
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('orderModal'));
                modal.show();
            }

            async clearTable() {
                const state = this.state.getState();
                if (!state.selectedTable) {
                    Utils.showError('Önce bir masa seçmelisiniz');
                    return;
                }

                const result = await Swal.fire({
                    title: 'Masayı Boşalt',
                    text: `Masa ${state.selectedTable.number} boşaltılacak. Emin misiniz?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Evet, Boşalt',
                    cancelButtonText: 'İptal',
                    confirmButtonColor: '#d97706'
                });

                if (result.isConfirmed) {
                    try {
                        const response = await APIService.completeTableOrders(state.selectedTable.id);
                        if (response.success) {
                            Utils.showSuccess(`Masa ${state.selectedTable.number} başarıyla boşaltıldı`);
                            
                            // Clear selected table and reset sidebar
                            this.state.setState({ 
                                selectedTable: null, 
                                cart: [],
                                sidebarState: 'noTable',
                                activeOrders: [],
                                salesSummary: null
                            });
                            
                            // Update sidebar title
                            document.getElementById('sidebarTitle').textContent = 'Masa Seçin';
                            
                            // Hide buttons
                            document.getElementById('viewOrdersBtn').style.display = 'none';
                            document.getElementById('clearTableBtn').style.display = 'none';
                            
                            // Refresh table status
                            this.loadTables();
                        } else {
                            throw new Error(response.message || 'Masa boşaltılamadı');
                        }
                    } catch (error) {
                        console.error('Error clearing table:', error);
                        Utils.showError(`Masa boşaltılırken hata oluştu: ${error.message}`);
                    }
                }
            }

            async processPayment() {
                const state = this.state.getState();
                if (!state.selectedTable || !state.activeOrders.length) {
                    Utils.showError('Önce bir masa ve aktif sipariş seçmelisiniz');
                    return;
                }

                const paymentAmount = parseFloat(document.getElementById('paymentAmount').value);
                if (!paymentAmount || paymentAmount <= 0) {
                    Utils.showError('Geçerli bir ödeme tutarı girin');
                    return;
                }

                // Calculate total amount for all active orders
                const totalAmount = state.activeOrders.reduce((sum, order) => sum + parseFloat(order.total_amount), 0);
                
                if (paymentAmount < totalAmount) {
                    Utils.showError('Ödeme tutarı sipariş tutarından az olamaz');
                    return;
                }

                try {
                    // Process payment for each active order
                    for (const order of state.activeOrders) {
                        const paymentData = {
                            order_id: order.id,
                            payment_method: state.selectedPaymentMethod,
                            amount: parseFloat(order.total_amount),
                            customer_id: null,
                            notes: `Masa ${state.selectedTable.number} ödemesi`
                        };

                        const result = await APIService.processPayment(paymentData);
                        if (!result.success) {
                            throw new Error(result.message || 'Ödeme işlenemedi');
                        }
                    }

                    // Show success message
                    const changeAmount = paymentAmount - totalAmount;
                    let successMessage = `Ödeme başarıyla tamamlandı! Toplam: ${Utils.formatCurrency(totalAmount)}`;
                    if (changeAmount > 0) {
                        successMessage += ` Para üstü: ${Utils.formatCurrency(changeAmount)}`;
                    }

                    Utils.showSuccess(successMessage);

                    // Clear table and reset state
                    await this.clearTable();

                } catch (error) {
                    console.error('Payment processing failed:', error);
                    Utils.showError(`Ödeme işlenirken hata oluştu: ${error.message}`);
                }
            }
        }

        // Global functions for HTML onclick handlers
        function addToCart(productId, productName, productPrice) {
            if (window.posApp) {
                window.posApp.addToCart(productId, productName, productPrice);
            }
        }

        function updateQuantity(itemId, change) {
            if (window.posApp) {
                window.posApp.updateQuantity(itemId, change);
            }
        }

        function removeFromCart(itemId) {
            if (window.posApp) {
                window.posApp.removeFromCart(itemId);
            }
        }

        function selectTable(tableId, tableNumber, location, capacity) {
            if (window.posApp) {
                window.posApp.selectTable(tableId, tableNumber, location, capacity);
            }
        }

        function checkout() {
            if (window.posApp) {
                window.posApp.checkout();
            }
        }

        function viewTableOrders() {
            if (window.posApp) {
                window.posApp.viewTableOrders();
            }
        }

        function clearTable() {
            if (window.posApp) {
                window.posApp.clearTable();
            }
        }

        function processPayment() {
            if (window.posApp) {
                window.posApp.processPayment();
            }
        }

        function viewOrderDetails() {
            if (window.posApp) {
                window.posApp.viewTableOrders();
            }
        }

        function openTableModal() {
            const modal = new bootstrap.Modal(document.getElementById('tableModal'));
            modal.show();
        }

        // Payment method selection
        document.addEventListener('DOMContentLoaded', function() {
            // Payment method selection
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('payment-method-btn')) {
                    // Remove active class from all buttons
                    document.querySelectorAll('.payment-method-btn').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    
                    // Add active class to clicked button
                    e.target.classList.add('active');
                    
                    // Update state
                    if (window.posApp) {
                        const method = e.target.dataset.method;
                        window.posApp.state.setState({ selectedPaymentMethod: method });
                    }
                }
            });

            // Payment amount input handling
            const paymentAmountInput = document.getElementById('paymentAmount');
            if (paymentAmountInput) {
                paymentAmountInput.addEventListener('input', function() {
                    if (window.posApp) {
                        const state = window.posApp.state.getState();
                        const amount = parseFloat(this.value) || 0;
                        window.posApp.state.setState({ paymentAmount: amount });
                        
                        // Calculate change
                        if (state.activeOrders && state.activeOrders.length > 0) {
                            const totalAmount = state.activeOrders.reduce((sum, order) => sum + parseFloat(order.total_amount), 0);
                            const change = amount - totalAmount;
                            
                            const changeAmount = document.getElementById('changeAmount');
                            const changeValue = document.getElementById('changeValue');
                            
                            if (change > 0) {
                                changeAmount.style.display = 'block';
                                changeValue.textContent = Utils.formatCurrency(change);
                            } else {
                                changeAmount.style.display = 'none';
                            }
                        }
                    }
                });
            }
        });

        // Initialize application
        document.addEventListener('DOMContentLoaded', function() {
            window.posApp = new POSApplication();
        });
    </script>
</body>
</html>