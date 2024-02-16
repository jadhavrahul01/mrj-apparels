<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Search Results</h1>

        @foreach ($results as $result)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Table: {{ $result->table }}</h5>
                @if (isset($result->name))
                <p class="card-text">Name: {{ $result->name }}</p>
                @endif
                @if (isset($result->email))
                <p class="card-text">Email: {{ $result->email }}</p>
                @endif
                @if (isset($result->cname))
                <p class="card-text">Customer Name: {{ $result->cname }}</p>
                @endif
                @if (isset($result->cadd))
                <p class="card-text">Customer Address: {{ $result->cadd }}</p>
                @endif
                @if (isset($result->cgstin))
                <p class="card-text">Customer GSTIN: {{ $result->cgstin }}</p>
                @endif
                @if (isset($result->cstyle_ref))
                <p class="card-text">Style Reference: {{ $result->cstyle_ref }}</p>
                @endif
                @if (isset($result->phone))
                <p class="card-text">Phone: {{ $result->phone }}</p>
                @endif
                @if (isset($result->tokenNo))
                <p class="card-text">Token Number: {{ $result->tokenNo }}</p>
                @endif
                @if (isset($result->sname))
                <p class="card-text">Short Name: {{ $result->sname }}</p>
                @endif
                @if (isset($result->fullName))
                <p class="card-text">Full Name: {{ $result->fullName }}</p>
                @endif
                @if (isset($result->category))
                <p class="card-text">Category: {{ $result->category }}</p>
                @endif
                @if (isset($result->setOrder))
                <p class="card-text">Set Order: {{ $result->setOrder }}</p>
                @endif
                @if (isset($result->status))
                <p class="card-text">Status: {{ $result->status }}</p>
                @endif
                @if (isset($result->description))
                <p class="card-text">Description: {{ $result->description }}</p>
                @endif
                @if (isset($result->title))
                <p class="card-text">Title: {{ $result->title }}</p>
                @endif
                @if (isset($result->notification_title))
                <p class="card-text">Notification Title: {{ $result->notification_title }}</p>
                @endif
                @if (isset($result->notification_type))
                <p class="card-text">Notification Type: {{ $result->notification_type }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Include Bootstrap JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
