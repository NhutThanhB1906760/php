<?php
/*

@ lqthinh
@ 11/2022
 
  
*/
include_once __SITE_PATH . "/modules/com/list.php";
include_once __SITE_PATH . "/modules/com/form.php";
include_once __SITE_PATH . "/modules/com/search.php";

include_once __SITE_PATH . "/model/cusc_thinh_chuc_vu.php";


class thinhchucvuController extends AbstractController{

    private $form;
    private $searchMng;
    
    public function init() {
        
        // Tạo form search
        $this->searchMng = new SearchManager('frmMain');
        $this->searchMng->setSortable(true);
        $this->searchMng->regCom('cusc_thinh_chuc_vu_ma', true, Array(__ERR_VALID_STR => ''), null, null, null, array('txt'));
      
        // Khởi tạo form thêm, cập nhật
        $this->form = new SimpleFloatForm(__APP_CONTROLLER, 'Thêm mới chức vụ');
        $this->form->addRowAsCom('cusc_thinh_chuc_vu_ma', Array('txt'), NULL, NULL, "class='notered'");
        $this->form->addRowAsCom('cusc_thinh_chuc_vu_ten_vn', Array('txt'), NULL, NULL, "class='notered'");
        $this->form->addSubmitRow(UIHelper::Button("btnSave", "bt_" . __APP_CONTROLLER . "_submit", "Thực hiện", "onclick='thucHienThem()'"));
        
        
    }
    
    public function index() {
        
        $modelChucVu = new CUSC_THINH_CHUC_VU();
        
        // Lấy thông tin từ form
        $this->searchMng->setValue($this->objectsValue);
        $sort = $this->searchMng->getSort();
        $search = $this->searchMng->getSearch();
        
        //Thực hiện phân trang
        $total = $modelChucVu->getTotalDataChucVu($search);
        $paging = (PagingHelper::getCurPage($this->objectsValue, __APP_CONTROLLER, $total));
        $curPage = $paging['cur_page'];
        $offset  = $paging['offset'];
        $limit   = $paging['limit'];
        
        // Tạo biến data lấy kết quả tìm kiếm
        $data    = $modelChucVu->getDataChucVu($search, $sort, $offset, $limit);
        $params  = new SListParams();
        $params->name = __APP_CONTROLLER;
        $params->offset = $offset;
        $params->data = $data;
        
        // Ẩn cột Id và tên
        $params->hide_cols = Array('cusc_thinh_chuc_vu_id' => '');
        
        // Khai báo cột Id dùng trong chức năng sửa
        $params->id_col = Array('cusc_thinh_chuc_vu_id');
        
        // Hiển thị chức năng sửa
        $params->edit_col = UserInfo::checkPermission("edit");
        
        // CSS align một số cột
        $params->col_options = Array('cusc_thinh_chuc_vu_ma'      => Array('class' => 'bold center'));
        
        // Hiển thị danh sách (list)
        $list = SList::getHTML($params);
        
        // Form thêm, cập nhật
        $this->registry->template->form = $this->form->getHTML();

         // Hiển thị các chức năng thêm phía trên và dưới danh sách
        $this->registry->template->paging_top = PagingHelper::getHTML('frmMain', __APP_CONTROLLER, $curPage, $total, false);
        $this->registry->template->paging_bottom = PagingHelper::getHTML('frmMain', __APP_CONTROLLER, $curPage, $total, true, null);
        
        // Danh sách trên trang index
        $this->registry->template->list = $list;
        $this->registry->template->title = 'Tự điển nhân sự - chức vụ';
        $this->registry->template->data = $this->searchMng->getHTML();
        $this->registry->template->show(__APP_CONTROLLER . '_index');
    }
    
// --------------------------------------------------- START ADD PROCESS    
 
    // Hàm kiểm tra dữ liệu trước khi thực hiện thêm data
    public function validate_add() {
        
        $dbHelper = DBHelper::getInstance();
        
        // Lấy mã và tên chức vụ
        $maChucVu = $this->objectsValue[$this->form->getNameHTML('cusc_thinh_chuc_vu_ma')];
        $tenChucVu = $this->objectsValue[$this->form->getNameHTML('cusc_thinh_chuc_vu_ten_vn')];

        // Kiểm tra thông tin mã chức vụ
        MsgHelper::checkValue($dbHelper->getDBInfo('cusc_thinh_chuc_vu_ma')->getTitle(), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $maChucVu);
        
        if(!MsgHelper::hasError()){
            $modelChucVu = new CUSC_THINH_CHUC_VU();
            // Kiểm tra mã chức vụ đã tồn tại trong csdl chưa
            if($modelChucVu->check(Array('cusc_thinh_chuc_vu_ma' => $maChucVu))){
                 MsgHelper::addErrByCode(__ERR_EXIST, __ERR_EXIST, $dbHelper->getDBInfo('cusc_thinh_chuc_vu_ma')->getTitle());
            }
        }
        
        // Kiểm tra thông tin tên chức vụ
        MsgHelper::checkValue($dbHelper->getDBInfo('cusc_thinh_chuc_vu_ten_vn')->getTitle(), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $tenChucVu);

        return !MsgHelper::hasError();
        
    }
    
    // Hàm thêm dữ liệu sau khi đã kiểm tra
    public function process_add() {
        
        // Lấy thông tin mã và tên chức vụ
        $maChucVu  = $this->objectsValue[$this->form->getNameHTML('cusc_thinh_chuc_vu_ma')];
        $tenChucVu = $this->objectsValue[$this->form->getNameHTML('cusc_thinh_chuc_vu_ten_vn')];

        
        $modelChucVu = new CUSC_THINH_CHUC_VU();
        $data = Array(
            'cusc_thinh_chuc_vu_ma'     => Util::upperString($maChucVu),
            'cusc_thinh_chuc_vu_ten'    => Util::stripUnicode($tenChucVu),
            'cusc_thinh_chuc_vu_ten_vn' => $tenChucVu    
        );
        
        // Insert dữ liệu vào Database
        $modelChucVu->insert($data);
        
    }
    
    // Hàm thông báo việc thêm data
    public function add() {
        $xml = '';
        if (!MsgHelper::hasError()) {
            $xml = '<msg>OK</msg>';
        } else {
            $xml = '<msg>' . self::encodeXMLString(MsgHelper::getHTML()) . '</msg>';
        }
        $this->registry->template->showXML($xml);
    }

// --------------------------------------------------- END ADD PROCESS

// --------------------------------------------------- START EDIT PROCESS

     // Hàm public_get_info lấy id công đoàn truyền về file index tạo xml
     public function public_get_info() {
         
        $id = isset($this->objectsValue['id']) ? $this->objectsValue['id'] : "";
        $modelChucVu = new CUSC_THINH_CHUC_VU();
        $data = $modelChucVu->getMaChucVuById($id);
        $this->registry->template->showXML(self::putXMLString($data));
    }
        
    // Hàm kiểm tra thông tin chỉnh sửa
    public function validate_edit() {
        
        $dbHelper = DBHelper::getInstance();

        // Lấy thông tin các trường cần edit
        $tenChucVu = $this->objectsValue[$this->form->getNameHTML('cusc_thinh_chuc_vu_ten_vn')];
        
        // Kiểm tra thông tin các trường cần edit
        MsgHelper::checkValue($dbHelper->getDBInfo('cusc_thinh_chuc_vu_ten_vn')->getTitle(), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $tenChucVu);

        return !MsgHelper::hasError();
    }
    
    // Hàm chỉnh sửa thông tin
    public function process_edit() {
        
        // Lấy Id 
        $idChucVu = $this->objectsValue['h_thinhchucvu_id']; 

        // Lấy tên
        $tenChucVu = $this->objectsValue[$this->form->getNameHTML('cusc_thinh_chuc_vu_ten_vn')];
  
        // Khởi tạo model
        $modelChucVu = new CUSC_THINH_CHUC_VU();
        

        // Các câu lệnh sql
        $fields = "cusc_thinh_chuc_vu_ten = #b, cusc_thinh_chuc_vu_ten_vn = #c";
        $where = "cusc_thinh_chuc_vu_id = #z ";
        $params = Array(
                            'b' => Util::stripUnicode($tenChucVu),
                            'c' => $tenChucVu,    
                            'z' => $idChucVu
                                 );
        
        // Cập nhật dữ liệu vào bảng Công đoàn
        $modelChucVu->update($fields, $where, $params);
    }
    
    // Hàm thông báo kết quả của quá trình chỉnh sửa
    public function edit() {
        $xml = '';
        if (!MsgHelper::hasError()) {
            $xml = '<msg>OK</msg>';
        } else {
            $xml = '<msg>' . self::encodeXMLString(MsgHelper::getHTML()) . '</msg>';
        }
        $this->registry->template->showXML($xml);
    }
    
// --------------------------------------------------- END EDIT PROCESS
    
// ---------------------------------------------------- START PRINT-EXCEL PROCESS
    // Hàm dùng in dữ liệu
    public function public_in() {
        $this->process_in_xuat(TRUE);
        
    }

    // Hàm dùng xuất excel
    public function public_excel() {
        $this->process_in_xuat(FALSE);
    }

    // Hàm dùng để in và xuất
    public function process_in_xuat($in) {

        //Khai báo thư viện
        $print = new PrintHelper('main', __PRINT_TYPE_A4);


        // Lấy dữ liệu từ form tìm kiếm
        $this->searchMng->setValue($this->objectsValue);
        $search = $this->searchMng->getSearch();
        $sort = $this->searchMng->getSort();

        // Khởi tạo model sinh viên
        $modelChucVu = NEW CUSC_THINH_CHUC_VU();

        // Lấy dữ liệu trong list
        $list1 = $modelChucVu->getDataChucVu($search, $sort);


        // Bỏ cột id sinh viên khi in - xuất excel 
        $list = $print->removeFields(Array('cusc_thinh_chuc_vu_id'), $list1);

        // Không có dữ liệu
        if (empty($list)) {
            $print->showNoDataMsg();
            $this->registry->template->export($print->getHTML());
            return;
        }

        // Nếu có dữ liệu
        $keys = $list[0];

        // CSS một vài thuộc tính để in
        $keys['cusc_thinh_chuc_vu_ma'] = Array('align' => 'center');
        
       // Xây dựng các thuộc tính footer
        $footertitle[] = Array(Array('html' => '&nbsp;', 'align' => 'left', 'class' => 'sign', 'colspan'=> 1, 'width'=>'50%'),Array('html' => '<br>'.'</br>'.__PROVINCE.', ngày '.date('d').' tháng '.date('m').' năm '.date('Y'),'align' => 'right', 'class' => 'italic','nowrap'=>'nowrap', 'colspan'=> 2, 'style'=>'padding-right:10px')
);
        $footertitle[] = Array(Array('html' =>'<b></u></b>',  'align' => 'center','nowrap'=>'nowrap', 'colspan'=>2),Array('html' =>'<b>NGƯỜI LẬP BẢNG</u></b>',  'align' => 'right','nowrap'=>'nowrap', 'colspan'=> 1, 'style'=>'padding-right:46px'));
        
        $footertitle[] = Array(Array('html' => '<br><br><br><br><br><br><br><br>', 'align' => 'center', 'class' => 'sign'));

        // In hoặc xuất excel
        $print->addMainTitle(PrintCom::makeTitle(true, 'Tự Điển Chức vụ', NULL));
        $print->addBody(SimpleList::makeHeader($list[0], NULL, NULL, NULL, TRUE), $list, $keys);
        $print->addFooterNote($footertitle);

        if ($in) {
            $this->registry->template->export($print->getHTML());
        } else {
            $this->registry->template->export($print->getHTML(true), __EXPORT_TYPE_EXCEL, __APP_CONTROLLER);
        }
    }

// ---------------------------------------------------- END PRINT-EXCEL PROCESS    
}