<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\File;

class WhatsAppService
{
    protected $instanceId;
    protected $accessToken;
    protected $baseUrl;

    const BAN_PREVENTION_GAP = 240; // 4 Minutes gap between different orders/customers

    public function __construct()
    {
        $this->instanceId  = config('whatsapp.instance_id') ?: env('WHATSAPP_INSTANCE_ID');
        $this->accessToken = config('whatsapp.access_token') ?: env('WHATSAPP_ACCESS_TOKEN');
        $this->baseUrl     = config('whatsapp.base_url', 'https://wa2.shnaveed.com/api');
    }

    /**
     * Get Global Scheduled Delay (Anti-Ban Logic)
     * Ensures 4 minutes gap between different message sets
     */
    protected function getGlobalGapDelay()
    {
        $now = time();
        $lastSentPath = storage_path('app/whatsapp_last_sent_time.txt');
        
        if (!File::exists(dirname($lastSentPath))) {
            File::makeDirectory(dirname($lastSentPath), 0755, true);
        }

        $lastTime = File::exists($lastSentPath) ? (int)File::get($lastSentPath) : 0;
        
        // If last message was long ago, start from now
        // Otherwise, schedule 4 minutes after the last one
        $scheduledTime = max($now, $lastTime + self::BAN_PREVENTION_GAP);
        
        File::put($lastSentPath, $scheduledTime);
        
        return $scheduledTime - $now;
    }

    /**
     * Send Order Notification (Text + PDF Combined in One Message)
     */
    public function sendOrderNotification($order)
    {
        $delay = $this->getGlobalGapDelay();
        $ordNum = $order->order_number;
        
        $message = "Assalam-o-Alaikum " . strtoupper($order->first_name) . ",\n\n" .
                   "Thank you for shopping with Dr Auto Store. Your order has been successfully confirmed.\n\n" .
                   "Order Number : {$ordNum}\n" .
                   "Order Date   : " . ($order->created_at ? $order->created_at->format('d M Y') : now()->format('d M Y')) . "\n" .
                   "Total Amount : Rs. " . number_format($order->total_amount, 2) . "\n\n" .
                   "Your invoice PDF is attached below for your records.\n\n" .
                   "Regards,\nDr Auto Store Management";

        try {
            // Generate PDF binary
            $pdfContent = PDF::loadView('backend.order.pdf', ['order' => $order])->output();
            
            // Send Combined Media Message (PDF + Text as Caption)
            $this->queueMessage(
                $order->phone, 
                'media', 
                $pdfContent, 
                "Invoice-{$ordNum}.pdf", 
                $message, // Full text as caption
                $delay
            );
        } catch (\Exception $e) {
            Log::error("WhatsApp Order Combined Message Error: " . $e->getMessage());
            // Fallback to text only if PDF fails
            $this->queueMessage($order->phone, 'text', $message, "", "", $delay);
        }

        return true;
    }

    /**
     * Send Purchase Order Notification to Supplier (Combined PDF + Text)
     */
    public function sendPurchaseOrderNotification($purchaseOrder)
    {
        $delay = $this->getGlobalGapDelay();
        $poNum = $purchaseOrder->po_number;
        $supplier = $purchaseOrder->supplier;
        
        if (!$supplier || !$supplier->phone) return false;

        $message = "Assalam-o-Alaikum " . strtoupper($supplier->name) . ",\n\n" .
                   "This is a formal Purchase Order from Dr Auto Store.\n\n" .
                   "PO Number    : {$poNum}\n" .
                   "Order Date   : " . ($purchaseOrder->order_date ? \Carbon\Carbon::parse($purchaseOrder->order_date)->format('d M Y') : now()->format('d M Y')) . "\n" .
                   "Total Amount : Rs. " . number_format($purchaseOrder->total_amount, 2) . "\n\n" .
                   "Please find the attached PO document for item details and fulfillment. Kindly confirm receipt.\n\n" .
                   "Regards,\nPurchase Department - Dr Auto Store";

        try {
            // Generate Purchase PDF
            $pdfContent = PDF::loadView('backend.purchase.pdf', ['purchase_order' => $purchaseOrder])->output();
            
            // Send Combined Media Message
            $this->queueMessage(
                $supplier->phone, 
                'media', 
                $pdfContent, 
                "PO-{$poNum}.pdf", 
                $message, // Full text as caption
                $delay
            );
        } catch (\Exception $e) {
            Log::error("WhatsApp PO Combined Message Error: " . $e->getMessage());
            // Fallback to text
            $this->queueMessage($supplier->phone, 'text', $message, "", "", $delay);
        }

        return true;
    }

    /**
     * Common method for single text messages
     */
    public function sendMessage($to, $message)
    {
        $delay = $this->getGlobalGapDelay();
        return $this->queueMessage($to, 'text', $message, "", "", $delay);
    }

    /**
     * Send Professional Payment Reminder
     */
    public function sendPaymentReminder($to, $type, $amount, $dueDate, $reference)
    {
        $delay = $this->getGlobalGapDelay();
        $label = ($type == 'receivable') ? "Pending Payment" : "Outstanding Amount";
        $message = "Assalam-o-Alaikum,\n\n" .
                   "This is a professional reminder regarding your {$label}.\n\n" .
                   "Reference: {$reference}\n" .
                   "Amount Due: Rs. " . number_format($amount, 2) . "\n" .
                   "Due Date: {$dueDate}\n\n" .
                   "Kindly ensure the payment is processed at your earliest convenience to maintain your account status. Thank you.\n\n" .
                   "Regards,\nAccounts - Dr Auto Store";

        return $this->queueMessage($to, 'text', $message, "", "", $delay);
    }

    /**
     * Internal Queue Mechanism: Non-Blocking Background Workers
     */
    protected function queueMessage($phone, $type, $content, $filename = "", $caption = "", $delay = 0)
    {
        $jobId = uniqid('wa_');
        $readyContent = ($type === 'text') ? $content : base64_encode($content);

        $payload = [
            'phone'        => $this->formatPhone($phone),
            'type'         => $type,
            'content'      => $readyContent,
            'filename'     => $filename,
            'caption'      => $caption,
            'delay'        => (int)$delay,
            'instance_id'  => $this->instanceId,
            'access_token' => $this->accessToken,
            'base_url'     => $this->baseUrl
        ];

        $queueDir = storage_path('app/whatsapp_queue');
        if (!File::exists($queueDir)) File::makeDirectory($queueDir, 0755, true);
        
        $payloadPath = $queueDir . DIRECTORY_SEPARATOR . "{$jobId}.json";
        File::put($payloadPath, json_encode($payload));

        $workerCode = '<?php
        $payloadFile = "' . str_replace('\\', '/', $payloadPath) . '";
        if (!file_exists($payloadFile)) exit;
        $data = json_decode(file_get_contents($payloadFile), true);
        
        // Anti-hang logic: If we are running in the main web thread (via include), 
        // skip large delays to prevent browser timeout.
        $isSynchronous = !isset($argv);
        if ($data["delay"] > 0) {
            if (!$isSynchronous || $data["delay"] <= 5) {
                sleep($data["delay"]);
            }
        }
        
        $postData = [
            "number"       => $data["phone"],
            "instance_id"  => $data["instance_id"],
            "access_token" => $data["access_token"]
        ];

        if ($data["type"] === "text") {
            $postData["type"]    = "text";
            $postData["message"] = $data["content"];
        } else {
            $postData["type"]      = "media";
            $postData["media_url"] = "data:application/pdf;base64," . $data["content"];
            $postData["filename"]  = $data["filename"];
            $postData["message"]   = $data["caption"];
        }

        $ch = curl_init($data["base_url"] . "/send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_exec($ch);
        curl_close($ch);
        
        if(file_exists($payloadFile)) unlink($payloadFile);
        if(file_exists(__FILE__)) unlink(__FILE__);
        ';

        $workerPath = $queueDir . DIRECTORY_SEPARATOR . "{$jobId}_worker.php";
        File::put($workerPath, $workerCode);

        // Try to run in background
        $ranInBackground = false;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            if (function_exists('popen')) {
                $p = @popen("start /B php \"{$workerPath}\"", "r");
                if ($p) {
                    @pclose($p);
                    $ranInBackground = true;
                }
            }
        } else {
            if (function_exists('exec')) {
                // Check if exec is actually allowed by testing a simple command
                $disabled = explode(',', ini_get('disable_functions'));
                if (!in_array('exec', array_map('trim', $disabled))) {
                    @exec("php \"{$workerPath}\" > /dev/null 2>&1 &");
                    $ranInBackground = true;
                }
            }
        }

        // Fallback: If background execution failed, run it now but it might be slow
        if (!$ranInBackground) {
            // Note: Our modified worker code above will skip long sleeps if it detects it's synchronous
            include($workerPath);
        }

        return true;
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) == 10) $phone = '92' . $phone;
        if (str_starts_with($phone, '03')) $phone = '92' . substr($phone, 1);
        return $phone;
    }
}
