@foreach($categories as $category)
<div class="modal fade" id="editCategoryModal{{$category->id}}" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Kategori Düzenle</h5>
                <button type="button" class="btn btn-sm text-white" data-bs-dismiss="modal" aria-label="Kapat" style="border: none; background: none;">
            <i class="fal fa-times"></i>
        </button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('categories.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3 text-center">
                        <img src="{{ asset('images/'.$category->image)}}" alt="user-image" class="rounded-circle" height="64" width="64">
                    </div>

                    <input type="hidden" name="id" value="{{$category->id}}">
                    <div class="mb-3">
                        <label for="name" class="form-label">Kategori Adı</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{$category->name}}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Kategori Açıklaması</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{$category->description}}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Kategori Resmi</label>
                        <input type="file" class="form-control" id="image" name="image">
                    </div>
                    <div class="mb-3">
                        <label for="productVisibility" class="form-label">Ürün Görünürlük Durumu</label>
                        <select class="custom-select" name="visible" required>
                            <option value="1" @if($category->visible == 1) selected @endif>Aktif</option>
                            <option value="0" @if($category->visible == 0) selected @endif>Pasif</option>
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
