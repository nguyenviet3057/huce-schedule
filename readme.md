## Tổng quan Project

- Dự án được phát triển bằng ngôn ngữ **PHP 7.2** và framework **[Laravel 5.8](https://laravel.com/docs/5.8)**.
- CSDL sử dụng hệ quản trị **MySQL** thông qua **[xampp](https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/7.2.34/)**. Dữ liệu từ **[file Excel kỳ 2 năm học 2022-2023](/database/file/TKB%20HK2%2022-23%20-09-11-2022-14-34-55.xls)**, đầu ra dự kiến: file **[Docx](/database/file/Phan%20cong%20day%20de%20dieu%20chinh%20HK2%2022-23.docx)**
- Các vấn đề đang gặp phải, bài toán cần giải quyết, bugs sẽ nằm trong tab **[Issues](https://github.com/nguyenviet3057/huce-schedule/issues)**.
- *Đường dẫn demo sẽ được cập nhật sau (do hosting đang bị lỗi)*.

## Giao diện hiện có

- Hiển thị và nhập số alpha, theo từng môn học của từng giảng viên có dạy: [click]()
    ![](/public/image/ScreenShot_20230828132859.png "Trang nhập chỉ số alpha")
    Định dạng số là *float*, học phần mà giảng viên không dạy thì sẽ không có phần nhập số. Hiện mặc định được thống kê theo số lớp dạy của từng học phần theo từng giảng viên để hiển thị.

## Một số lưu ý

1. Thuật toán sắp xếp lịch giảng dạy dự kiến:

    - Bước 1: Xét môn có duy nhất 1 giảng viên dạy => ưu tiên xếp trước

    - Bước 2: Xét các lớp khóa mới ưu tiên xếp trước, Các môn có nhiều giảng viên dạy thì theo tỉ lệ trọng số để chia lớp, vẫn tính lớp ghép = 2x lớp đơn (Chuẩn hóa tỉ lệ và kiểm tra bộ số theo tỉ lệ có tổng đúng bằng tổng số lượng lớp của học phần - phân biệt lớp ghép và lớp đơn, giảm/tăng 1 lớp cho 1 số trong bộ nếu tổng bộ số bị vượt quá tổng số lượng lớp - ưu tiên không bị trùng lịch đối với các lịch đã được sắp xếp và tiếp đến thường ưu tiên cho hệ số alpha cao hơn để tinh chỉnh, sao cho không có lớp học phần nào bị trống giảng viên)

2. Lưu ý khác cần chú ý:
    - Thay đổi dữ liệu Mã học phần 471752, Mã lớp học 64PM2: Tuần học 257 -> 578 (SQL schedule_detail_1109_20230826 -> now: `UPDATE schedule_detail SET WEEK_STUDY = '             5 78   ' AND WEEKS = '[15,17,18]'
WHERE CLASS_CODE = 471752 AND ENROLL_CLASS = '64PM2' AND DAY_STUDY = 7`) ***(Do không khớp lịch của 2 lớp này do được ghép chung vào 1 lớp, chỉ khác đúng 1 tuần)*** [Issues 1](https://github.com/nguyenviet3057/huce-schedule/issues/1)
    - Thay đổi dữ liệu Mã học phần 471791, Mã lớp học 65PM5, 65PM6, thứ 3, Tuần ' 45 ': tiết 4-6 (ca 2) <schedule_detail_1521_20230826> -> tiết 1-3 (ca 1) <`UPDATE schedule_detail SET SESSION = 1 WHERE CLASS_CODE = 471791 AND ENROLL_CLASS IN ('65PM5', '65PM6') AND START_DATE = '2023-04-11'`> ***(Do bị trùng lịch, môn này chỉ có 1 giảng viên nhưng dạy tất cả các lớp trong cùng 1 ca, 1 ngày)*** [Issues 2](https://github.com/nguyenviet3057/huce-schedule/issues/2)
    - Thay đổi dữ liệu Mã học phần 471791, Mã lớp học 65PM3, 65PM4, thứ 4, Tuần ' 7 ': tiết 4-6 (ca 2) <schedule_detail_1529_20230826> -> tiết 1-3 (ca 1) <`UPDATE schedule_detail SET SESSION = 1 WHERE CLASS_CODE = 471791 AND ENROLL_CLASS IN ('65PM3', '65PM4') AND START_DATE = '2023-02-22'`> ***(Do bị trùng lịch, môn này chỉ có 1 giảng viên nhưng dạy tất cả các lớp trong cùng 1 ca, 1 ngày)*** [Issues 2](https://github.com/nguyenviet3057/huce-schedule/issues/2)

