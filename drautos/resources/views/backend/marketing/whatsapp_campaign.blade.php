@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid mb-5">

    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-3">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold" style="color: var(--primary) !important;">
            <i class="fab fa-whatsapp text-success mr-2"></i>Salesman Route Campaigns
        </h1>
    </div>

    <!-- Campaign Filter Form -->
    <div class="card premium-panel shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-header bg-white py-3" style="border-radius: 15px 15px 0 0;">
            <h6 class="m-0 font-weight-bold" style="color: var(--primary);">Configure Campaign</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('whatsapp.campaign') }}" method="GET">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Target City</label>
                        <select name="city" class="form-control" required>
                            <option value="">-- Select City --</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ $selectedCity == $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Visit Date</label>
                        <input type="date" name="visit_date" class="form-control" value="{{ $visitDate }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Salesman Name</label>
                        <input type="text" name="salesman" class="form-control" placeholder="e.g. Ali Raza" value="{{ $salesman }}" required>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100-mobile shadow-sm" style="border-radius: 10px;">
                            <i class="fas fa-users mr-2"></i>Load Audience
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Audience List -->
    @if($selectedCity)
    <div class="card premium-panel shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
            <h6 class="m-0 font-weight-bold" style="color: var(--primary);">
                Customers in {{ $selectedCity }} ({{ $customers->count() }})
            </h6>
        </div>
        <div class="card-body p-0">
            @if($customers->count() > 0)
                <div class="row m-0 p-3" id="customer-grid">
                    @foreach($customers as $customer)
                        @php 
                            $balance = $customer->current_balance ?? 0;
                            $hasDues = $balance > 0;
                            // Basic format: remove leading 0, add +92. (Very basic fallback).
                            $phone = $customer->phone;
                            if(strpos($phone, '0') === 0) {
                                $phone = '92' . ltrim($phone, '0');
                            } else if (strpos($phone, '+') === 0) {
                                $phone = ltrim($phone, '+');
                            }
                        @endphp
                        <div class="col-12 col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 shadow-sm" style="border-radius: 12px; border: 1px solid rgba(0,0,0,0.05);">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="font-weight-bold mb-1 text-dark" style="font-size: 1.1rem;">{{ $customer->name }}</h6>
                                            <div class="text-muted small"><i class="fas fa-phone-alt mr-1"></i>{{ $customer->phone }}</div>
                                        </div>
                                        <div class="text-right">
                                            @if($hasDues)
                                                <span class="badge badge-danger px-2 py-1 shadow-sm" style="font-size: 0.85rem;">Rs. {{ number_format($balance) }}</span>
                                                <div class="text-danger small mt-1 font-weight-bold">Dues Reminder</div>
                                            @else
                                                <span class="badge badge-success px-2 py-1 shadow-sm" style="font-size: 0.85rem;">Rs. {{ number_format($balance) }}</span>
                                                <div class="text-success small mt-1 font-weight-bold">Tea Meet</div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <hr style="margin: 10px 0; border-top: 1px dashed #e2e8f0;">

                                    <div class="d-flex justify-content-between gap-2 mt-3">
                                        <button class="btn btn-sm btn-outline-success flex-fill mr-1 send-wa-btn" 
                                            data-phone="{{ $phone }}" 
                                            data-name="{{ $customer->name }}" 
                                            data-balance="{{ number_format($balance) }}" 
                                            data-dues="{{ $hasDues ? 'true' : 'false' }}"
                                            data-lang="en"
                                            style="border-radius: 8px; font-weight: 600;">
                                            <i class="fab fa-whatsapp mr-1"></i> English
                                        </button>
                                        <button class="btn btn-sm btn-success flex-fill ml-1 send-wa-btn" 
                                            data-phone="{{ $phone }}" 
                                            data-name="{{ $customer->name }}" 
                                            data-balance="{{ number_format($balance) }}" 
                                            data-dues="{{ $hasDues ? 'true' : 'false' }}"
                                            data-lang="ur"
                                            style="border-radius: 8px; font-weight: 600; font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', sans-serif; font-size: 0.95rem;">
                                            <i class="fab fa-whatsapp mr-1"></i> اردو
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center p-5 text-muted">
                    <i class="fas fa-search fa-3x mb-3" style="opacity: 0.2;"></i>
                    <h5>No customers found in {{ $selectedCity }}</h5>
                </div>
            @endif
        </div>
    </div>
    @endif

</div>

<!-- Configuration Data for JS -->
<div id="campaignConfig" 
    data-salesman="{{ $salesman }}" 
    data-city="{{ $selectedCity }}" 
    data-date="{{ $visitDate }}">
</div>

@endsection

@push('styles')
<style>
    @media (max-width: 768px) {
        .w-100-mobile {
            width: 100% !important;
        }
        .gap-2 {
            gap: 0.5rem;
        }
    }
    
    /* Sent state styles */
    .btn.sent-state {
        background-color: #f8fafc !important;
        color: #94a3b8 !important;
        border-color: #e2e8f0 !important;
        box-shadow: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        const config = $('#campaignConfig').data();

        $('.send-wa-btn').on('click', function(e) {
            e.preventDefault();
            
            const btn = $(this);
            const phone = btn.data('phone');
            const name = btn.data('name');
            const balance = btn.data('balance');
            const hasDues = btn.data('dues');
            const lang = btn.data('lang');
            
            let msg = '';

            if (lang === 'en') {
                if (hasDues) {
                    msg = `Assalam-o-Alaikum ${name},\n\nWe hope you are doing well.\nThis is a polite notification from Danyal Autos that our representative, ${config.salesman}, will be visiting ${config.city} on ${config.date}.\n\nAccording to our ledgers, your current outstanding balance is Rs. ${balance}. We kindly request you to prepare the payment and clear these dues during the visit.\n\nThank you for your continued trust and business!\n\nRegards,\nDanyal Autos Management`;
                } else {
                    msg = `Assalam-o-Alaikum ${name},\n\nWe hope you are doing well.\nThis is a friendly notification from Danyal Autos that our representative, ${config.salesman}, will be visiting ${config.city} on ${config.date}.\n\nLet's meet for a cup of tea and discuss how we can grow our business together!\n\nThank you for your continued trust and business!\n\nRegards,\nDanyal Autos Management`;
                }
            } else {
                // Proper Urdu Script
                if (hasDues) {
                    msg = `السلام علیکم ${name}،\n\nامید ہے آپ خیریت سے ہوں گے۔\nیہ دانیال آٹوز کی جانب سے آپ کو مطلع کیا جا رہا ہے کہ ہمارے نمائندے، ${config.salesman}، ${config.date} کو ${config.city} کا دورہ کر رہے ہیں۔\n\nہمارے کھاتوں کے مطابق آپ کا بقایا بیلنس Rs. ${balance} ہے۔ براہ کرم دورے کے دوران واجبات کی ادائیگی کو یقینی بنائیں۔\n\nدانیال آٹوز کے ساتھ کاروبار کرنے کا شکریہ!\n\nوالسلام،\nدانیال آٹوز مینجمنٹ`;
                } else {
                    msg = `السلام علیکم ${name}،\n\nامید ہے آپ خیریت سے ہوں گے۔\nیہ دانیال آٹوز کی جانب سے آپ کو مطلع کیا جا رہا ہے کہ ہمارے نمائندے، ${config.salesman}، ${config.date} کو ${config.city} کا دورہ کر رہے ہیں۔\n\nآئیں مل کر چائے پر گپ شپ کرتے ہیں اور اپنا بزنس بڑھانے پر بات کرتے ہیں!\n\nدانیال آٹوز کے ساتھ کاروبار کرنے کا شکریہ!\n\nوالسلام،\nدانیال آٹوز مینجمنٹ`;
                }
            }

            const encodedMsg = encodeURIComponent(msg);
            const waUrl = `https://wa.me/${phone}?text=${encodedMsg}`;

            // Visual feedback
            btn.closest('.card').css('border-color', '#10b981').css('box-shadow', '0 0 0 2px rgba(16, 185, 129, 0.2)');
            btn.addClass('sent-state').html('<i class="fas fa-check"></i> Sent');

            // Open WhatsApp in new tab
            window.open(waUrl, '_blank');
        });
    });
</script>
@endpush
