<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PO-Support | Print</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    <style>
        .float-print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
        }
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 4px; }
        th { background: #eee; }
        .text-right { text-align: right; }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Floating Print Button -->
        <button type="button" class="btn btn-primary float-print-btn" onclick="window.print()">
            <i class="fas fa-print"></i>
        </button>

        <!-- Main content -->
        <section class="invoice">
            <!-- title row -->
            <div class="row">
                <div class="col-12">
                    <h2 class="page-header">
                        PT Arkananta Apta Pratista
                        {{-- <h5 class="float-right">Date: 2/10/2014</h5> --}}
                    </h2>
                </div>
                <!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    Lampiran PO
                    <address>
                        <strong>No. {{ $po->po_no }}</strong><br>
                        Date: {{ date('d F Y', strtotime($po->date)) }}<br>
                        Vendor: {{ optional($vendor)->vendor_name ?? (optional($vendor)->name ?? 'n/a') }}<br>
                        Project: {{ $po->project_code }}<br>
                    </address>
                </div>
            </div>
            <!-- /.row -->

            <!-- Table row -->
            <div class="row">
                <div class="col-12 table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Code</th>
                                <th>Description</th>
                                <th class="text-right">Qty</th>
                                <th>UoM</th>
                                <th class="text-right">Price (IDR)</th>
                                <th class="text-right">Subtotal (IDR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($item_services->count())
                                @foreach ($item_services as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->item_code }}</td>
                                        <td>{{ $item->item_desc }}</td>
                                        <td class="text-right">{{ $item->qty }}</td>
                                        <td>{{ $item->uom }}</td>
                                        <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-right">{{ number_format($item->qty * $item->unit_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">No Data Found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <div class="row">
                <!-- remarks column -->
                <div class="col-6">
                    <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                        Remarks : <b>{{ $po->remarks }}</b>
                    </p>
                </div>
                <!-- /.col -->
                <div class="col-6">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th style="width:50%">Subtotal:</th>
                                <td>{{ number_format($item_services->sum(function($item) { return $item->qty * $item->unit_price; }), 2) }}</td>
                            </tr>
                            <tr>
                                <th>Tax (11 %)</th>
                                <td>{{ $po->is_vat ? number_format($item_services->sum(function($item) { return $item->qty * $item->unit_price; }) * 0.11, 2) : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Total:</th>
                                <td>
                                    {{ $po->is_vat
                                        ? number_format($item_services->sum(function($item) { return $item->qty * $item->unit_price; }) * 1.11, 2)
                                        : number_format($item_services->sum(function($item) { return $item->qty * $item->unit_price; }), 2) }}
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0">
                            <tr>
                                <td></td>
                                <td></td>
                                <td width="50%">
                                    <div class="text-center">
                                        <p>Balikpapan, {{ date('d F Y', strtotime(now())) }}<br>
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            Christina Linawati
                                            <br>Procument Manager
                                        </p>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- ./wrapper -->
    <script>
        // Print functionality is handled by the button
        // This media query hides the button when printing
    </script>
    <style>
        @media print {
            .float-print-btn {
                display: none !important;
            }
        }
    </style>
</body>

</html>
