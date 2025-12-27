<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Enterprise Reports Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all configuration options for the advanced reporting
    | system. These settings control performance, caching, validation, and
    | various enterprise features.
    |
    */

    'version' => '2.0.0',
    'environment' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    */
    
    'performance' => [
        'cache_ttl' => env('REPORTS_CACHE_TTL', 300), // 5 minutes
        'max_records' => env('REPORTS_MAX_RECORDS', 10000),
        'query_timeout' => env('REPORTS_QUERY_TIMEOUT', 30), // seconds
        'pagination_size' => env('REPORTS_PAGINATION_SIZE', 1000),
        'enable_query_optimization' => env('REPORTS_OPTIMIZE_QUERIES', true),
        'enable_parallel_processing' => env('REPORTS_PARALLEL_PROCESSING', false),
        'memory_limit' => env('REPORTS_MEMORY_LIMIT', '512M'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching Configuration
    |--------------------------------------------------------------------------
    */
    
    'cache' => [
        'enabled' => env('REPORTS_CACHE_ENABLED', true),
        'driver' => env('REPORTS_CACHE_DRIVER', 'redis'),
        'prefix' => env('REPORTS_CACHE_PREFIX', 'reports_'),
        'tags' => [
            'services' => 'services_cache',
            'customers' => 'customers_cache',
            'employees' => 'employees_cache',
            'reservations' => 'reservations_cache',
            'financial' => 'financial_cache',
            'inventory' => 'inventory_cache',
        ],
        'invalidation_rules' => [
            'on_data_change' => true,
            'scheduled_cleanup' => '0 2 * * *', // Daily at 2 AM
            'max_age' => 86400, // 24 hours
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security & Validation
    |--------------------------------------------------------------------------
    */
    
    'security' => [
        'enable_input_validation' => true,
        'enable_sql_injection_protection' => true,
        'enable_rate_limiting' => true,
        'rate_limit_per_minute' => 60,
        'enable_audit_logging' => true,
        'sensitive_fields' => ['password', 'credit_card', 'ssn'],
        'allowed_export_formats' => ['pdf', 'excel', 'csv'],
        'max_date_range_days' => 365,
    ],

    /*
    |--------------------------------------------------------------------------
    | Business Rules
    |--------------------------------------------------------------------------
    */
    
    'business_rules' => [
        'min_data_requirements' => [
            'reservations' => 1,
            'customers' => 1,
            'employees' => 1,
            'services' => 1,
        ],
        'data_quality_checks' => [
            'orphaned_records' => true,
            'data_consistency' => true,
            'required_fields' => true,
        ],
        'working_hours' => [
            'start' => '09:00',
            'end' => '18:00',
            'timezone' => 'Europe/Istanbul',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Report Types Configuration
    |--------------------------------------------------------------------------
    */
    
    'report_types' => [
        'services-daily' => [
            'title' => 'Günlük Hizmet Raporu',
            'icon' => 'fal fa-calendar-day',
            'color' => 'primary',
            'supports_date_filter' => true,
            'default_period' => 30,
            'cache_duration' => 300,
            'business_rules' => ['has_reservations', 'has_services'],
            'chart_types' => ['line', 'bar'],
            'export_formats' => ['pdf', 'excel', 'csv'],
        ],
        'services-monthly' => [
            'title' => 'Aylık Hizmet Raporu',
            'icon' => 'fal fa-calendar',
            'color' => 'info',
            'supports_date_filter' => true,
            'default_period' => 365,
            'cache_duration' => 600,
            'business_rules' => ['has_reservations', 'has_services'],
            'chart_types' => ['bar', 'doughnut'],
            'export_formats' => ['pdf', 'excel'],
        ],
        'services-popular' => [
            'title' => 'En Çok Tercih Edilen Hizmetler',
            'icon' => 'fal fa-star',
            'color' => 'warning',
            'supports_date_filter' => true,
            'default_period' => 90,
            'cache_duration' => 900,
            'business_rules' => ['has_services'],
            'chart_types' => ['doughnut', 'bar'],
            'export_formats' => ['pdf', 'excel', 'csv'],
        ],
        'staff-performance' => [
            'title' => 'Personel Performans Raporu',
            'icon' => 'fal fa-user-chart',
            'color' => 'success',
            'supports_date_filter' => true,
            'default_period' => 30,
            'cache_duration' => 300,
            'business_rules' => ['has_employees'],
            'chart_types' => ['bar', 'radar'],
            'export_formats' => ['pdf', 'excel'],
        ],
        'financial-daily' => [
            'title' => 'Günlük Finansal Rapor',
            'icon' => 'fal fa-chart-line',
            'color' => 'success',
            'supports_date_filter' => true,
            'default_period' => 30,
            'cache_duration' => 180,
            'business_rules' => ['has_financial_data'],
            'chart_types' => ['line', 'area'],
            'export_formats' => ['pdf', 'excel'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard Configuration
    |--------------------------------------------------------------------------
    */
    
    'dashboard' => [
        'auto_refresh_interval' => 300000, // 5 minutes in milliseconds
        'default_widgets' => [
            'total_reservations',
            'total_customers', 
            'total_employees',
            'system_health',
        ],
        'quick_date_filters' => ['today', 'week', 'month', 'year'],
        'default_date_range' => 30, // days
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Settings
    |--------------------------------------------------------------------------
    */
    
    'export' => [
        'pdf' => [
            'enabled' => true,
            'engine' => 'dompdf', // dompdf, wkhtmltopdf, tcpdf
            'paper_size' => 'A4',
            'orientation' => 'landscape',
            'margins' => [10, 10, 10, 10],
            'fonts' => ['DejaVu Sans', 'Arial', 'Times New Roman'],
            'include_logo' => true,
            'include_watermark' => false,
        ],
        'excel' => [
            'enabled' => true,
            'engine' => 'phpspreadsheet',
            'format' => 'xlsx',
            'include_charts' => true,
            'auto_filter' => true,
            'freeze_header' => true,
        ],
        'csv' => [
            'enabled' => true,
            'delimiter' => ',',
            'enclosure' => '"',
            'encoding' => 'UTF-8',
            'include_bom' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization
    |--------------------------------------------------------------------------
    */
    
    'localization' => [
        'default_locale' => 'tr_TR',
        'currency' => 'TRY',
        'currency_symbol' => '₺',
        'date_format' => 'd.m.Y',
        'datetime_format' => 'd.m.Y H:i',
        'number_format' => [
            'decimals' => 2,
            'decimal_separator' => ',',
            'thousands_separator' => '.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Advanced Features
    |--------------------------------------------------------------------------
    */
    
    'features' => [
        'scheduled_reports' => env('REPORTS_SCHEDULED_ENABLED', false),
        'report_templates' => env('REPORTS_TEMPLATES_ENABLED', false),
        'custom_report_builder' => env('REPORTS_BUILDER_ENABLED', false),
        'data_visualization' => env('REPORTS_CHARTS_ENABLED', true),
        'real_time_updates' => env('REPORTS_REALTIME_ENABLED', false),
        'ai_insights' => env('REPORTS_AI_ENABLED', false),
        'predictive_analytics' => env('REPORTS_PREDICTIONS_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | System Health Monitoring
    |--------------------------------------------------------------------------
    */
    
    'monitoring' => [
        'enabled' => true,
        'checks' => [
            'database_connection' => true,
            'cache_availability' => true,
            'data_integrity' => true,
            'query_performance' => true,
            'memory_usage' => true,
            'disk_space' => true,
        ],
        'thresholds' => [
            'slow_query_time' => 5.0, // seconds
            'memory_warning' => 80, // percentage
            'disk_warning' => 85, // percentage
            'cache_hit_ratio' => 70, // percentage
        ],
        'notifications' => [
            'enabled' => true,
            'channels' => ['email', 'slack'],
            'recipients' => [
                'admin@example.com',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    */
    
    'api' => [
        'enabled' => env('REPORTS_API_ENABLED', true),
        'version' => 'v1',
        'rate_limiting' => [
            'enabled' => true,
            'max_requests_per_minute' => 100,
            'max_requests_per_hour' => 1000,
        ],
        'authentication' => [
            'required' => true,
            'methods' => ['api_key', 'jwt'],
        ],
        'pagination' => [
            'default_per_page' => 50,
            'max_per_page' => 1000,
        ],
    ],

]; 