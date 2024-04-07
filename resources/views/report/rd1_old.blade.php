@extends('layouts.app')

@section('content')
    {{-- Css --}}
    <link href="{{ asset('/css/report/report-5.css?v=2') }}" rel="stylesheet">

    <!-- Bootstrap CSS -->
    {{-- <link href="{{ asset('/bootstrap-5.3.2-dist/css/bootstrap.min.css?v=2') }}" rel="stylesheet"> --}}

    @php
        include '../vendor/autoload.php';
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                // __DIR__ . '/tmp',
                storage_path('fonts/'),
            ]),
            'fontdata' => $fontData + [
                'sarabun' => [
                    'R' => 'THSarabunNew.ttf',
                    'I' => 'THSarabunNew Italic.ttf',
                    'B' => 'THSarabunNew Bold.ttf',
                ]
            ],
            'default_font' => 'sarabun',
            'mode' => 'utf-8','format' => 'A4','margin_left' => 0,'margin_right' => 0,'margin_top' => 10,'margin_bottom' => 0,'margin_header' => 0,'margin_footer' => 0
        ]);

        ob_start();
    @endphp

    <div id="pdfContent" class="container" style="padding: 10px;">
        <div class="wrap-header" style="display: block; width: 100%;">
            <div style="display: block; width: 70%; float: left;">
                <span style="font-size: 16px; font-weight: bold;">เลขประจำตัวผู้เสียภาษีอากร(13หลัก)*</span>

                <span style="font-size: 12px; color: #474747;">(ของผู้มีหน้าที่หักภาษี ณ ที่จ่าย)</span>

                <span style="font-size: 15px; border: 1px solid #000;">        1234567890123        </span>
            </div>
            <div style="display: block; width: 15%; float: right;">
                <span style="font-size: 16px; font-weight: bold;">สาขาที่</span>

                <span style="font-size: 15px; border: 1px solid #000;">    0000    </span>
            </div>
            <div style="display: block; width: 70%; float: left;">
                <span style="font-size: 16px;">ใบแนบ</span>

                <span style="font-size: 16px; font-weight: bold;">ภ.ง.ด.1</span>
            </div>
            <div style="display: block; width: 28%; float: right;">
                <span style="font-size: 16px;">แผ่นที่</span>

                <span style="font-size: 16px;">999</span>

                <span style="font-size: 16px;">ในจำนวน</span>

                <span style="font-size: 16px;">999</span>

                <span style="font-size: 16px;">แผ่น</span>
            </div>

            <div class="box-header">
                <span style="font-size: 16px;">(ให้แยกกรอกรายการในใบแนบนี้ตามเงินได้แต่ละประเภท โดยใส่เครื่องหมาย “/” ลงใน</span>
                <input type="checkbox">
                <span style="font-size: 16px;">หน้าข้อความแล้วแต่กรณี เพียงข้อเดียว)</span>
                <div style="display: block; width: 50%; float: left;">
                    <span style="font-size: 16px; font-weight: bold;">ประเภทเงินได้</span>

                    <input type="checkbox">

                    <span style="font-size: 16px;">(1) เงินได้ตามมาตรา 40 (1) เงินเดือน ค่าจ้าง ฯลฯ กรณีทั่วไป</span>
                    <br>
                    <span style="font-size: 16px; font-weight: bold;">เงินได้</span>

                    <input type="checkbox">

                    <span style="font-size: 16px;">(2) เงินได้ตามมาตรา 40 (1) เงินเดือน ค่าจ้าง ฯลฯ</span>
                    <br>

                    <span style="font-size: 16px;">กรณีได้รับอนุมัติจากกรมสรรพากรให้หักอัตรา</span>    <span style="font-size: 16px; font-weight: bold;">ร้อยละ 3</span>
                </div>
                <div style="display: block; width: 50%; float: right;">
                    <input type="checkbox">

                    <span style="font-size: 16px;">(3) เงินได้ตามมาตรา 40 (1) (2) กรณีนายจ้างจ่ายให้ครั้งเดียวเพราะเหตุออกจากงาน</span>
                    <br>
                    <input type="checkbox">

                    <span style="font-size: 16px;">(4) เงินได้ตามมาตรา 40 (2) กรณีผู้รับเงินได้เป็นผู้อยู่ในประเทศไทย</span>
                    <br>
                    <input type="checkbox">

                    <span style="font-size: 16px;">(5) เงินได้ตามมาตรา 40 (2) กรณีผู้รับเงินได้มิได้เป็นผู้อยู่ในประเทศไทย</span>
                </div>
            </div>
            <table class="table-report">
                <thead>
                    <tr style="border-bottom: 1px solid #000;">
                        <td rowspan="4" style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">ลำ<br>ดับ<br>ที่</td>
                        <td style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">
                            เลขประจำตัวประชาชน <span style="font-size: 12px; color: #474747;">(ของผู้มีเงินได้)</span>
                        </td>
                        <td rowspan="4" style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">
                            วัน เดือน ปี
                            <br>
                            ที่เข้าทำงาน
                            <br>
                            (เฉพาะกรณี<br>เข้าทำงาน<br>ระหว่างปี)
                        </td>
                        <td colspan="4" style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">
                            รายละเอียด
                        </td>
                        <td rowspan="2" colspan="3" style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">
                            ประเภทรายได้พึงประเมินที่จ่าย
                            <br>
                            (รวมทั้งประโยชน์เพิ่มอย่างอื่น)
                            <br>
                            ถ้ามากกว่าหนึ่งประเภทให้กรอกเรียงลงไป
                        </td>
                        <td rowspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">
                            รวมเงินภาษีที่หักและ
                            <br>
                            นำส่งในปีที่ล่วงมาแล้ว
                        </td>
                        <td rowspan="4" style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">
                            เงื่อนไข*
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #000;">
                        <td style="border-left: 1px solid #000; text-align: center;">เลขประจำตัวผู้เสียภาษีอากร <span style="font-size: 12px; color: #474747;">(ของผู้มีเงินได้)</span></td>
                        <td rowspan="3" style="border-left: 1px solid #000; text-align: center; padding: 0px 7px;">มีสามี<br>ภริยา<br>หรือไม่</td>
                        <td colspan="2" style="border-left: 1px solid #000; text-align: center;">จำนวนบุตร</td>
                        <td style="border-left: 1px solid #000; text-align: center; padding: 0px 2px;">ค่าลดหย่อนอื่นๆ</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #000;">
                        <td rowspan="2" style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">ชื่อผู้มีเงินได้</span></td>
                        <td rowspan="2" style="border-left: 1px solid #000; text-align: center; vertical-align: middle; padding: 0px 2px;">ศึกษา</td>
                        <td rowspan="2" style="border-left: 1px solid #000; text-align: center; vertical-align: middle; padding: 0px 2px;">ไม่<br>ศึกษา</td>
                        <td style="border-left: 1px solid #000; text-align: center;">จำนวนเงิน</td>
                        <td rowspan="2" style="border-left: 1px solid #000; text-align: center; vertical-align: middle; padding: 0px 2px;">
                            ประเภทเงินได้ที่จ่าย
                        </td>
                        <td rowspan="2" style="border-left: 1px solid #000; text-align: center; vertical-align: middle; padding: 0px 2px;">
                            จำนวน
                            <br>
                            คราวที่จ่ายทั้งปี
                        </td>
                        <td style="border-left: 1px solid #000; text-align: center;">
                            จำนวนเงินที่จ่ายแต่ละประเภท
                            <br>
                            เฉพาะคนหนึ่งๆทั้งปี
                        </td>
                        <td style="border-left: 1px solid #000; text-align: center;">จำนวนเงิน</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #000;">
                        <td style="border-left: 1px solid #000; text-align: center;">บาท    สต.</td>
                        <td style="border-left: 1px solid #000; text-align: center;">บาท        สต.</td>
                        <td style="border-left: 1px solid #000; text-align: center;">บาท        สต.</td>
                    </tr>
                </thead>
                <tbody>
                    @for ($i=1; $i<=20; $i++)
                        <tr style="border-bottom: 1px solid #000;">
                            <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">{{$i}}</td>
                            <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">
                                <span>1234567890123</span>
                                <br>
                                <span>1234567890123</span>
                                <br>
                                <span>ชื่อ ทดสอบ ทดสอบ</span>
                            </td>
                            <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">20 มีนาคม 2567</td>
                            <td style="border-left: 1px solid #000; vertical-align: middle;">
                                <input type="checkbox"><label>(1) ไม่มี</label>
                                <br>
                                <input type="checkbox"><label>(2) มี</label>
                            </td>
                            <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">0</td>
                            <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">0</td>
                            <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">9,000.00</td>
                            <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">เงินเดือน</td>
                            <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">(4)</td>
                            <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">90,000.00</td>
                            <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">2,000.00</td>
                            <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">(1)</td>
                        </tr>
                    @endfor
                    <tr style="border: 1px solid #000;">
                        <td colspan="9">

                            <span style="font-size: 12px; font-style: italic; color: #474747;">(ให้กรอกลำดับที่ต่อเนื่องกันทุกแผ่น)</span>

                            <span style="font-size: 12px; color: #474747;">รวมยอดเงินได้และภาษีนำส่ง</span>

                            <span style="font-size: 12px; font-style: italic; color: #474747;">(นำไปรวมกับ</span>

                            <span style="font-size: 12px; color: #474747; font-weight: bold;">ใบต่อ ภ.ง.ด. 1ก</span>

                            <span style="font-size: 12px; font-style: italic; color: #474747;">ฉบับอื่น (ถ้ามี))</span>
                        </td>
                        <td style="text-align: center; border: 1px solid #000;">7,837,278.00</td>
                        <td style="text-align: center; border: 1px solid #000;">302,541.00</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <div class="footer-box">
                <div style="width: 65%; float: left;">
                    <div style="font-size: 16px; font-weight: bold;">หมายเหตุ</div>
                    <span style="font-size: 12px; color: #474747;">1. ให้ระบุว่า "มี" หรือ "ไม่มี" สามีภริยา โดยใส่เครื่องหมาย / ลงใน <input type="checkbox"> หน้าข้อความแล้วแต่กรณี พร้อมทั้งกรอกจำนวนบุตรที่มีสิทธิหักลดหย่อนศึกษากี่คน</span>
                    <br>

                    <span style="font-size: 12px; color: #474747;">ไม่ศึกษากี่คน และยอดรวมจำนวนค่าลดหย่อนอื่นๆที่จ่าย ได้แก่ เบี้ยประกันชีวิต เงินสะสมฯ ดอกเบี้ยเงินกู้ยืมเพื่อซื้อ เช่าซื้อ หรือ สร้างอาคารอยู่อาศัย</span>
                    <br>

                    <span style="font-size: 12px; color: #474747;">และเงินสมทบ</span>
                    <br>
                    <span style="font-size: 12px; color: #474747;">2. ให้กรอกประเภทเงินที่จ่าย เช่น เงินเดือน ค่าจ้าง เบี้ยเลี้ยง โบนัส บำเหน็จ เงินค่าเช่าบ้าน ค่าธรรมเนียม ค่านายหน้า เบี้ยประชุม ค่าภาษีเงินได้ ฯลฯ</span>
                    <br>
                    <span style="font-size: 12px; color: #474747;">3. จำนวนคราวที่จ่ายทั้งปี ให้กรอกดังนี้     -จ่ายเป็นรายวัน กรอก 1     -จ่ายเป็นรายสัปดาห์ กรอก 2     -จ่ายเป็นรายปักษ์ กรอก 3</span>
                    <br>

                    <span style="font-size: 12px; color: #474747;">-จ่ายเป็นรายเดือน กรอก 4     -จ่ายเป็นจำนวนคราวไม่แน่นอน กรอก 5</span>
                    <br>
                    <span style="font-size: 12px; color: #474747;">4. เงื่อนไขการหักภาษี ให้กรอกดังนี้     -หัก ณ ที่จ่าย กรอก 1     -ออกให้ตลอดไป กรอก 2     -ออกให้ครั้งเดียว กรอก 3</span>
                </div>
                <div style="width: 35%; float: right;">
                    <div style="margin-top: 10px;">
                        <div style="width: 20%; float: left; text-align: center;">
                            <span style="font-size: 12px; color: #cccccc;">
                                ประทับตรา
                                <br>
                                นิติบุคคล
                                <br>
                                (ถ้ามี)
                            </span>
                        </div>
                        <div style="width: 80%; float: right;">
                            <span style="font-size: 12px;">ลงชื่อ   </span>
                            <span style="border-bottom: 1px dotted #000;">         ช่องใส่ result         </span>
                            <span style="font-size: 12px;">   ผู้จ่ายเงิน</span>
                            <div style="margin: 3px 0px;"></div>
                            <div>

                                <span style="font-size: 15px; border-bottom: 1px dotted #000;">นายทดสอบ ทดสอบ</span>
                            </div>
                            <span style="font-size: 12px;">ตำแหน่ง   </span>
                            <span style="border-bottom: 1px dotted #000;">         ช่องใส่ result         </span>
                            <br>
                            <span style="font-size: 12px;">ยื่นแบบวันที่   </span>
                            <span style="border-bottom: 1px dotted #000;">24<span>   เดือน   </span>กุมภาพันธ์<span>   พ.ศ.   </span>2567</span>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <table class="table-report">
                <tbody>
                    <tr style="border-bottom: 1px solid #000;">
                        <td rowspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">ลำดับ<br>ที่</td>
                        <td style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">
                            เลขประจำตัวผู้เสียภาษีอากร <span style="font-size: 12px; color: #474747;">(ของผู้มีหน้าที่หักภาษี ณ ที่จ่าย)</span>
                        </td>
                        <td colspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">
                            รายละเอียดเกี่ยวกับการจ่ายเงิน
                        </td>
                        <td rowspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">
                            จำนวนเงินภาษี<br>ที่หักและนำส่งในครั้งนี้
                        </td>
                        <td rowspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle; padding: 2px;">
                            เงื่อนไข*
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #000;">
                        <td style="border-left: 1px solid #000; text-align: center;">ชื่อผู้มีเงินได้ <span style="font-size: 12px; color: #474747;">(ให้ระบุชัดเจนว่าเป็น นาย นาง นางสาว หรือยศ)</span></td>
                        <td style="border-left: 1px solid #000; text-align: center;">วัน เดือน ปี ที่จ่าย</td>
                        <td style="border-left: 1px solid #000; text-align: center;">จำนวนเงินได้ที่จ่ายในครั้งนี้</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">1</td>
                        <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">
                            <span>1234567890123</span>
                            <br>
                            <span>ชื่อ ทดสอบ ทดสอบ</span>
                        </td>
                        <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">20 มีนาคม 2567</td>
                        <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">90,000.00</td>
                        <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">2,000.00</td>
                        <td style="border-left: 1px solid #000; text-align: center; vertical-align: middle;">(1)</td>
                    </tr>
                </tbody>
            </table> --}}
        </div>
    </div>

    @php

        $html = ob_get_contents();
        $stylesheet = file_get_contents('css/report/report-5.css');
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($html,2);
        $pdfFilePath = "rd1-{{ $id }}.pdf";
        $mpdf->Output($pdfFilePath, 'F');
        ob_end_clean();

    @endphp

    <div class="container" style="display: block; width: 100%;">
        <a href="../../../{{ $pdfFilePath }}" target="_blank">
            <button id="viewPdfButton ">View PDF ภงด 1 แนวตั้ง</button>
        </a>
    </div>
    <script>
       document.getElementById("viewPdfButton").addEventListener("click", function() {
        var pdfFilePath = "<?= $pdfFilePath ?>";
        window.open(pdfFilePath, "_blank");
        document.getElementById("pdfContent").style.display = "block";
    });
    </script>

@endsection
