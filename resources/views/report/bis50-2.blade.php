<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Include your CSS files -->
    <link href="{{ asset('/css/report-1.css?v=2') }}" rel="stylesheet">
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
        'debug' => true,
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

@endphp
    <div id="pdfContent" class="container">
        <div class="wrap-table" style="width: 100%; height: fit-content; border: 1px solid #000;">
            <div class="header" style="width: 100%; text-align: center;">
                <div style="font-size: 20px;">หนังสือรับรองการหักภาษี ณ ที่จ่าย</div>
                <div style="font-size: 12px;">
                    ตามมาตรา 50 ทวิแห่งประมวลผลรัษฎากร
                </div>
                <div style="display: block; width: 10%; float: right; font-size: 12px;">
                    เลขที่   {nb}
                </div>
            </div>
            <div class="wrap-body" style="width: 100%; padding: 0px 10px;">
                <div class="section" style="width: 100%; padding: 10px; border: 1px solid #000; border-radius: 10px; margin-bottom: 5px;">
                    <div style="float: left; width: 50%;">
                        <div style="font-size: 17px; font-weight: 500; color: #000;">ผู้มีหน้าที่หักภาษี ณ ที่จ่าย :</div>
                        <span style="font-size: 16px; color: #000;">ชื่อ      </span>
                        <span style="font-size: 15px; color: #000; border-bottom: 1px dotted #000;">บริษัท ฉวีวรรณ อินเตอร์เนชั่นแนลฟู๊ดส์ จำกัด</span>
                        <br>
                        <span style="font-size: 12px; color: #575757;">              (ให้ระบุว่าเป็น บุคคล นิติบุคคล บริษัท สมาคม หรือ คณะบุคคุล)</span>
                    </div>
                    <div style="float: right; width: 50%;">
                        <span style="font-size: 16px; color: #000;">เลขประจำตัวผู้เสียภาษีอากร(13หลัก)      </span>
                        <span style="font-size: 15px; min-width: 50mm; color: #000; border-bottom: 1px dotted #000;">     0205538000999     </span>
                        <br>
                        <span style="font-size: 16px; color: #000;">เลขประจำตัวผู้เสียภาษีอากร                 </span>
                        <span style="font-size: 15px; color: #000; border-bottom: 1px dotted #000;">     3032922364     </span>
                    </div>
                    <div style="display: block; width: 100%;">
                        <span style="font-size: 16px; color: #000;">ที่อยู่    </span>
                        <span style="font-size: 15px; color: #000; border-bottom: 1px dotted #000; display: inline-block; width: 100%;">83/5 ม.10 ตรอก/ซอย โปรดกรอกข้อมูลตามใบ ภพ.20 ถนน - แขวงตำบล หนองขาม เขต/อำเภอ ศรีราชา จังหวัด ชลบุรี</span>
                        <br>
                        <span style="font-size: 12px; color: #575757;">              (ให้ระบุ ชื่ออาคาร/หมู่บ้าน ห้องเลขที่ ชั้นที่ เลขที่ ตรอก/ซอย หมู่ที่ ถนน ตำบล/แขวง อำเภอ/เขต จังหวัด)</span>
                    </div>
                </div>
                <div class="section" style="width: 100%; padding: 10px; border: 1px solid #000; border-radius: 10px; margin-bottom: 5px; height: 180px;">
                    <div style="float: left; width: 50%;">
                        <div style="font-size: 17px; font-weight: 500; color: #000;">ผู้ถูกหักภาษี ณ ที่จ่าย :</div>
                        <span style="font-size: 16px; color: #000;">ชื่อ      </span>
                        <span style="font-size: 15px; color: #000; border-bottom: 1px dotted #000;">{{ (isset($data->name) ? $data->name:'').'  '.(isset($data->lastname) ? $data->lastname:'') }}</span>
                        <br>
                        <span style="font-size: 12px; color: #575757;">              (ให้ระบุว่าเป็น บุคคล นิติบุคคล บริษัท สมาคม หรือ คณะบุคคุล)</span>
                    </div>
                    <div style="float: right; width: 50%;">
                        <span style="font-size: 16px; color: #000;">เลขประจำตัวผู้เสียภาษีอากร(13หลัก)      </span>
                        <span style="font-size: 15px; color: #000; border-bottom: 1px dotted #000;">{{ $data->hid }}</span>
                        <br>
                        <span style="font-size: 16px; color: #000;">เลขประจำตัวผู้เสียภาษีอากร                 </span>
                        <span style="font-size: 15px; color: #000; border-bottom: 1px dotted #000;">{{-- 1016322131 --}}</span>
                    </div>
                    <div style="display: block; width: 100%;">
                        <span style="font-size: 16px; color: #000;">ที่อยู่    </span>
                        <span style="font-size: 15px; color: #000; border-bottom: 1px dotted #000;">{{ $data->address }}</span>
                        <br>
                        <span style="font-size: 12px; color: #575757;">              (ให้ระบุ ชื่ออาคาร/หมู่บ้าน ห้องเลขที่ ชั้นที่ เลขที่ ตรอก/ซอย หมู่ที่ ถนน ตำบล/แขวง อำเภอ/เขต จังหวัด)</span>
                    </div>
                    <div style="display: block; width: 100%;">
                        <div style="display: block; width: 5%; float: left;">
                            <span style="font-size: 16px; color: #000;">ลำดับที่    </span>
                        </div>
                        <div style="display: block; width: 95%; float: right;">
                            <div style="display: block; width: 20%; float: left;">
                                <span style="border: 1px solid #000; padding: 10px;">   {{ 1 }}   </span>
                                <span style="font-size: 16px; color: #000;">  ในแบบ</span>
                                <br>
                                <div style="font-size: 12px; color: #575757; text-align: center;">
                                    (ให้สามารถอ้างอิง หรือสอบถามได้ระหว่างลำดับที่ตามหนังสือรับรองฯ กับแบบยื่นรายการภาษีหัก ณ ที่จ่าย)
                                </div>
                            </div>
                            <div style="display: block; width: 20%; float: left;">
                                <input type="checkbox" checked>    <label>(1) ภ.ง.ด.1ก</label>
                                <br>
                                <input type="checkbox">    <label>(5) ภ.ง.ด.2ก</label>
                            </div>
                            <div style="display: block; width: 20%; float: left;">
                                <input type="checkbox">    <label>(2) ภ.ง.ด.1ก พิเศษ</label>
                                <br>
                                <input type="checkbox">    <label>(6) ภ.ง.ด.3ก</label>
                            </div>
                            <div style="display: block; width: 20%; float: left;">
                                <input type="checkbox">    <label>(3) ภ.ง.ด.2</label>
                                <br>
                                <input type="checkbox">    <label>(7) ภ.ง.ด.53</label>
                            </div>
                            <div style="display: block; width: 15%; float: left;">
                                <input type="checkbox">    <label>(4) ภ.ง.ด.3</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section" style="width: 100%; margin-bottom: 5px;">
                    <table class="table" style="width: 100%; border-color: #000; border-radius: 5px;">
                        <thead>
                            <tr>
                                <th scope="col" style="vertical-align: middle; text-align: center; padding: 10px; font-size: 18px;">ประเภทเงินได้พึงประเมินที่จ่าย</th>
                                <th scope="col" style="vertical-align: middle; text-align: center; padding: 10px; font-size: 18px;">วันเดือน<br>หรือปีภาษี ที่จ่าย</th>
                                <th scope="col" style="vertical-align: middle; text-align: center; padding: 10px; font-size: 18px;">จำนวนเงินที่จ่าย</th>
                                <th scope="col" style="vertical-align: middle; text-align: center; padding: 10px; font-size: 18px;">ภาษีที่หัก<br>และนำส่งไว้</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding: 8px 2px 2px 8px;">1. เงินเดือน ค่าจ้าง เบี้ยเลี้ยง โบนัส ฯลฯ ตามมาตรา 40 (1)</td>
                                <td style="padding: 8px 2px 2px 0px; text-align: center;">{{ isset($year) ? $year:'' }}</td>
                                <td style="padding: 8px 2px 2px 0px; text-align: center;">{{ isset($data->salarySummary($data->id)['salary']) ? $data->salarySummary($data->id)['salary']:'0' }}</td>
                                <td style="padding: 8px 2px 2px 0px; text-align: center;">{{-- 139,500.00 --}}0.00</td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 2px 2px 8px;">2. ค่าธรรมเนียม ค่านายหน้า ฯลฯ ตามมาตรา 40(2)</td>
                                <td style="padding: 2px; text-align: center;"></td>
                                <td style="padding: 2px;"></td>
                                <td style="padding: 2px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 2px 2px 8px;">3. ค่าแห่งลิขสิทธิ์ ฯลฯ ตามมาตรา 40(3)</td>
                                <td style="padding: 2px; text-align: center;"></td>
                                <td style="padding: 2px;"></td>
                                <td style="padding: 2px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 2px 2px 8px;">4. (ก) ดอกเบี้ย ฯลฯ ตามมตรา 40 (4)(3)</td>
                                <td style="padding: 2px; text-align: center;"></td>
                                <td style="padding: 2px;"></td>
                                <td style="padding: 2px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 2px 2px 8px;">    (ข) เงินปันผล ส่วนแบ่งกำไร ฯลฯ ตามมาตรา 40 (4) (ข)</td>
                                <td style="padding: 2px; text-align: center;"></td>
                                <td style="padding: 2px;"></td>
                                <td style="padding: 2px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 2px 2px 8px;">          (1) กรณีผู้ได้รับเงินปันผลได้รับเครดิตภาษี โดยจ่ายจากกำไรสุทธิของกิจการที่ต้องเสียภาษีเงินได้นิติบุคคลในอัตราดังนี้</td>
                                <td style="padding: 2px; text-align: center;"></td>
                                <td style="padding: 2px;"></td>
                                <td style="padding: 2px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 2px 2px 8px;">          (1.1) อัตราร้อยละ 30 ของกำไรสุทธิ</td>
                                <td style="padding: 2px; text-align: center;"></td>
                                <td style="padding: 2px;"></td>
                                <td style="padding: 2px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 2px 2px 8px;">          (1.2) อัตราร้อยละ 25 ของกำไรสุทธิ</td>
                                <td style="padding: 2px; text-align: center;"></td>
                                <td style="padding: 2px;"></td>
                                <td style="padding: 2px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 2px 2px 8px;">          (1.3) อัตราร้อยละ 20 ของกำไรสุทธิ</td>
                                <td style="padding: 2px; text-align: center;"></td>
                                <td style="padding: 2px;"></td>
                                <td style="padding: 2px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 2px 2px 8px;">          (1.4) อัตราอื่นๆ (ระบุ)<span style="font-size: 15px; color: #000; border-bottom: 1px dotted #000;">  {{-- 999 --}}  </span>ของกำไรสุทธิ</td>
                                <td style="padding: 2px; text-align: center;"></td>
                                <td style="padding: 2px;"></td>
                                <td style="padding: 2px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 2px 2px 8px;">          (2) กรณีผู้ได้รับเงินปันผลไม่ได้รับเครดิตภาษี เนื่องจากจ่ายจาก</td>
                                <td style="padding: 2px; text-align: center;"></td>
                                <td style="padding: 2px;"></td>
                                <td style="padding: 2px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 2px 2px 8px;">          (2.1) กำไรสุทธิของกิจการที่ได้รับยกเว้นภาษีเงินได้นิติบุคคล</td>
                                <td style="padding: 2px; text-align: center;"></td>
                                <td style="padding: 2px;"></td>
                                <td style="padding: 2px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 2px 2px 8px;">          (2.2) เงินปันผลหรือเงินส่วนแบ่งของกำไรที่ได้รับยกเว้นไม่ต้องนำมารวมคำนวณเป็นรายได้เพื่อเสียภาษีเงินได้นิติบุคคล</td>
                                <td style="padding: 2px; text-align: center;"></td>
                                <td style="padding: 2px;"></td>
                                <td style="padding: 2px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 2px 2px 8px;">          (2.3) กำไรสุทธิส่วนที่ได้หักผลขาดทุนสุทธิยกมาไม่เกิน 5 ปี ก่อนรอบระยะเวลาบัญชีปีปัจจุบัน</td>
                                <td style="padding: 2px; text-align: center;"></td>
                                <td style="padding: 2px;"></td>
                                <td style="padding: 2px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 2px 2px 8px;">          (2.4) กำไรที่รับรู้ทางบัญชีโดยวิธีส่วนได้เสีย (equity method)</td>
                                <td style="padding: 2px; text-align: center;"></td>
                                <td style="padding: 2px;"></td>
                                <td style="padding: 2px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 2px 2px 8px;">5. การจ่ายเงินได้ที่ต้องหักภาษี ณ ที่จ่าย ตามคำสั่งกรมสรรพากรที่ออกตามมาตรา 3 เตรส (ระบุ)<span style="font-size: 15px; color: #000; border-bottom: 1px dotted #000;">  {{-- ทดสอบระบุ --}}  </span></td>
                                <td style="padding: 2px; text-align: center;"></td>
                                <td style="padding: 2px;"></td>
                                <td style="padding: 2px;"></td>
                            </tr>
                            <tr style="border-bottom: 1px solid #000;">
                                <td style="padding: 2px 2px 8px 8px; border-bottom: 1px solid #000;">6. อื่นๆ (ระบุ)<span style="font-size: 15px; color: #000; border-bottom: 1px dotted #000;">  {{-- ทดสอบระบุ --}}  </span></td>
                                <td style="padding: 2px 0px 8px 0px; text-align: center;"></td>
                                <td style="padding: 2px 0px 8px 0px;"></td>
                                <td style="padding: 2px 0px 8px 0px;"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 2px; text-align: center;">รวมเงินที่จ่ายและภาษีที่หักนำส่ง</td>
                                <td style="padding: 2px; text-align: center; border: 1px solid #000;">{{ isset($data->salarySummary($data->id)['salary']) ? $data->salarySummary($data->id)['salary']:'0' }}</td>
                                <td style="padding: 2px; text-align: center; border: 1px solid #000;">{{-- 139,500.00 --}}</td>
                            </tr>
                            <tr>
                                <td colspan="4" style="padding: 2px 2px 2px 8px;">
                                    รวมเงินภาษีที่หักนำส่ง    (ตัวอักษร)    <span class="sumary-output">        หนึ่งแสนสามหมื่นเก้าพันห้าร้อยบาทถ้วน        </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="section" style="width: 100%; padding: 5px; border: 1px solid #000; border-radius: 10px; margin-bottom: 5px;">
                    <div style="width: 100%; display: block;">
                        <div style="display: inline-block; float: left; width: 8%;">
                            <span style="font-size: 16px;">เงินที่จ่ายเข้า</span>
                        </div>
                        <div style="display: inline-block; float: left; width: 38%;">
                            <span style="font-size: 16px;">กบข./กสจ./กองทุนสงเคราะห์ครูโรงเรียนเอกชน </span>
                            <span style="font-size: 15px; border-bottom: 1px dotted #000;">{{-- 999,999.00 --}} บาท</span>
                        </div>
                        <div style="display: inline-block; float: left; width: 27%;">
                            <span style="font-size: 16px;">กองทุนประกันสังคม </span>
                            <span style="font-size: 15px; border-bottom: 1px dotted #000;">{{ isset($data->salarySummary($data->id)['socialSecurityFivePercent']) ? $data->salarySummary($data->id)['socialSecurityFivePercent']:'0' }} บาท</span>
                        </div>
                        <div style="display: inline-block; left; width: 27%;">
                            <span style="font-size: 16px;">กองทุนสำรองเลี้ยงชีพ </span>
                            <span style="font-size: 15px; border-bottom: 1px dotted #000;">{{-- 999,990.00 --}} บาท</span>
                        </div>
                    </div>
                </div>
                <div class="section" style="width: 100%; padding: 5px; border: 1px solid #000; border-radius: 10px; margin-bottom: 5px;">
                    <div style="width: 100%; display: block;">
                        <div style="display: inline-block; float: left; width: 8%;">
                            <span style="font-size: 16px;">ผู้จ่ายเข้า</span>
                        </div>
                        <div style="display: block; width: 15%; float: left;">
                            <input type="checkbox">  <label>(1) หัก ณ ที่จ่าย</label>
                        </div>
                        <div style="display: block; width: 15%; float: left;">
                            <input type="checkbox">  <label>(2) ออกให้ตลอดไป</label>
                        </div>
                        <div style="display: block; width: 15%; float: left;">
                            <input type="checkbox">  <label>(3) ออกให้ครั้งเดียว</label>
                        </div>
                        <div style="display: block; width: 40%; float: left;">
                            <input type="checkbox">  <label>(4) อื่นๆ (ระบุ)</label><span style="border-bottom: 1px dotted #000;">  result อื่นๆ  </span>
                        </div>
                    </div>
                </div>
                <div style="width: 100%; display: block;">
                    <div style="width: 30%; height: 129px; padding: 10px; border: 1px solid #000; border-radius: 10px; float: left;">
                        <div style=" display: block; width: 100%;">
                            <div style="float: left; width: 20%;">
                                <span style="font-size: 16px;">คำเตือน</span>
                            </div>
                            <div style="float: left; width: 80%;">
                                <span style="font-size: 15px;">ผู้มีหน้าที่ออกหนังสือรับรองการหักภาษี ณ ที่จ่าย ฝ่าฝืนไม่ปฎิบัติตามมาตรา 50 ทวิ แห่งประมวลรัษฎากร ต้องรับโทษทางอาญาตามมาตรา 35 แห่งประมวลรัษฎากร</span>
                            </div>
                        </div>
                    </div>
                    <div style="width: 63%; padding: 10px; border: 1px solid #000; border-radius: 10px; float: right;">
                        <div style="width: 95%; display: block; text-align: center;">
                            <p>ขอรับรองว่าข้อความและตัวเลขดังกล่าวข้างต้นถูกต้องตรงกับความเป็นจริงทุกประการ</p>
                            <span>ลงชื่อ</span>
                            <span style="border-bottom: 1px dotted #000;">                                        {{-- {{ (isset($data->name) ? $data->name:'').'  '.(isset($data->lastname) ? $data->lastname:'') }} --}}    </span>
                            <span>ผู้จ่ายเงิน</span>
                            <br>
                            <div style="margin: 8px 0px 0px 0px;">
                                (...........................................................................)
                            </div>
                            <span>{{  now()->format('d/m/yy') }}</span>
                            <div style="display: block; width: 20%; float: right; font-size: 12px; color: rgb(141, 0, 0);">
                                ประทับตรา<br>นิติบุคคล (ถ้ามี)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
