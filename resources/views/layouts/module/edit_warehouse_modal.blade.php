@foreach($warehouses as $warehouse)
<div class="modal fade" id="editWarehouseModal{{ $warehouse->id }}" tabindex="-1" role="dialog" aria-labelledby="editWarehouseModalLabel{{ $warehouse->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fal fa-warehouse mr-1"></i>
                    Depo Düzenle
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editWarehouseForm" action="{{ route('warehouse.update') }}" method="post">
                    @csrf
                    <input type="hidden" name="warehouse_id" value="{{ $warehouse->id }}">
                    <div class="form-group">
                        <label class="form-label" for="warehouse_name{{ $warehouse->id }}">Depo Adı</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fal fa-warehouse-alt"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" id="warehouse_name{{ $warehouse->id }}" name="warehouse_name" value="{{ $warehouse->name }}" required>
                            <div class="invalid-feedback">Lütfen depo adı giriniz.</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="address{{ $warehouse->id }}">Adres</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fal fa-map-marker-alt"></i>
                                </span>
                            </div>
                            <textarea class="form-control" id="address{{ $warehouse->id }}" name="address" rows="3" required>{{ $warehouse->address }}</textarea>
                            <div class="invalid-feedback">Lütfen adres giriniz.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="phone{{ $warehouse->id }}">Telefon</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fal fa-phone"></i>
                                        </span>
                                    </div>
                                    <input type="tel" class="form-control" id="phone{{ $warehouse->id }}" name="phone" 
                                        data-inputmask="'mask': '(999) 999-9999'" value="{{ $warehouse->phone }}" required>
                                    <div class="invalid-feedback">Lütfen telefon giriniz.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="manager{{ $warehouse->id }}">Depo Sorumlusu</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fal fa-user"></i>
                                        </span>
                                    </div>
                                    <select class="custom-select" id="manager{{ $warehouse->id }}" name="manager" required>
                                        <option value="{{ $warehouse->manager }}">{{ $warehouse->manager_name }}</option>
                                        @foreach($employees as $employee)
                                        @if($employee->id != $warehouse->manager)
                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Lütfen sorumlu seçiniz.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect waves-themed" data-dismiss="modal">
                    <i class="fal fa-times mr-1"></i> İptal
                </button>
                <button type="button" class="btn btn-primary waves-effect waves-themed" onclick="updateWarehouse({{ $warehouse->id }})">
                    <i class="fal fa-save mr-1"></i> Güncelle
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach
