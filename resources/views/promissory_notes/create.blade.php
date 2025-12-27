@include('layouts.header')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="panel">
                <div class="panel-container">
                    <div class="panel-content">
                        <h3>Yeni Senet Ekle</h3>

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('promissory_notes.store') }}" method="POST">
                            @csrf

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
                                                    <option value="{{ $customer->id }}">
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
                                                    <option value="{{ $account->id }}">{{ $account->name }} (₺{{ number_format($account->balance, 2) }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type">Senet Türü *</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-exchange-alt"></i>
                                                </span>
                                            </div>
                                            <select name="type" id="type" class="form-control" required>
                                                <option value="">Tür Seç</option>
                                                <option value="verilen">Verilen Senet</option>
                                                <option value="alınan">Alınan Senet</option>
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
                                            <input type="text" name="bank_name" id="bank_name" class="form-control" required>
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
                                            <input type="text" name="branch_name" id="branch_name" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="note_number">Senet Numarası *</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-hashtag"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="note_number" id="note_number" class="form-control" required>
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
                                            <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" required>
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
                                            <input type="date" name="issue_date" id="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
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
                                            <input type="date" name="maturity_date" id="maturity_date" class="form-control" required>
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
                                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Senet Oluştur</button>
                                <a href="{{ route('promissory_notes.index') }}" class="btn btn-secondary">İptal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
