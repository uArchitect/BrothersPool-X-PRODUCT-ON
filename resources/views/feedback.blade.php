@include('layouts.header')

<style>
:root {
    --primary: #2563eb; --success: #059669; --gray-50: #f9fafb; 
    --gray-100: #f3f4f6; --gray-500: #6b7280; --gray-700: #374151;
    --border: #e5e7eb; --radius: 6px;
}

.feedback-card {
    background: white; border: 1px solid var(--border); border-radius: var(--radius);
    margin-bottom: 1rem; box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
    transition: box-shadow 0.2s ease;
}
.feedback-card:hover { box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); }
.feedback-card-header { 
    padding: 1.5rem; border-bottom: 1px solid var(--border); background: var(--gray-50); 
}
.feedback-card-body { padding: 1.5rem; }

.status-indicator {
    display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem;
    border-radius: 50px; font-size: 0.75rem; font-weight: 500; color: white;
    transition: transform 0.2s;
}
.status-indicator:hover { transform: translateY(-1px); }
.status-open { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.status-answered { background: linear-gradient(135deg, #10b981, #059669); }
.status-closed { background: linear-gradient(135deg, #6b7280, #4b5563); }

.btn-modern {
    display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem;
    border: none; border-radius: var(--radius); font-weight: 500; font-size: 0.875rem;
    cursor: pointer; text-decoration: none; transition: opacity 0.2s;
}
.btn-modern:hover { opacity: 0.8; }
.btn-primary-modern { background: var(--primary); color: white; }
.btn-success-modern { background: var(--success); color: white; }
.btn-modern.btn-sm { padding: 0.5rem 0.75rem; font-size: 0.8rem; }

.chat-container {
    background: white; border: 1px solid var(--border); border-radius: var(--radius);
    overflow: hidden; box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
}
.chat-table { width: 100%; border-collapse: collapse; }
.chat-table th {
    background: var(--gray-100); padding: 0.875rem; text-align: left; font-weight: 600;
    font-size: 0.8rem; color: var(--gray-700); border-bottom: 1px solid var(--border);
}
.chat-table td { padding: 1rem 0.875rem; border-bottom: 1px solid var(--border); vertical-align: top; }
.chat-table tr:last-child td { border-bottom: none; }
.message-row-user { background: #fefefe; }
.message-row-admin { background: #f8fafc; }
.message-author { font-weight: 600; color: var(--gray-700); min-width: 100px; }
.message-time { font-size: 0.75rem; color: var(--gray-500); white-space: nowrap; min-width: 120px; }
.message-content-cell { word-wrap: break-word; line-height: 1.5; color: var(--gray-700); }
.message-icon { width: 20px; text-align: center; color: var(--primary); }

.chat-input-container { background: var(--gray-50); border: 1px solid var(--border); border-radius: var(--radius); padding: 1rem; margin-top: 1rem; }
.chat-input { width: 100%; border: 1px solid var(--border); border-radius: var(--radius); padding: 0.75rem; font-size: 0.875rem; resize: vertical; min-height: 60px; }
.chat-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgb(37 99 235 / 0.1); }
.modal-modern .modal-content { border: none; border-radius: var(--radius); }
.modal-modern .modal-header { background: var(--gray-50); border-bottom: 1px solid var(--border); padding: 1.5rem; }
.modal-modern .modal-title { font-weight: 600; font-size: 1.125rem; color: var(--gray-700); }
.modal-modern .modal-body { padding: 1.5rem; }
.modal-modern .modal-footer { background: var(--gray-50); border-top: 1px solid var(--border); padding: 1.5rem; }
.form-modern { display: flex; flex-direction: column; gap: 1.5rem; }
.form-group-modern { display: flex; flex-direction: column; gap: 0.5rem; }
.form-label-modern { font-weight: 600; color: var(--gray-700); font-size: 0.875rem; }
.form-control-modern { padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius); font-size: 0.875rem; }
.form-control-modern:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgb(37 99 235 / 0.1); }

.notification {
    position: fixed; top: 1rem; right: 1rem; padding: 1rem 1.5rem; border-radius: var(--radius);
    color: white; font-weight: 500; z-index: 9999; animation: slideIn 0.3s ease;
}
.notification.success { background: var(--success); }
.notification.error { background: #dc2626; }
@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

.unread-message { border-left: 4px solid #ef4444; background: linear-gradient(135deg, #fef2f2, #ffffff); position: relative; }
.unread-message::before {
    content: ''; position: absolute; top: 50%; left: -6px; width: 8px; height: 8px;
    background: #ef4444; border-radius: 50%; transform: translateY(-50%); animation: pulse 2s infinite;
}
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
.last-message { padding: 0.75rem; background: var(--gray-50); border-radius: var(--radius); border-left: 3px solid var(--primary); margin-top: 1rem; }
.message-meta { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; font-size: 0.8rem; color: var(--gray-500); }
.message-content { color: var(--gray-700); line-height: 1.4; }
.admin-message { border-left-color: #10b981; }
.user-message { border-left-color: var(--primary); }

@media (max-width: 768px) {
    .feedback-card-header, .feedback-card-body, .chat-input-container { padding: 1rem; }
    .modal-modern .modal-content { margin: 0.5rem; }
    .chat-table th, .chat-table td { padding: 0.75rem 0.5rem; }
    .alert .d-flex { flex-direction: column; gap: 1rem; }
    .alert .new-support-action button { width: 100%; justify-content: center; }
}
</style>

<main id="js-page-content" role="main" class="page-content feedback-system">
    <ol class="breadcrumb page-breadcrumb align-items-center">
        <li class="breadcrumb-item">
            <a href="#"><i class="fal fa-home mr-1"></i> Ana Sayfa</a>
        </li>
        <li class="breadcrumb-item active">Destek Sistemi</li>
    </ol>

    <div class="panel" id="panel-1">
        <div class="panel-hdr">
            <h2><i class="fal fa-headset mr-2 text-primary"></i>Destek Sistemi</h2>
            <div class="panel-toolbar">
                <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Daralt/Genişlet"></button>
                <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Tam Ekran"></button>
            </div>
        </div>
        
        <div class="panel-container show">
            <div class="panel-content">
                <!-- Info Banner -->
                <div class="alert alert-info border-0" style="background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%); border: 1px solid #93c5fd;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fal fa-info-circle fa-lg text-primary mr-3"></i>
                            <div>
                                <strong>Destek Sistemi</strong><br>
                                Sorunlarınızı ve taleplerinizi buradan iletebilir, geçmiş destek kayıtlarınızı görüntüleyebilirsiniz.
                            </div>
                        </div>
                        <!-- New Support Button -->
                        <div class="new-support-action">
                            <button class="btn btn-success-modern btn-modern" data-toggle="modal" data-target="#newSupportModal">
                                <i class="fal fa-plus"></i>
                                Yeni Destek Talebi
                            </button>
                        </div>
                    </div>
                </div>


                <!-- Feedback List -->
                <div class="feedback-list">
                            @php
                                $grouped = collect($getFeedback)->groupBy('id');
                            @endphp
                    
                            @forelse($grouped as $id => $messages)
                                @php
                                    $first = $messages->first();
                                    $lastMessage = $messages->last();
                                    
                                    // Status belirleme
                                    $statusClass = $first['durum'] == 0 ? 'status-open' : ($first['durum'] == 1 ? 'status-answered' : 'status-closed');
                                    $statusText = $first['durum'] == 0 ? 'Açık' : ($first['durum'] == 1 ? 'Yanıtlandı' : 'Kapalı');
                                    $statusIcon = $first['durum'] == 0 ? 'fa-circle' : ($first['durum'] == 1 ? 'fa-check-circle' : 'fa-times-circle');
                                    
                                    // Son mesaj analizi - daha akıllı
                                    $isLastFromAdmin = !isset($lastMessage['sender']) || ($lastMessage['sender'] !== 'user' && $lastMessage['sender'] !== 'users');
                                    $shouldShowLastMessage = $lastMessage['id'] !== $first['id']; // İlk mesaj değilse göster
                                    
                                    // Kart CSS sınıfı - admin'den gelen okunmamış mesaj varsa kırmızı vurgu
                                    $cardClass = ($isLastFromAdmin && $first['durum'] == 0) ? 'unread-message' : '';
                                @endphp
                        
                        <div class="feedback-card mb-3 {{ $cardClass }}" data-feedback-id="{{ $id }}">
                            <div class="feedback-card-header">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="status-indicator {{ $statusClass }}">
                                            <i class="fal {{ $statusIcon }}"></i>
                                            {{ $statusText }}
                                        </div>
                                        <h6 class="mb-0 font-weight-bold">{{ $first['konu'] }}</h6>
                                    </div>
                                    <button class="btn btn-primary-modern btn-modern btn-sm" 
                                            data-toggle="modal" 
                                            data-target="#feedbackDetailModal{{ $id }}">
                                        <i class="fal fa-eye"></i>
                                        Görüntüle
                                    </button>
                                </div>
                            </div>
                            
                            <div class="feedback-card-body">
                                @if($shouldShowLastMessage)
                                    <!-- Sadece son mesajı akıllıca göster -->
                                    @php
                                        $messageToShow = $lastMessage;
                                        $isFromAdmin = !isset($messageToShow['sender']) || ($messageToShow['sender'] !== 'user' && $messageToShow['sender'] !== 'users');
                                        $messageClass = $isFromAdmin ? 'admin-message' : 'user-message';
                                        $authorName = $messageToShow['name'] ?? $messageToShow['user'] ?? 'Bilinmeyen';
                                        $messageIcon = $isFromAdmin ? 'fa-user-headset' : 'fa-user';
                                    @endphp
                                    
                                    <div class="last-message {{ $messageClass }}">
                                        <div class="message-meta">
                                            <i class="fal {{ $messageIcon }}"></i>
                                            <span class="font-weight-medium">{{ $authorName }}</span>
                                            <span>•</span>
                                            <span>{{ \Carbon\Carbon::parse($messageToShow['message_date'] ?? $messageToShow['date'])->format('d.m.Y H:i') }}</span>
                                        </div>
                                        <div class="message-content">
                                            {!! Str::limit(strip_tags($messageToShow['message']), 150) !!}
                                        </div>
                                    </div>
                                @else
                                    <!-- İlk mesaj varsa onu göster -->
                                    <div class="last-message user-message">
                                        <div class="message-meta">
                                            <i class="fal fa-user"></i>
                                            <span class="font-weight-medium">{{ $first['name'] ?? $first['user'] ?? 'Siz' }}</span>
                                            <span>•</span>
                                            <span>{{ \Carbon\Carbon::parse($first['message_date'] ?? $first['date'])->format('d.m.Y H:i') }}</span>
                                        </div>
                                        <div class="message-content">
                                            {!! Str::limit(strip_tags($first['message']), 150) !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                            @empty
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fal fa-comments fa-3x"></i>
                            </div>
                            <div class="empty-state-title">Henüz destek talebi bulunmuyor</div>
                            <div class="empty-state-description">
                                İlk destek talebinizi oluşturmak için yukarıdaki butona tıklayın.
                            </div>
                        </div>
                            @endforelse
                </div>
            </div>
        </div>
    </div>
</main>

<!-- New Support Modal -->
<div class="modal fade modal-modern" id="newSupportModal" tabindex="-1" role="dialog" aria-labelledby="newSupportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="{{ route('feedback.add') }}" class="form-modern">
      @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newSupportModalLabel">
                        <i class="fal fa-life-ring mr-2 text-primary"></i>
                        Yeni Destek Talebi
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="form-label-modern" for="feedback_type">Destek Tipi</label>
                                <select class="form-control-modern" name="feedback_type" id="feedback_type" required>
                <option value="">Seçiniz</option>
                @foreach($feedback_types as $type)
                                        <option value="{{ is_array($type) ? $type['id'] : $type->id }}">
                                            {{ is_array($type) ? $type['name'] : $type->name }}
                                        </option>
                @endforeach
              </select>
            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="form-label-modern" for="feedback_title">Başlık</label>
                                <input type="text" class="form-control-modern" name="feedback_title" id="feedback_title" 
                                       placeholder="Kısa bir başlık yazınız" maxlength="100" required>
                            </div>
            </div>
          </div>
                    
                    <div class="form-group-modern">
                        <label class="form-label-modern" for="feedback">Açıklama</label>
                        <textarea class="form-control-modern" name="feedback" id="feedback" rows="5" required 
                                  placeholder="Sorununuzu veya talebinizi detaylıca yazınız..."></textarea>
          </div>
        </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fal fa-times mr-1"></i> İptal
          </button>
                    <button type="submit" class="btn btn-success-modern btn-modern">
                        <i class="fal fa-paper-plane mr-1"></i> Gönder
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Feedback Detail Modals -->
@foreach($grouped as $id => $messages)
<div class="modal fade modal-modern" id="feedbackDetailModal{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="feedbackDetailModalLabel{{ $id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
                <h5 class="modal-title" id="feedbackDetailModalLabel{{ $id }}">
                    <i class="fal fa-comments mr-2 text-primary"></i>
                    Destek Detayı: {{ $messages->first()['konu'] }}
                </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
            
      <div class="modal-body">
                <div class="chat-container">
                    <table class="chat-table" id="chatMessages{{ $id }}">
                        <thead>
                            <tr>
                                <th width="20"></th>
                                <th width="120">Kişi</th>
                                <th width="140">Tarih</th>
                                <th>Mesaj</th>
                            </tr>
                        </thead>
                        <tbody>
          @foreach($messages as $msg)
                                @php
                                    // Basit logic: kim admin kim user
                                    $isAdminMessage = isset($msg['sender']) && ($msg['sender'] == 'users' || $msg['sender'] == 'user');
                                    $rowClass = $isAdminMessage ? 'message-row-admin' : 'message-row-user';
                                    $iconClass = $isAdminMessage ? 'fa-user' : 'fa-user-headset';
                                    
                                    // Mesaj tarihi kontrolü
                                    $messageDate = $msg['message_date'] ?? $msg['date'];
                                    $displayName = $msg['name'] ?? $msg['user'] ?? 'Bilinmeyen';
                                @endphp
                                
                                <tr class="{{ $rowClass }}" data-sender="{{ $msg['sender'] ?? 'admin' }}">
                                    <td class="message-icon">
                                        <i class="fal {{ $iconClass }}"></i>
                                    </td>
                                    <td class="message-author">{{ $displayName }}</td>
                                    <td class="message-time">{{ \Carbon\Carbon::parse($messageDate)->format('d.m.Y H:i') }}</td>
                                    <td class="message-content-cell">{!! $msg['message'] !!}</td>
                                </tr>
          @endforeach
                        </tbody>
                    </table>
        </div>
                    
        @if($messages->first()['durum'] != 2)
                                                 <form method="POST" action="{{ route('feedback.reply') }}" class="chat-input-container">
          @csrf
          <input type="hidden" name="feedback_id" value="{{ $id }}">
                             @php
                                 // API veri yapısına göre user_id'yi bul
                                 $replyUserId = null;
                                 foreach($messages as $msg) {
                                     if (isset($msg['user_id']) && $msg['user_id']) {
                                         $replyUserId = $msg['user_id'];
                                         break;
                                     }
                                 }
                             @endphp
                             @if($replyUserId)
                                 <input type="hidden" name="user_id" value="{{ $replyUserId }}">
          @endif
                            
                            <div class="d-flex gap-2">
                                <textarea id="message{{ $id }}" name="message" class="chat-input flex-grow-1" 
                                          placeholder="Yanıtınızı buraya yazın..." required></textarea>
                                <button type="submit" class="btn btn-primary-modern btn-modern" style="align-self: flex-end;">
                                    <i class="fal fa-paper-plane"></i>
                                </button>
          </div>
        </form>
                    @else
                        <div class="alert alert-warning border-0 text-center" style="background: #fef3c7; border: 1px solid #f59e0b;">
                            <i class="fal fa-lock mr-2"></i>
                            Bu destek talebi kapatılmıştır. Yeni yanıt eklenemez.
                        </div>
        @endif
                </div>
      </div>
    </div>
  </div>
</div>
@endforeach

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@include('layouts.footer')

<script>
class FeedbackSystem {
    constructor() {
        this.activePoll = null;
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.setupModals();
    }
    
    bindEvents() {
        $(document).on('submit', 'form[action*="feedback.add"]', this.handleNewFeedback.bind(this));
        $(document).on('submit', 'form[action*="feedback.reply"]', this.handleReply.bind(this));
        $(document).on('shown.bs.modal', '.modal', this.handleModalShown.bind(this));
    }
    
    setupModals() {
        @foreach($grouped as $id => $messages)
        this.setupModal{{ $id }}();
        @endforeach
    }
    
  @foreach($grouped as $id => $messages)
    setupModal{{ $id }}() {
        const modal = $(`#feedbackDetailModal{{ $id }}`);
        const input = modal.find(`#message{{ $id }}`);
        
        modal.on('shown.bs.modal', () => {
            setTimeout(() => {
                this.scrollToBottom(modal.find('.chat-table')[0]);
                input.focus();
                this.startRealTimeUpdates({{ $id }});
            }, 300);
        });
        
        input.on('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
    }
    @endforeach
    
    async handleNewFeedback(event) {
        event.preventDefault();
        const form = $(event.target);
        const submitBtn = form.find('button[type="submit"]');
        
        this.setButtonLoading(submitBtn, true);
        
        try {
            const response = await fetch(form.attr('action'), {
                method: 'POST',
                body: new FormData(form[0]),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showNotification('Destek talebi başarıyla oluşturuldu!', 'success');
                form[0].reset();
                $('#newSupportModal').modal('hide');
                setTimeout(() => location.reload(), 1000);
            } else {
                this.showNotification(result.message || 'Bir hata oluştu', 'error');
            }
        } catch (error) {
            this.showNotification('Bağlantı hatası oluştu', 'error');
        } finally {
            this.setButtonLoading(submitBtn, false);
        }
    }
    
    async handleReply(event) {
        event.preventDefault();
        const form = $(event.target);
        const submitBtn = form.find('button[type="submit"]');
        const textarea = form.find('textarea');
        
        if (!textarea.val().trim()) {
            this.showNotification('Lütfen bir mesaj yazın', 'error');
            return;
        }
        
        this.setButtonLoading(submitBtn, true);
        
        try {
            const response = await fetch(form.attr('action'), {
                method: 'POST',
                body: new FormData(form[0]),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showNotification('Mesaj gönderildi!', 'success');
                textarea.val('').css('height', '60px');
            } else {
                this.showNotification(result.message || 'Mesaj gönderilemedi', 'error');
            }
        } catch (error) {
            this.showNotification('Bağlantı hatası oluştu', 'error');
        } finally {
            this.setButtonLoading(submitBtn, false);
        }
    }
    
    handleModalShown(event) {
        const modal = $(event.target);
        const chatContainer = modal.find('.chat-table');
        if (chatContainer.length) {
            setTimeout(() => this.scrollToBottom(chatContainer[0]), 100);
        }
    }
    
    setButtonLoading(button, loading) {
        if (loading) {
            button.prop('disabled', true).data('original-text', button.html())
                  .html('<i class="fal fa-spinner fa-spin mr-1"></i> Gönderiliyor...');
        } else {
            button.prop('disabled', false).html(button.data('original-text') || 'Gönder');
        }
    }
    
    scrollToBottom(element) {
        if (element) element.scrollTop = element.scrollHeight;
    }
    
    showNotification(message, type = 'success') {
        const notification = $(`<div class="notification ${type}">
            <i class="fal fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} mr-2"></i>${message}
        </div>`);
        $('body').append(notification);
        setTimeout(() => notification.fadeOut(300, () => notification.remove()), 4000);
    }
}

document.addEventListener('DOMContentLoaded', () => new FeedbackSystem());
</script>

