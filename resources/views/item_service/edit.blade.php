@extends('templates.main')

@section('title_page')
  Item Service
@endsection

@section('breadcrumb_title')
  item service
@endsection

@section('content')
    <div class="row">
      <div class="col-10">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Edit Item</h3>
            <a href="{{ route('po_service.add_items', $po_id) }}" class="btn btn-sm btn-primary float-right"><i class="fas fa-undo"></i> Back</a>
          </div>
          <div class="card-body">
            <form action="{{ route('item_service.update', $item->id) }}" method="POST">
              @csrf @method('PUT')

              <div class="form-group">
                <label for="item_code">Item Code</label>
                <input type="text" value="{{ old('item_code', $item->item_code) }}" name="item_code" class="form-control @error('item_code') is-invalid @enderror">
                @error('item_code')
                  <div class="invalid-feedback">
                    {{ $message }}
                  </div>
                @enderror
              </div>

              <div class="form-group">
                <label for="item_desc">Item Description</label>
                <input type="text" value="{{ old('item_desc', $item->item_desc) }}" name="item_desc" class="form-control @error('item_desc') is-invalid @enderror">
                @error('item_desc')
                  <div class="invalid-feedback">
                    {{ $message }}
                  </div>
                @enderror
              </div>

              <div class="form-group">
                <label for="qty">Qty</label>
                <input type="text" value="{{ old('qty', $item->qty) }}" name="qty" class="form-control @error('qty') is-invalid @enderror">
                @error('qty')
                  <div class="invalid-feedback">
                    {{ $message }}
                  </div>
                @enderror
              </div>
    
              <div class="form-group">
                <label for="uom">UOM</label>
                <input type="text" value="{{ old('uom', $item->uom) }}" name="uom" class="form-control @error('uom') is-invalid @enderror">
                @error('uom')
                  <div class="invalid-feedback">
                    {{ $message }}
                  </div>
                @enderror
              </div>

              <div class="form-group">
                <label for="unit_price">Unit Price</label>
                <input type="text" value="{{ old('unit_price', $item->unit_price) }}" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror">
                @error('unit_price')
                  <div class="invalid-feedback">
                    {{ $message }}
                  </div>
                @enderror
              </div>

              <div class="card-footer">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i> Save</button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
@endsection