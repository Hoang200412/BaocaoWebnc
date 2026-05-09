@extends('project_1.admin.layouts.layout')
 
@section('content')
<div class="main-content">
    <div class="admin-page-shell m-3">

        <div class="admin-page-header">
            <h3><i class="fas fa-gear me-2"></i>Quản lý Setting</h3>
            <p>Quản lý cấu hình hệ thống.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="admin-card">
            <div class="admin-toolbar">
                <a href="{{route('settings.create')}}" class="btn-icon btn-add" data-bs-toggle="tooltip" data-bs-title="Thêm Setting">
                    <i class="fas fa-plus"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                       <tr>
                           <th>#</th>
                           <th>Tên</th>
                           <th>Value</th>
                           <th>Hành động</th>
                       </tr>
                    </thead>
                    <tbody>
                       @foreach ($settings as $setting)
                           <tr>
                               <td>{{$setting->id}}</td>
                               <td>{{$setting->name}}</td>
                               <td>{{$setting->value}}</td>
                               <td>
                                    <div class="admin-actions">
                                        <a class="btn-icon btn-view" href="{{ route('setting.edit', ['id' => $setting->id]) }}" data-bs-toggle="tooltip" data-bs-title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('setting.destroy', $setting->id) }}" method="POST" class="delete-form d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon btn-delete" data-bs-toggle="tooltip" data-bs-title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                           </tr>
                       @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3 border-top pt-3">
                {{$settings->links()}}
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Bạn có chắc chắn muốn xóa?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endsection
