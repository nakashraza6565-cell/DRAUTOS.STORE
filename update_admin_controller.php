<?php
$file = __DIR__ . '/drautos/app/Http/Controllers/AdminController.php';
$content = file_get_contents($file);

// 1. Change method signature to inject Request
$content = str_replace('public function index()', 'public function index(\Illuminate\Http\Request $request)', $content);

// 2. Add Start/End Date logic at the very beginning of the try block
$dateLogic = <<<'EOD'
        try {
            $startDate = $request->input('start_date', Carbon::today()->subDays(6)->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
            
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            
            $diffDays = $end->diffInDays($start);
            if ($diffDays > 31) { // Cap at 31 days max for charts
                $start = $end->copy()->subDays(31);
                $diffDays = 31;
                $startDate = $start->format('Y-m-d');
            }
EOD;
$content = str_replace("        try {\n", $dateLogic . "\n", $content);

// 3. Replace Carbon::today()->subDay(6)
$content = str_replace("Carbon::today()->subDay(6)", "\$start", $content);
$content = str_replace("Carbon::today()->subDays(6)", "\$start", $content);

// 4. Update the loops
// For sales trend chart loop:
$content = str_replace('for ($i = 6; $i >= 0; $i--) {', 'for ($i = $diffDays; $i >= 0; $i--) {', $content);

// For the date generation inside the loops
$content = preg_replace("/Carbon::today\(\)->subDays\(\\$i\)->format\('Y-m-d'\)/", "\$end->copy()->subDays(\$i)->format('Y-m-d')", $content);

// For the label generation (sales trend chart)
$content = preg_replace("/Carbon::today\(\)->subDays\(\\$i\)->format\('D'\)/", "(\$diffDays > 7 ? \$end->copy()->subDays(\$i)->format('M d') : \$end->copy()->subDays(\$i)->format('D'))", $content);

// 5. Update the loop for cash flow (it also uses $i = 6)
// Wait, both loops were updated by the previous replace since it replaces all occurrences.
// Let's verify if there were 2 loops. Yes.

// 6. We also need to add $startDate and $endDate to the compact() variables returned to the view so they can be retained.
// Actually, in the view we use `request('start_date')`, so we don't strictly need it in compact(), 
// but it's cleaner if we pass it, although request() works fine.

// Save changes
file_put_contents($file, $content);
echo "Updated AdminController.php successfully.\n";
