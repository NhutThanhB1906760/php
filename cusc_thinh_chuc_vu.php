<?php
/*

@ lqthinh
@ 11/2022
 
  
*/
class CUSC_THINH_CHUC_VU extends AbstractModel{
    
    
    // Khai báo các trường của bảng
    protected $fields = Array(
        'cusc_thinh_chuc_vu_id'     => '',
        'cusc_thinh_chuc_vu_ma'     => '',
        'cusc_thinh_chuc_vu_ten'    => '',
        'cusc_thinh_chuc_vu_ten_vn' => ''
    );
    
    protected $pk = 'cusc_thinh_chuc_vu_id';
    protected $table_name = 'cusc_thinh_chuc_vu';
    
    // Hàm đếm số lượng record
    public function getTotalDataChucVu($search) {
     
        $fields = " cusc_thinh_chuc_vu_id ";
        $tables = $this->table_name;
        $wheres = $this->makeWhereClause($search);
        $where = NULL;
        $params = NULL;

        if ($wheres) {
            $where = " WHERE " . $wheres['where'];
            $params = $wheres['params'];
        }

        return $this->countData($fields, $tables, $where, $params);
        
    }
    
    // Hàm lấy data chức vụ
    public function getDataChucVu($search, $sort, $offset = NULL, $limit = NULL){
        
        $fields = " cusc_thinh_chuc_vu_id,
                    cusc_thinh_chuc_vu_ma,
                    cusc_thinh_chuc_vu_ten_vn
        ";
        $tables = $this->table_name;
        
        $wheres = $this->makeWhereClause($search);
        $where = NULL;
        $params = NULL;

        if ($wheres) {
            $where = " WHERE " . $wheres['where'];
            $params = $wheres['params'];
        }
        
        return $this->load($fields, $tables, $where, $params, $sort, $offset, $limit);
    }
    
    // Hàm lấy mã chức vụ thông qua Id chức vụ
    public function getMaChucVuById($idChucVu) {
        
            $fields = " 
                    cusc_thinh_chuc_vu_id,
                    cusc_thinh_chuc_vu_ma,
                    cusc_thinh_chuc_vu_ten_vn
                    ";

        $tables = $this->table_name;
        $where = " WHERE cusc_thinh_chuc_vu_id = #z";
        $params = Array('z' => $idChucVu);
        
        return $this->load($fields, $tables, $where, $params);
        
    }
    
    // Hàm lấy id phòng ban bằng mã phòng ban
    public function getIdChucVuByMa($ma){
        $fields = "cusc_thinh_chuc_vu_id";
        $tables = $this->table_name;
        $wheres = " WHERE cusc_thinh_chuc_vu_ma = #a ";
        $params = Array('a' => $ma);
        return $this->load($fields, $tables, $wheres, $params);
    }
    
    
    // Hàm lấy id chức vụ bằng tên chức vụ (vn)
    public function getIdChucVuByTen($tenChucVu){
        $fields = "cusc_thinh_chuc_vu_id";
        $tables = $this->table_name;
        $wheres = " WHERE cusc_thinh_chuc_vu_ten_vn = #a ";
        $params = Array('a' => $tenChucVu);
        return $this->load($fields, $tables, $wheres, $params);
    }
   

}