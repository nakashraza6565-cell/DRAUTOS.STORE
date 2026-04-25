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

<script>
$(document).ready(function() {
    $('#quickAddSupplierForm').on('submit', function(e) {
        e.preventDefault();
        let $form = $(this);
        let $btn = $form.find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: "{{ route('suppliers.store') }}",
            type: "POST",
            data: $form.serialize(),
            success: function(response) {
                // Assuming the response returns the new supplier object
                let newOption = new Option(response.name + ' (' + (response.phone || '') + ')', response.id, true, true);
                $(newOption).data('phone', response.phone || '');
                $(newOption).data('balance', '0.00');
                $(newOption).data('name', response.name);
                
                $('#supplier_id').append(newOption).trigger('change');
                $('#addSupplierModal').modal('hide');
                $form[0].reset();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Supplier Added',
                    text: response.name + ' has been registered successfully.',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(err) {
                $btn.prop('disabled', false).text('Register Supplier');
                let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Error adding supplier';
                Swal.fire('Error', msg, 'error');
            }
        });
    });
});
</script>
