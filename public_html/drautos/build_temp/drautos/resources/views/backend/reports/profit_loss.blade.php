@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profit & Loss Statement</h1>
        <button class="btn btn-sm btn-primary shadow-sm" onclick="window.print()"><i class="fas fa-print fa-sm text-white-50"></i> Print P&L</button>
    </div>

    <div class="row">
        <!-- Revenue Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Sales Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{number_format($totalRevenue, 2)}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- COGS Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Cost of Goods Sold (COGS)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{number_format($totalCostOfGoods, 2)}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Operational Expenses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{number_format($totalExpenses, 2)}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Financial Summary (P&L)</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-lg mb-0">
                        <tr class="bg-light">
                            <th class="py-4">Particulars</th>
                            <th class="text-right py-4">Amount (PKR)</th>
                        </tr>
                        <tr>
                            <td class="pl-4">Total Operating Revenue (Sales)</td>
                            <td class="text-right pr-4 font-weight-bold text-success">+ Rs. {{number_format($totalRevenue, 2)}}</td>
                        </tr>
                        <tr>
                            <td class="pl-4">Less: Cost of Goods Sold (Product Costs)</td>
                            <td class="text-right pr-4 font-weight-bold text-danger">- Rs. {{number_format($totalCostOfGoods, 2)}}</td>
                        </tr>
                        <tr class="table-info font-weight-bold">
                            <td class="pl-4">GROSS PROFIT</td>
                            <td class="text-right pr-4">Rs. {{number_format($totalRevenue - $totalCostOfGoods, 2)}}</td>
                        </tr>
                        <tr>
                            <td class="pl-4">Less: Operational Expenses (Electricity, Salaries, etc.)</td>
                            <td class="text-right pr-4 font-weight-bold text-danger">- Rs. {{number_format($totalExpenses, 2)}}</td>
                        </tr>
                        <tr class="bg-dark text-white font-weight-bold" style="font-size: 1.25rem;">
                            <td class="pl-4 py-4">NET PROFIT / LOSS</td>
                            <td class="text-right pr-4 py-4 text-{{$netProfit >= 0 ? 'success' : 'danger'}}">
                                Rs. {{number_format($netProfit, 2)}}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4 bg-gradient-{{$netProfit >= 0 ? 'success' : 'danger'}} text-white">
                <div class="card-body text-center py-5">
                    <i class="fas fa-{{$netProfit >= 0 ? 'smile-beam' : 'frown'}} fa-5x mb-4 opacity-50"></i>
                    <h3 class="font-weight-bold">{{$netProfit >= 0 ? 'Surplus' : 'Deficit'}} Business Status</h3>
                    <p class="mb-0">Based on processed orders and recorded expenses.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
