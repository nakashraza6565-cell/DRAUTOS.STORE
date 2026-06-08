<?php
$file = __DIR__ . '/drautos/app/Http/Controllers/AdminController.php';
$content = file_get_contents($file);

$method = <<<'EOD'
    public function chartDetails(\Illuminate\Http\Request $request)
    {
        $date = $request->date;
        $type = $request->type;
        
        if ($type == 'cash_flow') {
            $inTransactions = \App\Models\AccountTransaction::with('account')
                ->whereDate('transaction_date', $date)
                ->where('type', 'in')
                ->get();
                
            $outTransactions = \App\Models\AccountTransaction::with('account')
                ->whereDate('transaction_date', $date)
                ->where('type', 'out')
                ->get();
                
            return view('backend.partials.chart_details', compact('inTransactions', 'outTransactions', 'date', 'type'));
        } elseif ($type == 'incoming_sales') {
            $incoming = \App\Models\InventoryIncoming::with(['supplier', 'items'])
                ->whereDate('received_date', $date)
                ->get();
                
            $sales = \App\Models\Order::with('user')
                ->whereDate('created_at', $date)
                ->get();
                
            return view('backend.partials.chart_details', compact('incoming', 'sales', 'date', 'type'));
        }
        
        return "Invalid chart type.";
    }

EOD;

$content = str_replace('public function whatsappSettings()', $method . "    public function whatsappSettings()", $content);
file_put_contents($file, $content);
echo "Added chartDetails method to AdminController.php\n";
