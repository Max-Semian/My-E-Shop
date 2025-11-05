<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6">
                <div class="footer-logo">
                    <a href="/">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/footer-logo.png" alt="Crethos">
                    </a>
                </div>
                <p>
                    Your individuality, trending now
                </p>
            </div>
            <div class="col-md-3 col-6">
                <h4>About Us</h4>
                <ul class="list-unstyled">
                    <li><a href="index.html">Our Story</a></li>
                    <li><a href="#">Trends</a></li>
                    <li><a href="#">HOME</a></li>
                </ul>
            </div>
            <div class="col-md-3 col-6">
                <h4>Support</h4>
                <ul class="list-unstyled">
                    <li>FAQ</li>
                    <li>Shipping & Payment</li>
                    <li>Returns & Exchanges</li>
                    <li>Contact Us</li>
                </ul>
            </div>
            <div class="col-md-3 col-6">
                <h4>Legal</h4>
                <ul class="list-unstyled">
                    <li>Privacy Policy</li>
                    <li>Terms of Service</li>
                </ul>
            </div>
            <div class="col-md-3 col-6">
                <h4>Follow Us</h4>
                <ul class="list-unstyled">
                    <li>Instagram</li>
                    <li>Pinterest</li>
                    <li>TikTok</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom" style="border-top: 1px solid #ddd; padding-top: 40px;">
            <img class="footer-social-icon" src="<?php echo get_template_directory_uri(); ?>/assets/img/Social-media.png" alt="Crethos" style="height: 24px; margin-top: 10px;">
            <img class="footer-social-icon" src="<?php echo get_template_directory_uri(); ?>/assets/img/Social-media1.png" alt="Crethos" style="height: 24px; margin-top: 10px;">
            <img class="footer-social-icon" src="<?php echo get_template_directory_uri(); ?>/assets/img/Social-media2.png" alt="Crethos" style="height: 24px; margin-top: 10px;">    
            <img class="footer-social-icon" src="<?php echo get_template_directory_uri(); ?>/assets/img/Social-media3.png" alt="Crethos" style="height: 24px; margin-top: 10px;">
            <p class="footer-copy" style="padding-top: 20px;">&copy; 2024 My E-Shop. All rights reserved.</p>
    </div>
</footer>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ищем все возможные варианты
    const shopButton1 = document.querySelector('.nav-link.dropdown-toggle');
    const shopButton2 = document.querySelector('a[href*="shop"]');
    const shopButton3 = document.querySelector('.dropdown-toggle');
    
    const dropdown1 = document.querySelector('.shop-dropdown');
    const dropdown2 = document.querySelector('.dropdown-menu');
    const dropdown3 = document.querySelector('ul.shop-dropdown');
    
    const dropdownParent = document.querySelector('.nav-item.dropdown');
    
    const shopButton = shopButton1 || shopButton2 || shopButton3;
    const dropdown = dropdown1 || dropdown2 || dropdown3;
    
         if (shopButton && dropdown && dropdownParent) {
         let hideTimeout;
         
         dropdownParent.addEventListener('mouseenter', function() {
             // Отменяем скрытие если оно было запланировано
             if (hideTimeout) {
                 clearTimeout(hideTimeout);
                 hideTimeout = null;
             }
             const rect = shopButton.getBoundingClientRect();
             
             // Восстанавливаем нужные классы для стилизации внутренних элементов
             dropdown.className = 'dropdown-menu shop-dropdown';
             dropdown.removeAttribute('style');
             
             // Применяем только наши стили
             dropdown.style.cssText = `
                 position: fixed !important;
                 top: ${rect.bottom + 5}px !important;
                 left: ${rect.left}px !important;
                 display: block !important;
                 opacity: 1 !important;
                 visibility: visible !important;
                 transform: translateY(0) !important;
                 z-index: 2147483647 !important;
                 background: white !important;
                 border: 1px solid #ccc !important;
                 border-radius: 12px !important;
                 box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
                 padding: 15px !important;
                 min-width: 250px !important;
                 max-height: none !important;
                 overflow: visible !important;
                 transition: none !important;
                 animation: none !important;
                 pointer-events: auto !important;
             `;
             
             // Перемещаем dropdown в конец body для гарантии правильного z-index
             document.body.appendChild(dropdown);
             
             // Добавляем стили для внутренних элементов
             const dropdownItems = dropdown.querySelectorAll('.dropdown-item');
             dropdownItems.forEach((item, index) => {
                 item.style.cssText = `
                     display: flex !important;
                     align-items: center !important;
                     padding: 12px 20px !important;
                     color: #333333 !important;
                     text-decoration: none !important;
                     font-size: 14px !important;
                     font-weight: 500 !important;
                     font-family: 'Montserrat', sans-serif !important;
                     transition: all 0.3s ease !important;
                     border: none !important;
                     background: transparent !important;
                     opacity: 1 !important;
                     transform: translateX(0) !important;
                     animation: none !important;
                     position: relative !important;
                     overflow: hidden !important;
                 `;
                 
                 // Добавляем hover эффект
                 item.addEventListener('mouseenter', function() {
                     this.style.background = 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important';
                     this.style.color = '#D85AFF !important';
                     this.style.transform = 'translateX(5px) !important';
                 });
                 
                 item.addEventListener('mouseleave', function() {
                     this.style.background = 'transparent !important';
                     this.style.color = '#333333 !important';
                     this.style.transform = 'translateX(0) !important';
                 });
             });
             
             // Стилизуем иконки
             const dropdownIcons = dropdown.querySelectorAll('.dropdown-icon');
             dropdownIcons.forEach(icon => {
                 icon.style.cssText = `
                     width: 35px !important;
                     height: 35px !important;
                     background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%) !important;
                     border-radius: 8px !important;
                     display: flex !important;
                     align-items: center !important;
                     justify-content: center !important;
                     margin-right: 12px !important;
                     transition: all 0.3s ease !important;
                     font-size: 16px !important;
                     color: #666 !important;
                 `;
             });
             
             // Стилизуем текст
             const dropdownTexts = dropdown.querySelectorAll('.dropdown-text');
             dropdownTexts.forEach(text => {
                 text.style.cssText = `
                     flex: 1 !important;
                     font-weight: 500 !important;
                     letter-spacing: 0.3px !important;
                     color: #333333 !important;
                 `;
             });
         });
        
                 dropdownParent.addEventListener('mouseleave', function(e) {
             // Устанавливаем задержку перед скрытием
             hideTimeout = setTimeout(function() {
                 dropdown.style.display = 'none';
             }, 100);
         });
         
         // Добавляем события для самого dropdown
         dropdown.addEventListener('mouseenter', function() {
             // Отменяем скрытие если курсор на dropdown
             if (hideTimeout) {
                 clearTimeout(hideTimeout);
                 hideTimeout = null;
             }
         });
         
         dropdown.addEventListener('mouseleave', function() {
             // Небольшая задержка для плавности
             hideTimeout = setTimeout(function() {
                 dropdown.style.display = 'none';
             }, 100);
         });
        
        // Также добавим события для самого dropdown
        dropdown.addEventListener('mouseenter', function() {
            dropdown.style.display = 'block';
            dropdown.style.opacity = '1';
            dropdown.style.visibility = 'visible';
            dropdown.style.transform = 'translateY(0)';
        });
    }
});
</script>
<?php wp_footer();?>
</body>
</html>