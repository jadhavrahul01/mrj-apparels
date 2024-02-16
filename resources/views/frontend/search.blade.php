@extends('frontend.includes.app')
@section('title', 'Search')

@section('content')
<div class="content-body">

    <div class="container-fluid">
        <div class="flex-wrap mb-sm-4 d-flex align-items-center text-head">
            <h2 class="mb-3 me-auto">Search</h2>

        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="table-responsive fs-14">
                    <table class="table mb-4 display dataTablesCard order-table shadow-hover card-table" id="searchTable">
                        <thead>
                            <tr class="text-center">
                                <th>Table Name</th>
                                <th>Field</th>
                                <th>Found Value</th>
                            </tr>
                        </thead>

                        <tbody class="text-center">
                            @foreach($results as $result)
                            @foreach($result->matchedFields as $field => $value)
                            <tr>
                                @if ($loop->first)
                                <td rowspan="{{ count($result->matchedFields) }}">{{ $result->table }}</td>
                                @endif
                                <td>{{ ucfirst($field) }}</td>
                                <td>
                                    @if($result->table == 'orders')
                                    <a href="{{ url('order-edit/' . $result->id) }}">
                                        {!! str_replace($searchTerm, '<span style="background-color: #FFFF00">' . $searchTerm . '</span>', $value) !!}
                                    </a>
                                    @else
                                    {!! str_replace($searchTerm, '<span style="background-color: #FFFF00">' . $searchTerm . '</span>', $value) !!}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customJs')
<script>


</script>
@endsection
