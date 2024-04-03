<div>
    <div class="text-center mb-2">
        <h1 class="mb-1">Invoice Payments</h1>
    </div>
    @if ($invoicePayments)
    <div class="card-table table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Amount</th>
                    <th>Conversion Rate</th>
                    <th>Remaining Amount</th>
                    <th>Bank</th>
                    <th>Bank Charges</th>
                    <th>Bank Description</th>
                    <th>Payment Date</th>
                    <th>PDF</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoicePayments->invoicePayments as $payment)
                    <tr>
                        <td>{{ formatCurrency($payment->amount, $invoicePayments->currency) }}</td>
                        <td>{{ $payment->conversion_rate }}</td>
                        <td>{{ formatCurrency($payment->remaining_amount, $invoicePayments->currency) }}</td>
                        <td>{{ $payment->bank }}</td>
                        <td>{{ formatCurrency($payment->bank_charges, $invoicePayments->currency) }}</td>
                        <td>{{ $payment->description }}</td>
                        <td>{{ formatDate($payment->billed_at) }}</td>
                        <td><a href="{{ asset($payment->file)}}" target="_blank"><i data-feather="arrow-down"></i> </a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@script
    <script type="module">
        $(document).ready(function () {
            Livewire.dispatch('feather-icons');
            Livewire.on('reinitialize-icons', () => {
                $(document).ready(function () {
                    Livewire.dispatch('feather-icons');
                });
            });
        });
    </script>
@endscript
