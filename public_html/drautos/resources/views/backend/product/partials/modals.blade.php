<!-- Add Unit Modal -->
<div class="modal fade" id="addUnitModal" tabindex="-1" role="dialog" aria-labelledby="addUnitModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addUnitModalLabel">Add New Unit Option</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="quickAddUnitForm" method="POST">
        <div class="modal-body">
          <div class="form-group">
              <label for="new_unit_name">Unit Name (e.g., per carton, per gram)</label>
              <input type="text" name="name" id="new_unit_name" class="form-control" placeholder="Enter unit name" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Unit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Model Modal -->
<div class="modal fade" id="addModelModal" tabindex="-1" role="dialog" aria-labelledby="addModelModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addModelModalLabel">Add New Model</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="quickAddModelForm" method="POST">
        <div class="modal-body">
          <div class="form-group">
              <label for="new_model_name">Model Name</label>
              <input type="text" name="name" id="new_model_name" class="form-control" placeholder="Enter model name" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Model</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Brand Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1" role="dialog" aria-labelledby="addBrandModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addBrandModalLabel">Add Brand</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="quickAddBrandForm" method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label for="new_brand_title" class="col-form-label">Title <span class="text-danger">*</span></label>
            <input id="new_brand_title" type="text" name="title" placeholder="Enter title" required class="form-control">
          </div>
          <div class="form-group">
            <label for="new_brand_company" class="col-form-label">Company Name</label>
            <input id="new_brand_company" type="text" name="company_name" placeholder="Enter company name" class="form-control">
          </div>
          <div class="form-group">
            <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
            <select name="status" class="form-control">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Brand</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="quickAddCategoryForm" method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label for="new_cat_title" class="col-form-label">Title <span class="text-danger">*</span></label>
            <input id="new_cat_title" type="text" name="title" placeholder="Enter title" required class="form-control">
          </div>
          <div class="form-group">
            <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
            <select name="status" class="form-control">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Category</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add SubCategory Modal -->
<div class="modal fade" id="addSubCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addSubCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSubCategoryModalLabel">Add SubCategory</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="quickAddSubCategoryForm" method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label for="new_subcat_title" class="col-form-label">Title <span class="text-danger">*</span></label>
            <input id="new_subcat_title" type="text" name="title" placeholder="Enter title" required class="form-control">
          </div>
          <div class="form-group">
              <label for="new_subcat_status">Status</label>
              <select name="status" id="new_subcat_status" class="form-control">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
              </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save SubCategory</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSupplierModalLabel">Add New Supplier (Detailed)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="quickAddSupplierForm" method="POST">
        <div class="modal-body">
          <div class="row">
              <div class="col-md-6 form-group">
                  <label for="new_sup_name">Supplier Name <span class="text-danger">*</span></label>
                  <input type="text" name="name" id="new_sup_name" class="form-control" placeholder="Enter supplier name" required>
              </div>
              <div class="col-md-6 form-group">
                  <label for="new_sup_email">Email</label>
                  <input type="email" name="email" id="new_sup_email" class="form-control" placeholder="Enter email">
              </div>
          </div>
          <div class="row">
              <div class="col-md-6 form-group">
                  <label for="new_sup_phone">Phone Number</label>
                  <input type="text" name="phone" id="new_sup_phone" class="form-control" placeholder="Enter phone number">
              </div>
              <div class="col-md-6 form-group">
                  <label for="new_sup_company">Company Name</label>
                  <input type="text" name="company_name" id="new_sup_company" class="form-control" placeholder="Enter company name">
              </div>
          </div>
          <div class="form-group">
              <label for="new_sup_address">Address</label>
              <textarea name="address" id="new_sup_address" class="form-control" rows="2" placeholder="Enter full address"></textarea>
          </div>
          <div class="form-group">
              <label for="new_sup_status">Status</label>
              <select name="status" id="new_sup_status" class="form-control">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
              </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Supplier</button>
        </div>
      </form>
    </div>
  </div>
</div>
