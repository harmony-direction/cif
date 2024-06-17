@extends('layouts.setting-dashboard')
@push('styles')

@endpush
@section('content')
<div>
    <div>
        <div class="container-fluid">
            <div class="title-header">
                <div>
                    <h3 class="m-0">ตำแหน่ง</h3>
                </div>
                <div>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active">ตำแหน่ง</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">รายชื่อตำแหน่ง</h3>
                            <a class="btn btn-header" href="{{route('setting.general.user-position.create')}}">
                                <i class="fas fa-plus">
                                </i>
                                เพิ่มตำแหน่ง
                            </a>
                        </div>
                        <div>
                            <div class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                <thead class="border-bottom">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>ตำแหน่ง</th>

                                                        <th class="text-end">เพิ่มเติม</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($userPositions as $userPosition)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{$userPosition->name}}</td>
                                                        <td class="text-end">
                                                            <a class="btn btn-action btn-edit btn-sm"
                                                                href="{{ route('setting.general.user-position.view', ['id' => $userPosition->id]) }}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            <a class="btn btn-action btn-delete btn-sm"
                                                                data-confirm='ลบตำแหน่ง "{{$userPosition->name}}" หรือไม่?'
                                                                href="#" data-id="{{$userPosition->id}}"
                                                                data-delete-route="{{ route('setting.general.user-position.delete', ['id' => '__id__']) }}"
                                                                data-message="ตำแหน่ง">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{asset('assets/js/helpers/helper.js?v=1')}}"></script>
@endpush
@endsection
