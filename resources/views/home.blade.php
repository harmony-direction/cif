@extends('layouts.home')

@section('content')
<main class="container-fluid px-3">
    <header class="d-flex justify-content-between align-items-center mt-4 mb-3 ">
        <h2 class="m-0">สวัสดี {{Auth::user()->name}}</h2>
        <p class="text-muted m-0">{{$formatDated}}</p>
    </header>
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-bs-dismiss="alert" aria-hidden="true" aria-label="Close"></button>
        <h5>ผิดพลาด!</h5>
        {{ session('error') }}
    </div>
    @endif

    <div class="row">
        @foreach ($groups as $group)
        <div class="col-sm-6 col-lg-4 px-2 mb-3">
            <div class="card card-body border-0">
                <div>
                    <h5 class="m-0 d-flex gap-3 align-items-center">
                        {!! $group->icon !!}
                        {{ $group->name}}
                    </h5>
                </div>
                <div>
                    {{-- <a href="{{ route('group.index', $group->id) }}" class="btn btn-info"><i
                            class="fas fa-angle-double-right mr-2"></i>เข้าสู่ระบบงาน</a> --}}
                    <p class="my-3">{{ $group->description }}</p>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route($group->default_route) }}" class="btn btn-secondary d-flex gap-2 align-items-center" style="max-width: max-content">
                            <span class="material-symbols-outlined">
                                arrow_right_alt
                            </span>
                            เข้าสู่ระบบงาน
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @can('is_admin')
        <div class="col-sm-6 col-lg-4 px-2 mb-3">
            <div class="card card-body border-0">
                <div>
                    <h5 class="m-0 d-flex gap-3 align-items-center">
                        <span class="material-symbols-outlined" style="font-size: 36px; color: #1849A9;">
                            settings
                        </span>
                        ตั้งค่าระบบ
                    </h5>
                </div>
                <div>
                    <p class="my-3">ตั้งค่าระบบ</p>
                    <div class="d-flex justify-content-end">
                        <a href="{{route('setting')}}" class="btn btn-secondary d-flex gap-2 align-items-center" style="max-width: max-content">
                            <span class="material-symbols-outlined">
                                arrow_right_alt
                            </span>
                            เข้าสู่ระบบงาน
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>
</main>

@endsection
