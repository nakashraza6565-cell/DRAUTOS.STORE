@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Packaging Usage History</h6>
    </div>
    <div class="card-body">
        <!-- Filter/Search -->
        <form method="GET" action="{{route('packaging.usage.index')}}" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="order_number" class="form-control" placeholder="Search by Order #..." value="{{request('order_number')}}">
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        <option value="sticker" {{request('type') == 'sticker' ? 'selected' : ''}}>Sticker</option>
                        <option value="box" {{request('type') == 'box' ? 'selected' : ''}}>Box</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{route('packaging.usage.index')}}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Date</th>
                        <th>Order #</th>
                        <th>Material</th>
                        <th>Type</th>
                        <th>Quantity Used</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usage as $record)
                    <tr>
                        <td>{{$record->date->format('d M Y, h:i A')}}</td>
                        <td>
                            <a href="{{$record->url}}" target="_blank">
                                {{$record->ref_no}}
                            </a>
                            <br>
                            <small class="text-muted">{{$record->source}}</small>
                        </td>
                        <td>{{$record->material}} ({{$record->size}})</td>
                        <td>
                            <span class="badge badge-{{$record->type == 'sticker' ? 'info' : 'warning'}}">
                                {{strtoupper($record->type)}}
                            </span>
                        </td>
                        <td>{{number_format($record->quantity, 2)}}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No usage records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="float-right mt-3">
                {{$usage->links()}}
            </div>
        </div>
    </div>
</div>
@endsection
