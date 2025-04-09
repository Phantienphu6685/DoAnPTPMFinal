describe('Kiểm tra danh sách quận và phường', () => {
    beforeEach(() => {
      cy.visit('http://localhost/thue_phong/index.php');  // Thay đổi URL đến trang mà bạn cần kiểm tra
    });
  
    it('Hiển thị danh sách quận', () => {
      // Kiểm tra xem có hiển thị đúng các quận từ dropdown quận
      cy.get('#district').should('be.visible');  // Kiểm tra xem dropdown quận có hiển thị không
      cy.get('#district').children('option').should('have.length.greaterThan', 1); // Kiểm tra ít nhất có 1 lựa chọn ngoài "Chọn quận"
      
      // Kiểm tra các quận cụ thể
      cy.get('#district').children('option').contains('Quận Thủ Đức');
      cy.get('#district').children('option').contains('Bình Thạnh');
      cy.get('#district').children('option').contains('Quận 9');
    });
  
    it('Hiển thị danh sách phường khi chọn quận', () => {
      // Mô phỏng hành động chọn quận
      cy.get('#district').select('Quận Thủ Đức');  // Thay 'Quận Thủ Đức' bằng quận bạn muốn kiểm tra
      
      // Thêm thời gian chờ để đảm bảo dropdown phường đã được cập nhật
      cy.wait(500);  // Chờ một khoảng thời gian để đảm bảo danh sách phường được cập nhật
      
      // Kiểm tra xem dropdown phường có được kích hoạt không
      cy.get('#ward').should('not.be.disabled');
      cy.get('#ward').children('option').should('have.length.greaterThan', 1); // Kiểm tra ít nhất có 1 lựa chọn ngoài "Chọn phường"
      
      // Kiểm tra các phường của Quận Thủ Đức (Ví dụ bạn có các phường như 'Phường Linh Xuân', 'Phường Bình Chiểu',...)
      cy.get('#ward').children('option').contains('Phường Linh Xuân');
      cy.get('#ward').children('option').contains('Phường Bình Chiểu');
    });
  });
