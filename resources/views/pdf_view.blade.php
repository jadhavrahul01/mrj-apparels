<!DOCTYPE html>
<html>
<head>
    <title>Order Details - PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h1 {
            color: #333;
        }

        p {
            margin-bottom: 10px;
        }

    </style>
</head>
<body>
    <h1>Order Details</h1>

    <!-- Access order data -->
    <p><strong>Customer Name:</strong> {{ $order->cname }}</p>
    <p><strong>Customer Address:</strong> {{ $order->cadd }}</p>
    <p><strong>GSTIN:</strong> {{ $order->cgstin }}</p>
    @if ($order->mtaker)
    @foreach (json_decode($order->mtaker, true) as $mtaker)
    <p><strong>Measurement Taker:</strong> {{ $mtaker['mtaker'] }}</p>
    <p><strong>Date:</strong> {{ $mtaker['mtaker_date'] }}</p>
    @endforeach
    @endif
    <p><strong>Email:</strong> {{ $order->email }}</p>
    <p><strong>Phone:</strong> {{ $order->phone }}</p>

    <!-- Nullable email fields -->
    @if (!empty($order->email2))
    <p><strong>Email 2:</strong> {{ $order->email2 }}</p>
    @endif

    @if (!empty($order->email3))
    <p><strong>Email 3:</strong> {{ $order->email3 }}</p>
    @endif

    @if (!empty($order->email4))
    <p><strong>Email 4:</strong> {{ $order->email4 }}</p>
    @endif

    @if (!empty($order->email5))
    <p><strong>Email 5:</strong> {{ $order->email5 }}</p>
    @endif

    <!-- Nullable phone fields -->
    @if (!empty($order->phone2))
    <p><strong>Phone 2:</strong> {{ $order->phone2 }}</p>
    @endif

    @if (!empty($order->phone3))
    <p><strong>Phone 3:</strong> {{ $order->phone3 }}</p>
    @endif

    @if (!empty($order->phone4))
    <p><strong>Phone 4:</strong> {{ $order->phone4 }}</p>
    @endif

    @if (!empty($order->phone5))
    <p><strong>Phone 5:</strong> {{ $order->phone5 }}</p>
    @endif


</body>
</html>
