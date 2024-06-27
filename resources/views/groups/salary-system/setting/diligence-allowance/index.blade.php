@extends('layouts.dashboard')

@section('content')
<di>
    <div>
        <div class="container-fluid">
            <div class="title-header">
                <div>
                    <h3 class="m-0">เบี้ยขยัน</h3>
                </div>
                <div aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active">เบี้ยขยัน</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            @if ($permission->show)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">รายละเอียด</h4>
                        </div>
                        <div class="table-responsive" id="table_container">
                            <table class="table table-borderless text-nowrap">
                                <thead class="border-bottom">
                                    <tr>
                                        <th>#</th>
                                        <th>รายการเบี้ยขยัน</th>
                                        <th class="text-end">เพิ่มเติม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($diligenceAllowances as $key => $diligenceAllowance)
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{$diligenceAllowance->name}}</td>
                                        <td class="text-end">
                                            <a class="btn btn-action btn-links btn-sm"
                                                href="{{route('groups.salary-system.setting.diligence-allowance.assignment',['id' => $diligenceAllowance->id])}}">
                                                <i class="fas fa-link"></i>
                                            </a>
                                            {{-- @if ($permission->update)
                                            <a class="btn btn-action btn-edit btn-sm"
                                                href="{{route('groups.salary-system.salary.diligence-allowance.view',['id' => $diligenceAllowance->id])}}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            @endif
                                            @if ($permission->delete)
                                            <a class="btn btn-action btn-delete btn-sm"
                                                data-confirm='ลบรายการเบี้ยขยัน "{{$diligenceAllowance->name}}" หรือไม่?'
                                                href="#" data-id="{{$diligenceAllowance->id}}"
                                                data-delete-route="{{ route('groups.salary-system.salary.diligence-allowance.delete', ['id' => '__id__']) }}"
                                                data-message="รายการเบี้ยขยัน">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            @endif --}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</di>
@push('scripts')
<script src="{{asset('assets/js/helpers/helper.js?v=1')}}"></script>

@endpush
@endsection