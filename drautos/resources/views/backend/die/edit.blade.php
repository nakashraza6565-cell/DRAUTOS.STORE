@extends('backend.layouts.master')

@section('title', 'Edit Die')

@section('main-content')
<div class="card shadow mb-4 border-0">
    <div class="card-header py-3">
        <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit mr-2"></i>Edit Die</h5>
    </div>
    <div class="card-body">
        <form method="post" action="{{route('die-management.update', $die->id)}}">
            @csrf 
            @method('PATCH')
            
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="inputName" class="col-form-label">Die Name <span class="text-danger">*</span></label>
                    <input id="inputName" type="text" name="name" placeholder="Enter die name/code" value="{{$die->name}}" class="form-control" required>
                    @error('name')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label for="product_id" class="col-form-label">Produces Product <span class="text-danger">*</span></label>
                    <select name="product_id" id="product_id" class="form-control select2" required>
                        <option value="">-- Select Product --</option>
                        @foreach($products as $product)
                            <option value="{{$product->id}}" {{$die->product_id == $product->id ? 'selected' : ''}}>{{$product->title}}</option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="rack_number" class="col-form-label">Rack Location / Warehouse Number</label>
                    <input id="rack_number" type="text" name="rack_number" placeholder="E.g. Rack A-3" value="{{$die->rack_number}}" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label for="die_type" class="col-form-label">Die Type</label>
                    <input id="die_type" type="text" name="die_type" placeholder="E.g. Molding, Casting, Press" value="{{$die->die_type}}" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="maker_id" class="col-form-label">Maker / Supplier</label>
                    <select name="maker_id" id="maker_id" class="form-control select2">
                        <option value="">-- Select Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{$supplier->id}}" {{$die->maker_id == $supplier->id ? 'selected' : ''}}>{{$supplier->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="making_cost" class="col-form-label">Making Cost (PKR)</label>
                    <input id="making_cost" type="number" step="0.01" min="0" name="making_cost" placeholder="0.00" value="{{$die->making_cost}}" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="custody_of" class="col-form-label">Custody Of</label>
                    <input id="custody_of" type="text" name="custody_of" placeholder="Name of person/workshop in custody" value="{{$die->custody_of}}" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label for="custody_phone" class="col-form-label">Custody Contact Phone</label>
                    <input id="custody_phone" type="text" name="custody_phone" placeholder="E.g. 03001234567" value="{{$die->custody_phone}}" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="quality_status" class="col-form-label">Quality Status</label>
                    <select name="quality_status" class="form-control">
                        <option value="good" {{$die->quality_status == 'good' ? 'selected' : ''}}>Good (Ready)</option>
                        <option value="maintenance_required" {{$die->quality_status == 'maintenance_required' ? 'selected' : ''}}>Maintenance Required</option>
                        <option value="damaged" {{$die->quality_status == 'damaged' ? 'selected' : ''}}>Damaged (Inactive)</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control">
                        <option value="active" {{$die->status == 'active' ? 'selected' : ''}}>Active</option>
                        <option value="inactive" {{$die->status == 'inactive' ? 'selected' : ''}}>Inactive</option>
                    </select>
                    @error('status')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>

            <hr>

            <!-- Main Display Photo -->
            <div class="form-group">
                <label for="inputPhoto" class="col-form-label">Main Display Photo</label>
                <div class="input-group">
                    <span class="input-group-btn">
                        <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
                            <i class="fas fa-image"></i> Choose Main Photo
                        </a>
                    </span>
                    <input id="thumbnail" class="form-control" type="text" name="photo" value="{{$die->photo}}">
                </div>
                <div id="holder" style="margin-top:15px;max-height:100px;">
                    @if($die->photo)
                        <img src="{{$die->photo}}" style="max-height:100px;">
                    @endif
                </div>
                @error('photo')
                    <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            <!-- Additional Photos Gallery -->
            <div class="form-group border p-3 rounded">
                <label class="font-weight-bold text-gray-800"><i class="fas fa-images mr-1 text-primary"></i>Additional Photos Gallery</label>
                <div class="gallery-photos-container" id="galleryContainer">
                    @php
                        $photos = $die->photos ?: [];
                    @endphp
                    @if(count($photos) > 0)
                        @foreach($photos as $index => $photoUrl)
                            <div class="input-group mb-2 gallery-row">
                                <input class="form-control gallery-input" type="text" name="photos[]" value="{{$photoUrl}}" placeholder="Photo URL">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-primary choose-gallery-lfm"><i class="fas fa-folder-open"></i></button>
                                    <button type="button" class="btn btn-outline-danger remove-gallery-row"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="input-group mb-2 gallery-row">
                            <input class="form-control gallery-input" type="text" name="photos[]" placeholder="Photo URL">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-primary choose-gallery-lfm"><i class="fas fa-folder-open"></i></button>
                                <button type="button" class="btn btn-outline-danger remove-gallery-row"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mt-1" id="addPhotoRowBtn"><i class="fas fa-plus"></i> Add Another Photo Row</button>
            </div>

            <div class="form-group mb-3 mt-4 text-right">
                <button class="btn btn-success font-weight-bold px-4" type="submit">Update Die</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // Initialize Main Photo Filemanager
        $('#lfm').filemanager('image');

        // Dynamic ID generator for gallery file managers
        let galleryIndex = 0;
        
        function setupGalleryLfm(row) {
            galleryIndex++;
            const inputField = row.find('.gallery-input');
            const lfmBtn = row.find('.choose-gallery-lfm');
            
            const uniqueId = 'gallery_input_edit_' + galleryIndex;
            inputField.attr('id', uniqueId);
            lfmBtn.attr('data-input', uniqueId);
            
            lfmBtn.filemanager('image');
        }

        // Setup existing rows
        $('.gallery-row').each(function() {
            setupGalleryLfm($(this));
        });

        // Add photo row btn
        $('#addPhotoRowBtn').click(function() {
            const newRow = $(`
                <div class="input-group mb-2 gallery-row">
                    <input class="form-control gallery-input" type="text" name="photos[]" placeholder="Photo URL">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-primary choose-gallery-lfm"><i class="fas fa-folder-open"></i></button>
                        <button type="button" class="btn btn-outline-danger remove-gallery-row"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `);
            $('#galleryContainer').append(newRow);
            setupGalleryLfm(newRow);
        });

        // Remove row
        $(document).on('click', '.remove-gallery-row', function() {
            if ($('.gallery-row').length > 1) {
                $(this).closest('.gallery-row').remove();
            } else {
                $(this).closest('.gallery-row').find('input').val('');
            }
        });
    });
</script>
@endpush
