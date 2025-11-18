@extends('layouts.AdminDashBoard')
@section('content')
 <div class="row">
                    <div class="col-12">
                        <div class="dashboard-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Quản lý danh mục</h5>
                                <a class="btn btn-sm btn-success" href="{{ route('admin.categories.create') }}">
                                    <i class="fas fa-plus"></i> Thêm Danh mục
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($categories as $category)
                                    <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                                        <div class="card category-card">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">{{ $category->name }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">{{ $category->description }}</p>
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-flex justify-content-between">
                                                    <a href="{{ route('admin.categories.edit', $category->id) }}"
                                                        class="btn btn-primary btn-sm btn-action">
                                                        <i class="fas fa-edit"></i> Sửa
                                                    </a>
                                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm btn-action">
                                                            <i class="fas fa-trash"></i> Xóa
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
@endsection