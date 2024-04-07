<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    {{-- Css --}}
    <link href="{{ asset('/css/report/sso2.css?v=2') }}" rel="stylesheet">
</head>

<body>
    <div id="pdfContent" class="container" style="padding: 10px;">
        <div class="wrap-header">
            <div class="wrap-title" style="width: 50%; float: left; font-size: 20px;">แบบรายการแสดงการส่งเงินสมทบ (สปส.1-10)(ตามแนวตั้ง)</div>
            <div class="wrap-number" style="width: 10%; float: right; text-align-last: right;">สปส.1-10 (ส่วนที่2)</div>
        </div>
        <div style="width: 65%; float: left;">
            <span style="font-size: 16px;">สำหรับค่าจ้างเงินเดือน    </span><span style="font-size: 16px;">{{ $month }} พ.ศ. {{ $year }}</span>
            <br>
            <span style="font-size: 16px;">ชื่อสถานประกอบการ     </span><span style="font-size: 16px; font-style: italic;">บริษัท ฉวีวรรณ อินเตอร์เนชั่นแนลฟู๊ดส์ จำกัด</span>
        </div>
        <div style="width: 23%; float: right;">
            <span>แผ่นที่     </span><span>{PAGENO}</span><span>     ในจำนวน     </span><span>{nb}</span>{{-- <span>     แผ่น</span> --}}
            <br>
            <span>เลขที่บัญชี</span><span>      </span><span>2000012019</span>
            <br>
            <span>ลำดับที่สาขา</span><span>   </span><span>000000</span>
        </div>
        <div style="width: 100%; display: block; ">
            <span>เพื่อประโยชน์ในการใช้สิทธิ์ขอรับประโยชน์ทดแทนของผู้ประกันตน ทุกครั้งที่นำส่งเงินสมทบ กรอกรายการให้ครบถ้วนถูกต้อง และชัดเจนด้วยลายมือตัวบรรจงหรือพิมพ์ดีด ให้แสดงรายการเฉพาะผู้ประกันตนที่มีค่าจ้าง ผู้ที่ไม่มีค่าจ้าง ไม่ต้องแสดง</span>
        </div>
        <table class="table-report">
            <thead>
                <tr style="border-bottom: 1px solid #000;">
                    <th style="border: 1px solid #000; text-align: center;">1</th>
                    <th style="border: 1px solid #000; text-align: center;">2</th>
                    <th style="border: 1px solid #000; text-align: center;">3</th>
                    <th style="border: 1px solid #000; text-align: center;">4</th>
                    <th style="border: 1px solid #000; text-align: center;">5</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #000;">
                    <th style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">ลำดับที่</th>
                    <th style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">เลขประจำตัวประชาชน</th>
                    <th style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">คำนำหน้าบนชื่อ-ชื่อสกุลผู้ประกันตน</th>
                    <th style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">ค่าจ้าง<br>(ไม่ต่ำกว่า 1,650 -<br>ไม่เกิน 15,000)</th>
                    <th style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">เงินสมทบ<br>ผู้ประกันตน</th>
                </tr>
                {{-- foreach ตรงนี้ --}}
                @if(isset($data) && count($data)>=1)
                    @foreach($data as $key => $item)
                        <tr>
                            <td class="text-center" style="border-left: 1px solid #000;">{{$key+1}}</td>
                            <td style="border-left: 1px solid #000; padding-left:10px;">{{ $item->hid }}</td>
                            <td style="border-left: 1px solid #000; padding-left:10px;">{{ $item->prefix->name.' '.$item->name.' '.$item->lastname }}</td>
                            <td class="text-center" style="border-left: 1px solid #000;">{{ $item->salarySummary($item->id)['salary'] }}</td>
                            <td class="text-center" style="border-left: 1px solid #000;">{{ $item->salarySummary($item->id)['socialSecurityFivePercent'] }}</td>
                        </tr>
                    @endforeach
                @else
                        <tr>
                            <td class="text-center" style="border-left: 1px solid #000;" rowspan="5">ไม่มีข้อมูล</td>
                        </tr>
                @endif
                {{-- End foreach ตรงนี้ --}}
            </tbody>
        </table>
    </div>
</body>

</html>
