<!-- Quick Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle mr-2"></i>Quick Add Supplier</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickAddSupplierForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="small font-weight-bold">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. ABC Trading Co." required>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="e.g. 0300-1234567">
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">Address (Optional)</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Street, City..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4">Register Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {

    // ── Fix: supplier modal z-index when opened from inside another modal ──
    // Bootstrap only properly supports 1 modal at a time, so when a second
    // modal opens, it gets stuck behind the first modal's backdrop.
    // We fix this by bumping the z-index dynamically on show.
    $('#addSupplierModal').on('show.bs.modal', function() {
        var baseZ = 1080 + (10 * $('.modal:visible').length);
        $(this).css('z-index', baseZ + 10);
        // Move the newly added backdrop on top too
        setTimeout(function() {
            $('.modal-backdrop').last().css('z-index', baseZ);
        }, 5);
    });

    // Reset z-index when closed so it doesn't interfere next time
    $('#addSupplierModal').on('hidden.bs.modal', function() {
        $(this).css('z-index', '');
    });

    // ── Save Supplier ──────────────────────────────────────────────
    $('#quickAddSupplierForm').on('submit', function(e) {
        e.preventDefault();
        let $form = $(this);
        let $btn = $form.find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: "{{ route('supplier.quick-store') }}",
            type: "POST",
            data: $form.serialize() + "&_token={{csrf_token()}}",
            success: function(res) {
                if(res.status === 'success') {
                    let response = res.supplier;

                    // Add to the main page supplier dropdown (e.g. on Incoming Goods form)
                    let newOption = new Option(response.name + ' (' + (response.phone || '') + ')', response.id, true, true);
                    $(newOption).data('phone', response.phone || '');
                    $(newOption).data('balance', '0.00');
                    $(newOption).data('name', response.name);
                    $('#supplier_id').append(newOption).trigger('change');

                    // Also add to the Quick Add Product modal's supplier select
                    let productModalOption = new Option(response.name + ' (' + (response.phone || '') + ')', response.id, true, true);
                    $('#qa-supplier-select').append(productModalOption).trigger('change');

                    $('#addSupplierModal').modal('hide');
                    $form[0].reset();

                    Swal.fire({
                        icon: 'success',
                        title: 'Supplier Added',
                        text: response.name + ' has been registered successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> Register Supplier');
            },
            error: function(err) {
                $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> Register Supplier');
                let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Error adding supplier';
                Swal.fire('Error', msg, 'error');
            }
        });
    });
});
</script>
@endpush
