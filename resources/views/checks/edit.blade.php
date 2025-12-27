@include('layouts.header')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="panel">
                <div class="panel-container">
                    <div class="panel-content">
                        <h3>Çek Düzenle</h3>

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('checks.update', $check->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_id">Müşteri *</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-user"></i>
                                                </span>
                                            </div>
                                            <select name="customer_id" id="customer_id" class="form-control" required>
                                                <option value="">Müşteri Seç</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}" {{ $check->customer_id == $customer->id ? 'selected' : '' }}>
                                                        {{ $customer->title }}
                                                        @if($customer->current_balance > 0)
                                                            (Alacak: ₺{{ number_format($customer->current_balance, 2) }})
                                                        @elseif($customer->current_balance < 0)
                                                            (Borç: ₺{{ number_format(abs($customer->current_balance), 2) }})
                                                        @else
                                                            (₺0.00)
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="fal fa-wallet"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="account_id">Hesap</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-university"></i>
                                                </span>
                                            </div>
                                            <select name="account_id" id="account_id" class="form-control">
                                                <option value="">Hesap Seç</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}" {{ $check->account_id == $account->id ? 'selected' : '' }}>{{ $account->name }} (₺{{ number_format($account->balance, 2) }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type">Çek Türü *</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-exchange-alt"></i>
                                                </span>
                                            </div>
                                            <select name="type" id="type" class="form-control" required>
                                                <option value="">Tür Seç</option>
                                                <option value="verilen" {{ $check->type == 'verilen' ? 'selected' : '' }}>Verilen Çek</option>
                                                <option value="alınan" {{ $check->type == 'alınan' ? 'selected' : '' }}>Alınan Çek</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bank_name">Banka Adı *</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-building"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="bank_name" id="bank_name" class="form-control" value="{{ $check->bank_name }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="branch_name">Şube Adı</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-map-marker-alt"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="branch_name" id="branch_name" class="form-control" value="{{ $check->branch_name }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="check_number">Çek Numarası *</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-hashtag"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="check_number" id="check_number" class="form-control" value="{{ $check->check_number }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount">Tutar *</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-lira-sign"></i>
                                                </span>
                                            </div>
                                            <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" value="{{ $check->amount }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="issue_date">Düzenleme Tarihi *</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <input type="date" name="issue_date" id="issue_date" class="form-control" value="{{ $check->issue_date }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="maturity_date">Vade Tarihi *</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-calendar-check"></i>
                                                </span>
                                            </div>
                                            <input type="date" name="maturity_date" id="maturity_date" class="form-control" value="{{ $check->maturity_date }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Durum *</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-info-circle"></i>
                                                </span>
                                            </div>
                                            <select name="status" id="status" class="form-control" required>
                                                <option value="PENDING" {{ $check->status == 'PENDING' ? 'selected' : '' }}>Beklemede</option>
                                                <option value="CLEARED" {{ $check->status == 'CLEARED' ? 'selected' : '' }}>Tahsil Edildi</option>
                                                <option value="BOUNCED" {{ $check->status == 'BOUNCED' ? 'selected' : '' }}>Karşılıksız</option>
                                                <option value="CANCELLED" {{ $check->status == 'CANCELLED' ? 'selected' : '' }}>İptal Edildi</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="description">Açıklama</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-comment-alt"></i>
                                                </span>
                                            </div>
                                            <textarea name="description" id="description" class="form-control" rows="3">{{ $check->description }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Çek Güncelle</button>
                                <a href="{{ route('checks.show', $check->id) }}" class="btn btn-secondary">İptal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
