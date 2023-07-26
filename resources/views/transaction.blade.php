@extends('base')

@section('title', 'Transaction')

@section('header_title', 'Transaction')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="card-body table-responsive p-0">
            <table id="transactions" class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>No</th>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Amount</th>
                  <th>Transaction Type</th>
                  <th>Payment Method</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                  @foreach ($transactions as $transaction)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $transaction->id }}</td>
                    <td>{{ $transaction->user->name }}</td>
                    <td>Rp. {{ number_format($transaction->amount) }}</td>
                    <td>{{ $transaction->transactionType->code }}</td>
                    <td>{{ $transaction->paymentMethod->code }}</td>
                    <td>{{ $transaction->status }}</td>
                    <td>{{ $transaction->created_at }}</td>
                  </tr>
                  @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('js')
    <script>
      $('#transactions').DataTable();
    </script>
@endsection