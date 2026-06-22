<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Print' }}</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: #fff;
            width: 100%;
            margin: 0;
            padding: 10px;
        }
        
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-only {
                display: block !important;
            }
            
            @page {
                margin: 0.5cm;
            }
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-left {
            text-align: left;
        }
        
        .font-bold {
            font-weight: bold;
        }
        
        .border {
            border: 1px solid #000;
        }
        
        .border-b {
            border-bottom: 1px solid #000;
        }
        
        .border-t {
            border-top: 1px solid #000;
        }
        
        .border-dashed {
            border-bottom: 1px dashed #000;
        }
        
        .mt-1 { margin-top: 4px; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }
        .mt-4 { margin-top: 16px; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mb-4 { margin-bottom: 16px; }
        .ml-1 { margin-left: 4px; }
        .ml-2 { margin-left: 8px; }
        .mr-1 { margin-right: 4px; }
        .mr-2 { margin-right: 8px; }
        
        .p-1 { padding: 4px; }
        .p-2 { padding: 8px; }
        .p-3 { padding: 12px; }
        .p-4 { padding: 16px; }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 5px 0;
        }
        
        td {
            padding: 3px 0;
        }
        
        .receipt {
            max-width: 80mm;
            margin: 0 auto;
            font-family: 'Courier New', monospace;
            font-size: 10px;
        }
        
        .invoice {
            max-width: 210mm;
            margin: 0 auto;
        }
        
        .barcode {
            text-align: center;
            font-family: 'Courier New', monospace;
        }
        
        .barcode svg {
            max-width: 100%;
            height: auto;
        }
        
        .label {
            width: 50mm;
            height: 30mm;
            border: 1px solid #000;
            padding: 5px;
            margin: 5px;
            float: left;
            page-break-inside: avoid;
        }
        
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    {{ $slot }}
</body>
</html>