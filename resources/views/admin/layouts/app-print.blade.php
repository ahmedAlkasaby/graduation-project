<!DOCTYPE html>
<html lang="{{ $admin_language }}" dir="{{ $admin_dir }}" data-theme="theme-default"
    data-template="vertical-menu-template-no-customizer"
    class="{{ $admin_theme_style ?? 'light' }}-style layout-navbar-fixed layout-menu-fixed layout-compact">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>@yield('title')</title>

    @include('admin.layouts.meta')

    <!-- Print Specific Styles - iOS Compatible -->
    <style>
        /* ============================================
           BASE STYLES
           ============================================ */
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        html {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #fff !important;
            color: #000 !important;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Force black text everywhere */
        body, body *,
        p, span, div,
        h1, h2, h3, h4, h5, h6,
        td, th, tr,
        label, strong, b, em, i {
            color: #000 !important;
        }

        /* RTL Support */
        [dir="rtl"] body {
            text-align: right;
        }

        [dir="ltr"] body {
            text-align: left;
        }

        /* ============================================
           CONTENT WRAPPER
           ============================================ */
        .content-wrapper {
            padding: 20px;
            max-width: 900px;
            margin: 0 auto;
            background: #fff !important;
        }

        .container-fluid {
            padding: 0;
            width: 100%;
        }

        /* ============================================
           INVOICE CARD STYLES
           ============================================ */
        .card,
        .invoice-preview-card {
            background: #fff !important;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .card-body {
            padding: 20px;
        }

        /* ============================================
           TABLE STYLES
           ============================================ */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff !important;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            color: #000 !important;
            font-size: 13px;
        }

        th {
            background-color: #f5f5f5 !important;
            font-weight: 600;
        }

        /* RTL Table alignment */
        [dir="rtl"] th,
        [dir="rtl"] td {
            text-align: right;
        }

        [dir="ltr"] th,
        [dir="ltr"] td {
            text-align: left;
        }

        .table-responsive {
            overflow: visible;
        }

        /* ============================================
           TYPOGRAPHY
           ============================================ */
        h1, h2, h3, h4, h5, h6 {
            color: #000 !important;
            margin-top: 0;
            font-weight: 600;
        }

        p {
            margin: 0 0 8px 0;
            color: #000 !important;
        }

        strong, b {
            font-weight: 600;
            color: #000 !important;
        }

        /* ============================================
           HR STYLES
           ============================================ */
        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 15px 0;
        }

        /* ============================================
           UTILITY CLASSES
           ============================================ */
        .fw-bold, .fw-medium {
            font-weight: 600 !important;
        }

        .mb-0 { margin-bottom: 0 !important; }
        .mb-1 { margin-bottom: 4px !important; }
        .mb-2 { margin-bottom: 8px !important; }
        .mb-3 { margin-bottom: 12px !important; }
        .mb-4 { margin-bottom: 16px !important; }

        .mt-0 { margin-top: 0 !important; }
        .mt-1 { margin-top: 4px !important; }
        .mt-2 { margin-top: 8px !important; }
        .mt-3 { margin-top: 12px !important; }
        .mt-4 { margin-top: 16px !important; }

        .text-center { text-align: center !important; }

        [dir="rtl"] .text-end { text-align: left !important; }
        [dir="ltr"] .text-end { text-align: right !important; }

        [dir="rtl"] .text-start { text-align: right !important; }
        [dir="ltr"] .text-start { text-align: left !important; }

        /* ============================================
           PRINT MEDIA QUERY
           ============================================ */
        @media print {
            /* Reset for print */
            *, *::before, *::after {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
                -webkit-transform: none !important;
                transform: none !important;
                -webkit-animation: none !important;
                animation: none !important;
                -webkit-transition: none !important;
                transition: none !important;
                -webkit-box-shadow: none !important;
                box-shadow: none !important;
            }

            html, body {
                width: 100% !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                -webkit-print-color-adjust: exact !important;
            }

            /* Force black text */
            body, body *,
            p, span, div,
            h1, h2, h3, h4, h5, h6,
            td, th, tr,
            label, strong, b, em, i {
                color: #000 !important;
                -webkit-text-fill-color: #000 !important;
            }

            /* Hide elements not needed for print */
            .no-print,
            .layout-overlay,
            .drag-target,
            .footer,
            nav,
            button,
            .btn,
            script,
            .modal,
            .modal-backdrop {
                display: none !important;
                visibility: hidden !important;
            }

            /* Content wrapper */
            .content-wrapper {
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            .container-fluid {
                padding: 10px !important;
            }

            /* Invoice content visibility */
            #invoiceContent,
            #invoiceContent *,
            .invoice-preview-card,
            .invoice-preview-card * {
                visibility: visible !important;
            }

            #invoiceContent,
            .invoice-preview-card {
                position: relative !important;
                width: 100% !important;
                border: none !important;
                box-shadow: none !important;
                -webkit-box-shadow: none !important;
            }

            /* Card styles */
            .card,
            .invoice-preview-card {
                border: none !important;
                border-radius: 0 !important;
                background: #fff !important;
                page-break-inside: avoid !important;
            }

            .card-body {
                padding: 10px !important;
            }

            /* Table styles */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                -webkit-border-horizontal-spacing: 0 !important;
                -webkit-border-vertical-spacing: 0 !important;
                page-break-inside: auto !important;
            }

            thead {
                display: table-header-group !important;
            }

            tr {
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }

            th, td {
                padding: 8px !important;
                border: 1px solid #ccc !important;
                font-size: 11pt !important;
                color: #000 !important;
                -webkit-text-fill-color: #000 !important;
            }

            th {
                background-color: #f0f0f0 !important;
                -webkit-print-color-adjust: exact !important;
            }

            .table-responsive {
                overflow: visible !important;
                -webkit-overflow-scrolling: auto !important;
            }

            /* Images */
            img {
                max-width: 120px !important;
                height: auto !important;
                page-break-inside: avoid !important;
            }

            /* Typography */
            h1, h2, h3, h4, h5, h6 {
                page-break-after: avoid !important;
                color: #000 !important;
            }

            p {
                orphans: 3 !important;
                widows: 3 !important;
                color: #000 !important;
            }

            /* Links */
            a, a:visited {
                text-decoration: none !important;
                color: #000 !important;
            }

            a[href]::after {
                content: none !important;
            }

            /* Page settings */
            @page {
                size: A4;
                margin: 10mm;
            }

            /* Flexbox to block for iOS */
            .d-flex,
            .d-inline-flex,
            .flex-row,
            .flex-column {
                display: block !important;
            }

            .row {
                display: block !important;
            }

            [class*="col-"] {
                float: none !important;
                width: 100% !important;
                display: block !important;
            }
        }

        /* ============================================
           iOS SPECIFIC FIXES
           ============================================ */
        @supports (-webkit-touch-callout: none) {
            body {
                -webkit-text-size-adjust: 100%;
            }

            @media print {
                * {
                    -webkit-print-color-adjust: exact !important;
                }

                body {
                    -webkit-text-size-adjust: 100% !important;
                }

                .invoice-preview-card,
                .card,
                #invoiceContent {
                    -webkit-transform: translateZ(0) !important;
                    transform: translateZ(0) !important;
                }
            }
        }
    </style>

    @yield('after_head')
</head>

<body data-language="{{ $admin_language ?? 'en' }}">

    <div class="content-wrapper">
        <div class="container-fluid flex-grow-1 container-p-y">
            @yield('head_content')
            @yield('content')
        </div>
    </div>

    <!-- Hidden elements for print -->
    <div class="layout-overlay layout-menu-toggle no-print"></div>
    <div class="drag-target no-print"></div>

    <!-- Minimal Scripts -->
    <script>
        // iOS compatible print function
        (function() {
            var printTriggered = false;

            function triggerPrint() {
                if (printTriggered) return;
                printTriggered = true;

                // Small delay for iOS
                setTimeout(function() {
                    if (typeof window.print === 'function') {
                        try {
                            window.print();
                        } catch (e) {
                            console.log('Print error:', e);
                        }
                    }
                }, 800);
            }

            // Try multiple events for iOS compatibility
            if (document.readyState === 'complete') {
                triggerPrint();
            } else {
                window.addEventListener('load', triggerPrint);
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(triggerPrint, 1000);
                });
            }
        })();
    </script>

    @yield('after_foot')

</body>

</html>
