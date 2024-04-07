<!DOCTYPE html>
<html lang="th">

<head>
    <link href="{{ asset('/css/report/report-4.css?v=2') }}" rel="stylesheet">
</head>

<body>
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
            'mode' => 'utf-8','format' => 'A4','margin_left' => 0,'margin_right' => 0,'margin_top' => 0,'margin_bottom' => 0,'margin_header' => 0,'margin_footer' => 0
        ]);

        ob_start();
        @endphp
    <div class="container" style="padding: 10px;">
        <div class="header" style="display: block; width: 100%; margin-top: 40px;">
            <div class="header-bg" style="width: 80%; float: left; background-color: #ebebeb; border-radius: 5px;">
                <div class="logo" style="width: 15%; float: left;">
                    <img src="https://upload.wikimedia.org/wikipedia/th/9/93/%E0%B8%95%E0%B8%A3%E0%B8%B2%E0%B8%81%E0%B8%A3%E0%B8%A1%E0%B8%AA%E0%B8%A3%E0%B8%A3%E0%B8%9E%E0%B8%B2%E0%B8%81%E0%B8%A3.png" alt="Logo">
                </div>
                <div class="header-title" style="width: 85%; float: right; text-align: center;">
                    <div style="font-size: 22px; font-weight: bold;">
                        แบบยื่นรายการภาษีเงินได้หัก ณ ที่จ่าย
                    </div>
                    <div style="font-size: 18px; font-weight: bold;">
                        ตามมาตรา 59 แห่งประมวลรัษฎากร
                    </div>
                    <div style="font-size: 14px;">
                        สำหรับหักภาษี ณ ที่จ่ายตามมาตรา 50 (1) กรณีจ่ายเงินได้พึงประเมินตามมาตรา 40 (1) (2) แห่งประมวลรัษฎากร
                    </div>
                </div>
            </div>
            <div style="display: block; width: 15%; float: right; padding: 5px; border: 2px solid #dedeff; text-align: center; border-radius: 5px; font-size: 54px; font-weight: bold;">
                ภ.ง.ด.1
            </div>
        </div>
        <div class="section-1" style="display: block; width: 100%; margin-top: 10px;">
            <div class="box-left" style="width: 50%; float: left; padding: 10px 0px 0px 0px; border-right: 1px solid #000;">
                <div style="border-bottom: 1px solid #000; padding-bottom: 15px;">
                    <span style="font-size: 16px; font-weight: bold;">เลขประจำตัวผู้เสียภาษีอากร(13หลัก)*</span>
                                
                    <span style="font-size: 15px; border-bottom: 1px dotted #000;">     0205538000999     </span>
                    <br>
                                    <span style="font-size: 12px; color: #474747;">(ของผู้มีหน้าที่หักภาษี ณ ที่จ่าย)</span>
                    <br>
                    <br>
                    <span style="font-size: 16px; font-weight: bold;">ชื่อผู้มีหน้าที่หักภาษี ณ ที่จ่าย (หน่วยงาน)</span>
                                
                    <span style="font-size: 16px;">สาขาที่</span>  
                    <span style="font-size: 15px; border: 1px solid #000;">  0000  </span>
                    <br>
                    <span style="font-size: 15px; border-bottom: 1px dotted #000; margin-bottom: 5px;">บริษัท ฉวีวรรณ อินเตอร์เนชั่นแนลฟู๊ดส์ จำกัด</span>
                    <br>
                    <span style="font-size: 16px; font-weight: bold;">ที่อยู่:  </span>
                    <span style="font-size: 15px; border-bottom: 1px dotted #000;">83/5 หมู่ 10 ตรอก/ซอย โปรดกรอกให้ตรงตามใบ ภพ. 20 ถนน - แขวง/ตำบล หนองขาม เขต/อำเภอ ศรีราชา จังหวัด ชลบุรี</span>
                    <br>
                    <div style="margin-top: 5px;"></div>
                    <span style="font-size: 16px;">รหัสไปรษณีย์</span>  
                    <span style="font-size: 15px; border: 1px solid #000;">  20230  </span>
                      
                    <span style="font-size: 16px;">โทรศัพท์:</span>
                    <span style="font-size: 15px; border-bottom: 1px dotted #000;">038111630</span>
                </div>
                <div style="clear: both;"></div>
                <div style="display: block; width: 100%; border-bottom: 1px solid #000; padding: 15px 0px 15px 0px;">
                    <div style="display: block; width: 50%; float: left; text-align: center;">
                        <input type="checkbox">    <label>(1) ยื่น<span style="font-weight: bold;">ปกติ</span></label>
                    </div>
                    <div style="display: block; width: 50%; float: left; text-align: center;">
                        <input type="checkbox">    <label>(2) ยื่น<span style="font-weight: bold;">ปกติ</span>เพิ่มเติม</label>
                        <span style="font-size: 15px; border: 1px solid #000;">     </span>
                    </div>
                </div>
            </div>
            <div class="box-right" style="width: 49%; float: left; padding: 10px 0px 0px 0px;">
                <div style="border-bottom: 1px solid #000; padding: 0px 0px 17px 10px;">
                    <span style="font-size: 16px;"><span style="font-weight: bold;">เดือน</span>ที่จ่ายเงินได้พึงประเมิน</span>
                    <br>
                    <span style="font-size: 15px;">(ให้ทำเครื่องหมาย "/" ลงใน <input type="checkbox">  <label>หน้าชื่อเดือน) {{ isset($month) ? $month:'' }} พ.ศ.</label></span>
                      
                    <span style="font-size: 15px; border-bottom: 1px dotted #000;">{{ isset($year) ? $year:'' }}</span>
                    <br>
                    <div style="display: block; width: 100%;">
                        <div style="display: block; width: 25%; float: left;">
                            <input type="checkbox">    <label>(1) มกราคม</label>
                            <br>
                            <input type="checkbox">    <label>(2) กุมภาพันธ์</label>
                            <br>
                            <input type="checkbox">    <label>(3) มีนาคม</label>
                        </div>
                        <div style="display: block; width: 23%; float: left;">
                            <input type="checkbox">    <label>(4) เมษายน</label>
                            <br>
                            <input type="checkbox">    <label>(5) พฤษภาคม</label>
                            <br>
                            <input type="checkbox">    <label>(6) มิถุนายน</label>
                        </div>
                        <div style="display: block; width: 23%; float: left;">
                            <input type="checkbox">    <label>(7) กรกฎาคม</label>
                            <br>
                            <input type="checkbox">    <label>(8) สิงหาคม</label>
                            <br>
                            <input type="checkbox">    <label>(9) กันยายน</label>
                        </div>
                        <div style="display: block; width: 25%; float: left;">
                            <input type="checkbox">    <label>(10) ตุลาคม</label>
                            <br>
                            <input type="checkbox">    <label>(11) พฤศจิกายน</label>
                            <br>
                            <input type="checkbox">    <label>(12) ธันวาคม</label>
                        </div>
                    </div>
                </div>
                <div style="border-bottom: 1px solid #000; padding: 0px 0px 15px 10px;">
                    <span style="font-size: 16px; color: #777777;">ใบเสร็จเล่มที่....................................เลขที่.............................</span>
                    <br>
                    <span style="font-size: 16px; color: #777777;">จำนวนเงิน.........................................................................บาท</span>
                    <div style="margin-top: 10px;"></div>
                    <span style="font-size: 16px; color: #777777;">ลงชื่อ.........................................................................ผู้รับเงิน</span>
                    <br>
                    <span style="font-size: 16px; color: #777777;">วันที่................................................................................</span>
                </div>
            </div>
            <div class="box-bottom" style="display: block; width: 100%; padding: 10px 30px; border-bottom: 1px solid #000;">
                <div style="display: block; width: 100%; margin-bottom: 20px;">
                    <div style="display: block; width: 40%; float: left;">
                        <span style="font-size: 16px">มีรายละเอียดการหักเป็นรายผู้มีเงินได้ ปรากฏตาม</span>
                        <span style="font-size: 14px; color: #777777; font-style: italic;">(ให้แสดงรายละเอียดใน<span style="font-weight: bold;">ใบแนบ ภ.ง.ด.1</span>หรือใน<span style="font-weight: bold;">สื่อบันทึกในระบบคอมพิวเตอร์อย่างใดอย่างหนึ่งเท่านั้น</span>)</span>
                    </div>
                    <div style="display: block; width: 60%; float: left;">
                        <input type="checkbox">    <label style="font-size: 16px;">ใบแนบ <span style="font-weight: bold;">ภ.ง.ด.1</span></label>
                        <span style="font-size: 16px">ที่แนบมาพร้อมนี้ :</span>
                                                        
                        <span style="font-size: 16px">จำนวน  </span>
                        <span style="font-size: 15px; border-bottom: 1px dotted #000;">    </span>
                        <span style="font-size: 16px">  แผ่น</span>
                        <br>
                        <input type="checkbox">    <span style="font-size: 16px; font-weight: bold;">สื่อบันทึกในระบบคอมพิวเตอร</span>
                        <span style="font-size: 16px">ที่แนบมาพร้อมนี้ :</span>
                                  
                        <span style="font-size: 16px">จำนวน  </span>
                        <span style="font-size: 15px; border-bottom: 1px dotted #000;">    </span>
                        <span style="font-size: 16px">  แผ่น</span>
                        <br>
                                <span style="font-size: 16px">(ตามหนังสือแสดงความประสงค์ฯ ทะเบียนรับเลขที่</span>
                        <span style="font-size: 15px; border-bottom: 1px dotted #000;">    </span>
                        <br>
                                <span style="font-size: 16px">หรือตามหนังสือข้อตกลงการใช้งานฯ เลขอ้างอิงการลงทะเบียน</span>
                        <span style="font-size: 15px; border-bottom: 1px dotted #000;">     )</span>
                    </div>
                </div>
                <div style="display: block; width: 100%;">
                    <table style="width: 100%">
                        <thead>
                            <tr>
                                <th style="width: 53%;">
                                    <div style="width: 100%; font-size: 18px; background-color: #ebebeb; text-align: center; width: 100%; border: none; border-radius: 8px;">
                                                                           สรุปรายการภาษีที่นำส่ง                                   
                                    </div>
                                </th>
                                <th style="width: 13%; text-align: center;">
                                    <div style="border: 1px solid #000; font-size: 18px;">    จำนวนราย    </div>
                                </th>
                                <th style="width: 16%; text-align: center;">
                                    <div style="border: 1px solid #000; font-size: 18px;">         เงินได้ทั้งสิ้น         </div>
                                </th>
                                <th style="width: 17%; text-align: center;">
                                    <div style="border: 1px solid #000; font-size: 18px;">      ภาษีที่นำส่งทั้งสิ้น      </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding: 2px 0px; font-size: 18px;">1. เงินได้ตาม<span style="font-weight: bold;">มาตรา 40 (1)</span> เงินเดือน ค่าจ้าง ฯลฯ กรณีทั่วไป</td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    {{ $data['employee'] }}
                                </td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    {{ number_format($data['sum_salary'],2) }}
                                </td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    {{ $data['sum_social_security'] }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" style="padding: 2px 0px; font-size: 18px;">2. เงินได้ตาม<span style="font-weight: bold;">มาตรา 40 (1)</span> เงินเดือน ค่าจ้าง ฯลฯ กรณีได้รับ</td>
                            </tr>
                            <tr>
                                <td colspan="4" style="padding: 2px 0px; font-size: 18px;">    อนุมัติจากกรมสรรพากรให้หักอัตรา<span style="font-weight: bold;">ร้อยละ 3</span></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 0px;">
                                        <span style="font-size: 18px; font-style: italic;">(ตามหนังสือที่  </span>
                                    <span style="font-size: 18px; border-bottom: 1px dotted #000;">    </span>
                                    <span style="font-size: 18px; font-style: italic;">  ลงวันที่  </span>
                                    <span style="font-size: 18px; border-bottom: 1px dotted #000;">                    )</span>
                                </td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 0px; font-size: 18px;">3. เงินได้ตาม<span style="font-weight: bold;">มาตรา 40 (1) (2)</span> กรณีนายจ้างจ่ายให้ครั้งเดียวเพราะเหตุออกจากงาน</td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 0px; font-size: 18px;">4. เงินได้ตาม<span style="font-weight: bold;">มาตรา 40 (2)</span> กรณีผู้รับเงินได้เป็นผู้อยู่ในประเทศไทย</td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 0px; font-size: 18px;">5. เงินได้ตาม<span style="font-weight: bold;">มาตรา40 (2)</span> กรณีผู้รับเงินได้มิได้เป็นผู้อยู่ในประเทศไทย</td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 0px; font-size: 18px; font-weight: bold;">6. รวม</td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="padding: 2px 0px; font-size: 18px;">7. เงินเพิ่ม (ถ้ามี)</td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="padding: 2px 0px; font-size: 18px;">8. <span style="font-weight: bold;">รวม</span>ยอดภาษีที่นำส่งทั้งสิ้น และเงินเพิ่ม <span style="font-weight: bold;">(6. + 7.)</span></td>
                                <td style="padding: 2px 0px; text-align: center; font-size: 18px;">
                                    0
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    {{-- <div style="display: block; width: 60%; float: left;">
                        <div style="font-size: 18px; background-color: #ebebeb; text-align: center; width: 100%; border: none; border-radius: 8px; margin-bottom: 10px;">สรุปรายการภาษีที่นำส่ง</div>
                        <span style="font-size: 16px;">1. เงินได้ตามมาตรา 40 (1) เงินเดือน ค่าจ้าง ฯลฯ กรณีทั่วไป</span>
                        <br>
                        <span style="font-size: 16px;">2. เงินได้ตามมาตรา 40 (1) เงินเดือน ค่าจ้าง ฯลฯ กรณีได้รับ</span>
                        <br>
                              <span style="font-size: 16px;">อนุมัติจากกรมสรรพากรให้หักอัตราร้อยละ 3</span>
                        <br>
                              <span style="font-size: 14px;">(ตามหนังสือที่</span>  
                        <span style="font-size: 14px; border-bottom: 1px dotted #000;">0 0</span>  
                        <span style="font-size: 14px;">ลงวันที่</span>  
                        <span style="font-size: 14px; border-bottom: 1px dotted #000;">18 มีนาคม พ.ศ. 2567)</span>
                        <br>
                        <span style="font-size: 16px;">3. เงินได้ตามมาตรา 40 (1) (2) กรณีนายจ้างจ่ายให้ครั้งเดียวเพราะเหตุออกจากงาน</span>
                        <br>
                        <span style="font-size: 16px;">4. เงินได้ตามมาตรา 40 (2) กรณีผู้รับเงินได้เป็นผู้อยู่ในประเทศไทย</span>
                        <br>
                        <span style="font-size: 16px;">5. เงินได้ตามมาตรา40 (2) กรณีผู้รับเงินได้มิได้เป็นผู้อยู่ในประเทศไทย</span>
                        <br>
                        <span style="font-size: 16px;">6. รวม</span>
                        <br>
                        <span style="font-size: 16px;">7. เงินเพิ่ม (ถ้ามี)</span>
                        <br>
                        <span style="font-size: 16px;">8. รวมยอดภาษีที่นำส่งทั้งสิ้น และเงินเพิ่ม (6. + 7.)</span>
                    </div>
                    <div style="display: block; width: 40%; float: left;">
                        <div class="head" style="display: block; width: 100%;">
                            <div style="width: 30%; float: left; border: 1px solid #000; text-align: center; border-radius: 8px;">
                                จำนวนราย
                            </div>
                            <div style="width: 33%; float: left; border: 1px solid #000; text-align: center; border-radius: 8px;">
                                เงินได้ทั้งสิ้น
                            </div>
                            <div style="width: 33%; float: left; border: 1px solid #000; text-align: center; border-radius: 8px;">
                                ภาษีที่นำส่งทั้งสิ้น
                            </div>
                        </div>
                        <div class="row" style="display: block; width: 100%;">
                            <div style="width: 30%; height: 30px; float: left; border: 1px solid #000; text-align: center;">
                                0
                            </div>
                            <div style="width: 33%; height: 30px; float: left; border: 1px solid #000; text-align: center;">
                                0
                            </div>
                            <div style="width: 33%; height: 30px; float: left; border: 1px solid #000; text-align: center;">
                                0
                            </div>
                            <div style="width: 30%; height: 30px; float: left; border: 1px solid #000; text-align: center;">
                                0
                            </div>
                            <div style="width: 33%; height: 30px; float: left; border: 1px solid #000; text-align: center;">
                                0
                            </div>
                            <div style="width: 33%; height: 30px; float: left; border: 1px solid #000; text-align: center;">
                                0
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
            <div style="display: block; width: 100%; padding: 10px 0px; border-bottom: 1px solid #000;">
                <div style="font-size: 16px; text-align: center; margin-bottom: 5px;">ข้าพเจ้าขอรับรองว่า รายการที่แจ้งไว้ข้างต้นนี้ เป็นรายการที่ถูกต้องและครบถ้วนทุกประการ</div>
                <div style="display: block; width: 100%;">
                    <div style="width: 80%; float: left; text-align: center; padding-left: 80px;">
                        <span style="font-size: 12px;">ลงชื่อ   </span>
                        <span style="border-bottom: 1px dotted #000;">                                    </span>
                        <span style="font-size: 12px;">   ผู้จ่ายเงิน</span>
                        <br>
                        <div style="margin-bottom: 8px;"></div>
                        <div>
                            (...........................................................................)
                        </div>
                        <span style="font-size: 12px;">ตำแหน่ง   </span>
                        <span style="border-bottom: 1px dotted #000;">                                    </span>
                        <br>
                        <span style="font-size: 12px;">ยื่นแบบวันที่   </span>
                        <span style="border-bottom: 1px dotted #000;">    <span>   เดือน   </span>                    <span>   พ.ศ.   </span>        </span>
                    </div>
                    <div style="width: 20%; float: right; text-align: center;">
                        <span style="font-size: 12px; color: #cccccc;">
                            ประทับตรา
                            <br>
                            นิติบุคคล
                            <br>
                            (ถ้ามี)
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

