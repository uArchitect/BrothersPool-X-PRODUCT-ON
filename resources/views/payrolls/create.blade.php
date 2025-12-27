@include('layouts.header')

<style>
.form-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.form-section h5 {
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 10px;
    margin-bottom: 20px;
}
</style>

<main id="js-page-content" role="main" class="page-content">
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel shadow-sm">
                <div class="panel-container show">
                    <div class="panel-content">
                        
                        <!-- Başlık -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="text-primary mb-1"><i class="fal fa-plus-circle mr-2"></i>Yeni Bordro Oluştur</h4>
                                <p class="text-muted mb-0">Personel için yeni bordro kaydı oluşturun</p>
                            </div>
                            <div>
                                <a href="{{ route('payrolls.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="fal fa-arrow-left mr-1"></i> Geri Dön
                                </a>
                            </div>
                        </div>

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fal fa-exclamation-circle mr-2"></i>{{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        <!-- Form -->
                        <form action="{{ route('payrolls.store') }}" method="POST">
                            @csrf
                            
                            <div class="form-section">
                                <h5><i class="fal fa-user mr-2"></i>Personel Seçimi</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="employee_id">Personel <span class="text-danger">*</span></label>
                                            <select class="form-control @error('employee_id') is-invalid @enderror" 
                                                    id="employee_id" name="employee_id" required>
                                                <option value="">Personel Seçin</option>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}
                                                            @if(!$employee->is_active) style="color: #dc3545; font-style: italic;" @endif>
                                                        {{ $employee->name }} 
                                                        @if($employee->payment_frequency)
                                                            ({{ ucfirst($employee->payment_frequency) }})
                                                        @endif
                                                        @if(!$employee->is_active)
                                                            [Aktif Değil]
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">
                                                <i class="fal fa-info-circle"></i> 
                                                Kırmızı renkli personeller aktif değildir, ancak bordro oluşturulabilir.
                                            </small>
                                            @error('employee_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h5><i class="fal fa-calendar-alt mr-2"></i>Periyot Bilgileri</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="period_type">Periyot Tipi <span class="text-danger">*</span></label>
                                            <select class="form-control @error('period_type') is-invalid @enderror" 
                                                    id="period_type" name="period_type" required>
                                                <option value="">Seçin</option>
                                                <option value="daily" {{ old('period_type') == 'daily' ? 'selected' : '' }}>Günlük</option>
                                                <option value="weekly" {{ old('period_type') == 'weekly' ? 'selected' : '' }}>Haftalık</option>
                                                <option value="monthly" {{ old('period_type') == 'monthly' ? 'selected' : '' }}>Aylık</option>
                                                <option value="hourly" {{ old('period_type') == 'hourly' ? 'selected' : '' }}>Saatlik</option>
                                            </select>
                                            @error('period_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="period_start_date">Başlangıç Tarihi <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('period_start_date') is-invalid @enderror" 
                                                   id="period_start_date" name="period_start_date" 
                                                   value="{{ old('period_start_date') }}" required>
                                            @error('period_start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="period_end_date">Bitiş Tarihi <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('period_end_date') is-invalid @enderror" 
                                                   id="period_end_date" name="period_end_date" 
                                                   value="{{ old('period_end_date') }}" required>
                                            @error('period_end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fal fa-save mr-2"></i> Bordro Oluştur
                                </button>
                                <a href="{{ route('payrolls.index') }}" class="btn btn-secondary btn-lg ml-2">
                                    <i class="fal fa-times mr-2"></i> İptal
                                </a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
$(document).ready(function() {
    // Bitiş tarihi başlangıç tarihinden önce olamaz
    $('#period_start_date').on('change', function() {
        var startDate = $(this).val();
        if (startDate) {
            $('#period_end_date').attr('min', startDate);
            if ($('#period_end_date').val() && $('#period_end_date').val() < startDate) {
                $('#period_end_date').val(startDate);
            }
        }
    });
});
</script>

@include('layouts.footer')

