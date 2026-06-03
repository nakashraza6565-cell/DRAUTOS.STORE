



    // Live Clock (Synced to Pakistan/Lahore Time)
    setInterval(() => {
        document.getElementById('live-clock').innerText = new Date().toLocaleTimeString('en-US', { 
            timeZone: 'Asia/Karachi',
            hour12: true, 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        });
    }, 1000);

    // Calendar
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('dashboard-calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 400,
            headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' },
            events: "fake_route",
            eventColor: '#6366f1',
            editable: true,
            eventAllow: function(dropInfo, draggedEvent) {
                // Only allow dragging of actual Tasks
                return draggedEvent.extendedProps.isTask;
            },
            eventDrop: function(info) {
                if(!info.event.extendedProps.isTask) {
                    info.revert();
                    return;
                }
                // AJAX call to update task date
                $.ajax({
                    url: '/admin/tasks/' + info.event.id,
                    type: 'PUT',
                    data: {
                        _token: "fake_route",
                        start_date: info.event.startStr,
                        end_date: info.event.endStr || info.event.startStr
                    },
                    success: function(res) {
                        if(res.success) {
                            console.log('Task rescheduled');
                        } else {
                            info.revert();
                        }
                    },
                    error: function() {
                        info.revert();
                        alert('Error rescheduling task.');
                    }
                });
            },
            dateClick: function(info) {
                $('#quickAddDate').val(info.dateStr);
                $('#quickAddCalendarModal').modal('show');
            },
            eventDidMount: function(info) {
                // Add Bootstrap Popover for Google Calendar-like tooltips
                $(info.el).popover({
                    title: info.event.title,
                    placement: 'top',
                    trigger: 'hover',
                    html: true,
                    content: `
                        <div class="small">
                            <strong>Details:</strong> ${info.event.extendedProps.description || 'No details'}<br>
                            <strong>Status:</strong> <span class="badge badge-light border">${info.event.extendedProps.status || 'N/A'}</span><br>
                            <strong>Assignee:</strong> ${info.event.extendedProps.assignee || 'Unassigned'}
                        </div>
                    `,
                    container: 'body'
                });
                
                // Support custom text colors
                if (info.event.extendedProps.textColor) {
                    info.el.style.color = info.event.extendedProps.textColor;
                }
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
            }
        });
        calendar.render();
        
        window.calendarObj = calendar;
    });

    // Handle Quick Add Form via AJAX
    $('#quickAddCalendarForm').on('submit', function(e) {
        e.preventDefault();
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.text();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    $('#quickAddCalendarModal').modal('hide');
                    $('#quickAddCalendarForm')[0].reset();
                    if(window.calendarObj) {
                        window.calendarObj.refetchEvents();
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Added!',
                        text: 'Task saved to calendar.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                alert('Failed to save task.');
            },
            complete: function() {
                $btn.html(originalText).prop('disabled', false);
            }
        });
    });

        // Chart defaults
        Chart.defaults.global.defaultFontFamily = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.global.defaultFontColor = '#64748b';

        var rawDates = [];
        
        function openChartDetails(date, chartType) {
            $('#chartDetailsModalTitle').html('<i class="fas fa-search-dollar mr-2"></i> Details for ' + date);
            $('#chartDetailsModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2 text-muted">Fetching breakdown...</p></div>');
            $('#chartDetailsModal').modal('show');
            
            $.get("fake_route", { date: date, type: chartType }, function(data) {
                $('#chartDetailsModalBody').html(data);
            }).fail(function() {
                $('#chartDetailsModalBody').html('<div class="alert alert-danger">Failed to load details.</div>');
            });
        }

                // Top 5 Customers by Revenue (Horizontal Bar Chart)
        var ctxRev = document.getElementById("topRevenueChart").getContext('2d');
        new Chart(ctxRev, {
            type: 'horizontalBar',
            data: {
                labels: [],
                datasets: [{
                    label: "Total Spent",
                    backgroundColor: "#10b981",
                    hoverBackgroundColor: "#059669",
                    borderColor: "#10b981",
                    data: [],
                    barPercentage: 0.5
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                scales: {
                    xAxes: [{
                        ticks: {
                            callback: function(value) { return 'Rs ' + Number(value).toLocaleString(); }
                        },
                        gridLines: { display: true, drawBorder: false, borderDash: [5, 5] }
                    }],
                    yAxes: [{
                        gridLines: { display: false, drawBorder: false }
                    }],
                },
                legend: { display: false },
                tooltips: {
                    backgroundColor: "#1e293b",
                    bodyFontColor: "#fff",
                    titleMarginBottom: 10,
                    titleFontColor: '#e2e8f0',
                    titleFontSize: 13,
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            return 'Spent: Rs. ' + Number(tooltipItem.xLabel).toLocaleString();
                        }
                    }
                }
            }
        });


// Cash Flow Bar Chart
        var ctxFlow = document.getElementById("cashFlowChart").getContext('2d');
        new Chart(ctxFlow, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: "Money In",
                        backgroundColor: "#facc15",
                        hoverBackgroundColor: "#d97706",
                        borderColor: "#facc15",
                        data: [],
                        barPercentage: 0.6,
                        categoryPercentage: 0.5
                    },
                    {
                        label: "Money Out",
                        backgroundColor: "#083259",
                        hoverBackgroundColor: "#0e4a7a",
                        borderColor: "#083259",
                        data: [],
                        barPercentage: 0.6,
                        categoryPercentage: 0.5
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                scales: {
                    xAxes: [{ gridLines: { display: false, drawBorder: false } }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value) { return 'Rs ' + Number(value).toLocaleString(); }
                        },
                        gridLines: { color: "rgba(0, 0, 0, .05)", zeroLineColor: "transparent", drawBorder: false, borderDash: [5, 5] }
                    }],
                },
                legend: { display: true, position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 6, fontStyle: 'bold' } },
                tooltips: {
                    backgroundColor: "#1e293b",
                    bodyFontColor: "#fff",
                    titleMarginBottom: 10,
                    titleFontColor: '#e2e8f0',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var label = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return label + ': Rs. ' + Number(tooltipItem.yLabel).toLocaleString();
                        }
                    }
                },
                onClick: function(evt, activeElements) {
                    if (activeElements.length > 0) {
                        var index = activeElements[0]._index;
                        openChartDetails(rawDates[index], 'cash_flow');
                    }
                }
            }
        });

        // Incoming Goods vs Customer Sales Chart
        var ctxIncomingSales = document.getElementById("incomingVsSalesChart").getContext('2d');
        
        var gradientIncoming = ctxIncomingSales.createLinearGradient(0, 0, 0, 400);
        gradientIncoming.addColorStop(0, "rgba(163, 177, 198, 0.4)");
        gradientIncoming.addColorStop(1, "rgba(163, 177, 198, 0.05)");

        var gradientSales = ctxIncomingSales.createLinearGradient(0, 0, 0, 400);
        gradientSales.addColorStop(0, "rgba(250, 204, 21, 0.4)");
        gradientSales.addColorStop(1, "rgba(250, 204, 21, 0.05)");

        new Chart(ctxIncomingSales, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: "Incoming Goods Amount",
                        lineTension: 0.3,
                        backgroundColor: gradientIncoming,
                        borderColor: "#a3b1c6",
                        pointRadius: 4,
                        pointBackgroundColor: "#fff",
                        pointBorderColor: "#a3b1c6",
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: "#a3b1c6",
                        pointHoverBorderColor: "#fff",
                        pointBorderWidth: 2,
                        data: [],
                    },
                    {
                        label: "Customer Sales Amount",
                        lineTension: 0.3,
                        backgroundColor: gradientSales,
                        borderColor: "#facc15",
                        pointRadius: 4,
                        pointBackgroundColor: "#fff",
                        pointBorderColor: "#facc15",
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: "#facc15",
                        pointHoverBorderColor: "#fff",
                        pointBorderWidth: 2,
                        data: [],
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                scales: {
                    xAxes: [{ gridLines: { display: false, drawBorder: false } }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value) { return 'Rs ' + Number(value).toLocaleString(); }
                        },
                        gridLines: { color: "rgba(0, 0, 0, .05)", zeroLineColor: "transparent", drawBorder: false, borderDash: [5, 5] }
                    }],
                },
                legend: { display: true, position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 6, fontStyle: 'bold' } },
                tooltips: {
                    backgroundColor: "#1e293b",
                    bodyFontColor: "#fff",
                    titleMarginBottom: 10,
                    titleFontColor: '#e2e8f0',
                    titleFontSize: 13,
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var label = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return label + ': Rs. ' + Number(tooltipItem.yLabel).toLocaleString();
                        }
                    }
                },
                onClick: function(evt, activeElements) {
                    if (activeElements.length > 0) {
                        var index = activeElements[0]._index;
                        openChartDetails(rawDates[index], 'incoming_sales');
                    }
                }
            }
        });
    });





