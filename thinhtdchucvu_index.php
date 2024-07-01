<?php 
    print_r($form); 
    print_r($form_them_tu_file); 
?>
<form action="<?php echo __HOST_NAME . __APP_PATH . __APP_CONTROLLER; ?>" id = "frmMain" name = "frmMain" method = "post">
    <h2> <?php echo $title; ?></h2>
    <?php
        print_r($data);
        print_r($paging_top);
        print_r($list);
        print_r($paging_bottom);
  
    ?>
    <div class="control">
        <?php
        //Bổ sung nút thêm mới, thao tác có ảnh hưởng đến csdl nên cần truyền tên action phần quyền : add
        echo UIHelper::Button("btnAdd", "btnAdd", "Thêm mới", "onclick='prepareThem();'", 'add');
        
        echo UIHelper::Button('btnAdd', 'btnAdd', 'Thêm từ file', "onclick='prepareThemTuFile();showModal(\"dlg_form_them_tu_file\")'", "themtufile");
//                
        echo UIHelper::Button('btnDelete', 'btnDelete', 'Xóa', "onclick='processCheckList(\"frmMain\",\"" . __APP_CONTROLLER . "/delete\",\"" . __APP_CONTROLLER . "\")'", 'delete');
//
        echo UIHelper::Button("btnPrint", "btnPrint", "In", "onclick='newTab(\"frmMain\",\"" . __HOST_NAME . __APP_PATH . __APP_CONTROLLER . "/public_in\")'");
//
        echo UIHelper::Button("btnExcel", "btnExcel", "Xuất Excel", "onclick='newTab(\"frmMain\",\"" . __HOST_NAME . __APP_PATH . __APP_CONTROLLER . "/public_excel\")'");
       
        ?>
    </div>
</form> 

<script type="text/javascript">
    function prepareThem() {
        showModal('dlg_<?php echo __APP_CONTROLLER; ?>');
        $("#sp_<?php echo __APP_CONTROLLER; ?>_Title").html("Thêm mới");
        $("#txt_<?php echo __APP_CONTROLLER; ?>_cusc_thinh_td_chuc_vu_ma").removeAttr('disabled', false).addClass("notered").val('');
        $("#txt_<?php echo __APP_CONTROLLER; ?>_cusc_thinh_td_chuc_vu_ten_vn").val('');
        $("#txt_<?php echo __APP_CONTROLLER; ?>_cusc_thinh_td_chuc_vu_ten_en").val('');
        $("#txt_<?php echo __APP_CONTROLLER; ?>_cusc_thinh_td_chuc_vu_diem_cong").val('');
        $("#txt_<?php echo __APP_CONTROLLER; ?>_cusc_thinh_td_chuc_vu_ghi_chu").val('');
        $('#bt_<?php echo __APP_CONTROLLER; ?>_submit').attr('onclick', '').unbind('click').click(function() {
            thucHienThem();
        });
    }// end function prepareThem
        
    function thucHienThem() {
        //Hàm thuchienThem sử dụng ajax gọi hàm add từ Controller và nhận kết quả trả về xml
        var dataString = $('#frm_<?php echo __APP_CONTROLLER; ?>').serialize();
        $("#dlg_<?php echo __APP_CONTROLLER; ?>").hide();
        $('#loading').show();
        $.ajax({
            type: "POST",
            url: "<?php echo __HOST_NAME . __APP_PATH . __APP_CONTROLLER; ?>/add",
            data: dataString,
            dataType: "xml",
            success: function(xml) {
                if (!xml) {
                    //Thất bại hiển thị lỗi
                    alert("Failed to connect.");
                }
                //Thành công thì submit trang
                $(xml).find('Result').each(function() {
                    var msg = $(this).find('msg').text();
                    if (msg == "OK") {
                        $('#frmMain').append("<input type='hidden' name='success' value='Đã thêm dữ liệu thành công!'/>");
                        document.frmMain.submit();
                    }
                    else {
                        showModal('dlg_<?php echo __APP_CONTROLLER; ?>');
                        $("#tb_msg_<?php echo __APP_CONTROLLER; ?>").show();
                        $("#ul_<?php echo __APP_CONTROLLER; ?>").html(decodeURI(msg)).show();
                        $('#loading').hide();
                    }
                });
            }
        });
    }// end function thucHienThem
    
    function goToEditList(name, index) {
            //Hàm goToEditList sử dụng ajax gọi hàm public_get_info từ Controller và nhận kết quả trả về xml
            showLoading();
            $.ajax({
                type: "POST",
                url: "<?php echo __HOST_NAME . __APP_PATH . __APP_CONTROLLER; ?>/public_get_info",
                data: 'id=' + $("#h_" + name + "_" + index).val(),
                dataType: "xml",
                success: function(xml) {
                    if (!xml) {
                        alert("Failed to connect.");
                    }
                    $(xml).find('Result').each(function() {
                        var msg = $(this).find('msg').text();
                        if (msg == "OK") {
                            // name: chính là $params->name đã định nghĩa trong hàm index()
                            // index: số tự tăng bắt đầu từ 0, tăng theo số dòng hiển thị trên danh sách
                            // dựa vào id để quy định dòng dữ liệu trong DB

                            // Xác định id dòng được chọn thông qua câu lệnh:
                            var id = $("#h_" + name + "_" + index).val();


                            var ma = $(this).find('cusc_thinh_td_chuc_vu_ma').text().htmlEntitiesDecode();
                            var ten = $(this).find('cusc_thinh_td_chuc_vu_ten_vn').text().htmlEntitiesDecode();
                            var ten_en = $(this).find('cusc_thinh_td_chuc_vu_ten_en').text().htmlEntitiesDecode();
                            var diem_cong = $(this).find('cusc_thinh_td_chuc_vu_diem_cong').text().htmlEntitiesDecode();
                            var ghi_chu = $(this).find('cusc_thinh_td_chuc_vu_ghi_chu').text().htmlEntitiesDecode();

                            $("#h_<?php echo __APP_CONTROLLER; ?>_id").val(id);
                            $("#txt_<?php echo __APP_CONTROLLER; ?>_cusc_thinh_td_chuc_vu_ma").val(ma);
                            $("#txt_<?php echo __APP_CONTROLLER; ?>_cusc_thinh_td_chuc_vu_ten_vn").val(ten);
                            $("#txt_<?php echo __APP_CONTROLLER; ?>_cusc_thinh_td_chuc_vu_ten_en").val(ten_en);
                            $("#txt_<?php echo __APP_CONTROLLER; ?>_cusc_thinh_td_chuc_vu_diem_cong").val(diem_cong);
                            $("#txt_<?php echo __APP_CONTROLLER; ?>_cusc_thinh_td_chuc_vu_ghi_chu").val(ghi_chu);
                            $("#dlg_<?php echo __APP_CONTROLLER; ?>").slideDown();
                            $('#loading').hide();
                            prepareSua();
                        } else {
                            alert("Không thể lấy thông tin theo yêu cầu.");
                            $('#loading').hide();
                            $('#overlay').slideUp();
                        }
                    });
                }
            });
        }// end function goToEditList
        
    function prepareSua() {
            $("#sp_<?php echo __APP_CONTROLLER; ?>_Title").html("Sửa chức vụ");
            //noi dung ma khong duoc phep chinh sua, disable = true. cac noi dung khong bat buoc nhap dung removeClass("notered")
            $("#txt_<?php echo __APP_CONTROLLER; ?>_cusc_thinh_td_chuc_vu_ma").attr('disabled', true).removeClass("notered");
            $('#bt_<?php echo __APP_CONTROLLER; ?>_submit').attr('onclick', '').unbind('click').click(function() {
                thucHienSua();
            });
        }// end function prepareSua
        
        
    function thucHienSua() {
            //Hàm thuchienThem sử dụng ajax gọi hàm add từ Controller và nhận kết quả trả về xml
            var dataString = $('#frm_<?php echo __APP_CONTROLLER; ?>').serialize();
            $("#dlg_<?php echo __APP_CONTROLLER; ?>").hide();
            $('#loading').show();
            $.ajax({
                type: "POST",
                url: "<?php echo __HOST_NAME . __APP_PATH . __APP_CONTROLLER; ?>/edit",
                data: dataString,
                dataType: "xml",
                success: function(xml) {
                    if (!xml) {
                        //Thất bại hiển thị lỗi
                        alert("Failed to connect.");
                    }
                    //Thành công thì submit trang
                    $(xml).find('Result').each(function() {
                        var msg = $(this).find('msg').text();
                        if (msg == "OK") {
                            $('#frmMain').append("<input type='hidden' name='success' value='Đã cập nhật dữ liệu thành công!'/>");
                            document.frmMain.submit();
                            $('#loading').hide();
                        }
                        else {
                            $("#dlg_<?php echo __APP_CONTROLLER; ?>").slideDown();
                            showModal('dlg_<?php echo __APP_CONTROLLER; ?>');
                            $("#tb_msg_<?php echo __APP_CONTROLLER; ?>").show();
                            $("#ul_<?php echo __APP_CONTROLLER; ?>").html(decodeURI(msg)).show();
                            $('#loading').hide();
                        }
                    });
                }
            });
        }// end function thucHienThem
        
        
    // Form thêm file
    function prepareThemTuFile() {
        $('#bt_form_them_tu_file_submit').attr('onclick', '').unbind('click').click(function() {
            thucHienThemTuFile();
        });
    }
    
    // Thực hiện thêm file
    function thucHienThemTuFile() {
        $('#frm_form_them_tu_file').attr('action', "<?php echo __HOST_NAME . __APP_PATH . __APP_CONTROLLER; ?>/themtufile");
        $('#frm_form_them_tu_file').submit();
    }
</script>