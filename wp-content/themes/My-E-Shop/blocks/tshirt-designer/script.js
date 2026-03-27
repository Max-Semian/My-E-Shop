/**
 * T-Shirt Designer Frontend JavaScript
 */

(function($) {
    'use strict';

    class TShirtDesigner {
        constructor(blockId, options) {
            this.blockId = blockId;
            this.options = options;
            this.$block = $('#' + blockId);
            this.currentSide = 'front';
            this.canvas = {
                front: null,
                back: null
            };
            this.designs = {
                front: [],
                back: []
            };
            this.zoomLevel = 1;
            
            this.init();
        }

        init() {
            this.initCanvas();
            this.bindEvents();
            this.updateTShirtColor();
        }

        initCanvas() {
            const self = this;
            
            // Инициализируем canvas для передней стороны
            const frontCanvas = this.$block.find('[data-side="front"] .design-canvas')[0];
            if (frontCanvas && typeof fabric !== 'undefined') {
                this.canvas.front = new fabric.Canvas(frontCanvas, {
                    backgroundColor: 'transparent',
                    preserveObjectStacking: true
                });
            }

            // Инициализируем canvas для задней стороны
            const backCanvas = this.$block.find('[data-side="back"] .design-canvas')[0];
            if (backCanvas && typeof fabric !== 'undefined') {
                this.canvas.back = new fabric.Canvas(backCanvas, {
                    backgroundColor: 'transparent',
                    preserveObjectStacking: true
                });
            }
        }

        bindEvents() {
            const self = this;

            // Переключение между сторонами
            this.$block.find('.side-btn').on('click', function() {
                const side = $(this).data('side');
                self.switchSide(side);
            });

            // Инструменты
            this.$block.find('.tool-item').on('click', function() {
                const tool = $(this).data('tool');
                self.activateTool(tool);
            });

            // Зум
            this.$block.find('[data-action="zoom-in"]').on('click', function() {
                self.zoom(0.1);
            });

            this.$block.find('[data-action="zoom-out"]').on('click', function() {
                self.zoom(-0.1);
            });

            this.$block.find('[data-action="fit"]').on('click', function() {
                self.resetZoom();
            });

            // Сохранение дизайна
            this.$block.find('.btn-save-design').on('click', function() {
                self.saveDesign();
            });

            // Закрытие панели графики
            this.$block.find('.close-panel').on('click', function() {
                self.$block.find('.graphics-panel').fadeOut(300);
            });

            // Выбор графики
            this.$block.find('.graphic-item').on('click', function() {
                const url = $(this).data('url');
                self.addGraphic(url);
            });

            // Клавиатурные сокращения
            $(document).on('keydown', function(e) {
                if (self.$block.is(':visible')) {
                    if (e.key === 'Delete' || e.key === 'Backspace') {
                        self.deleteSelected();
                    }
                }
            });
        }

        switchSide(side) {
            if (side === this.currentSide) return;

            this.currentSide = side;
            
            // Обновляем кнопки
            this.$block.find('.side-btn').removeClass('active');
            this.$block.find('.side-btn[data-side="' + side + '"]').addClass('active');
            
            // Показываем нужную сторону
            this.$block.find('.tshirt-view').hide();
            this.$block.find('.tshirt-view[data-side="' + side + '"]').fadeIn(300);
        }

        activateTool(tool) {
            const self = this;
            
            // Обновляем активный инструмент
            this.$block.find('.tool-item').removeClass('active');
            this.$block.find('.tool-item[data-tool="' + tool + '"]').addClass('active');

            switch(tool) {
                case 'upload':
                    this.uploadImage();
                    break;
                case 'text':
                    this.addText();
                    break;
                case 'graphics':
                    this.$block.find('.graphics-panel').fadeIn(300);
                    break;
                case 'library':
                    alert('My Library - feature in development');
                    break;
                case 'templates':
                    alert('My Templates - feature in development');
                    break;
                case 'shutterstock':
                    alert('Shutterstock Integration - feature in development');
                    break;
                case 'fiverr':
                    alert('Fiverr Integration - feature in development');
                    break;
            }
        }

        uploadImage() {
            const self = this;
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            
            input.onchange = function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        self.addImage(event.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            };
            
            input.click();
        }

        addImage(imageUrl) {
            const currentCanvas = this.canvas[this.currentSide];
            if (!currentCanvas) return;

            fabric.Image.fromURL(imageUrl, function(img) {
                img.scaleToWidth(150);
                img.set({
                    left: currentCanvas.width / 2 - (img.width * img.scaleX) / 2,
                    top: currentCanvas.height / 2 - (img.height * img.scaleY) / 2
                });
                currentCanvas.add(img);
                currentCanvas.setActiveObject(img);
                currentCanvas.renderAll();
            });
        }

        addGraphic(url) {
            this.addImage(url);
            this.$block.find('.graphics-panel').fadeOut(300);
        }

        addText() {
            const currentCanvas = this.canvas[this.currentSide];
            if (!currentCanvas) return;

            const text = new fabric.IText('Text', {
                left: currentCanvas.width / 2,
                top: currentCanvas.height / 2,
                fontFamily: 'Arial',
                fontSize: 40,
                fill: '#000000',
                originX: 'center',
                originY: 'center'
            });

            currentCanvas.add(text);
            currentCanvas.setActiveObject(text);
            text.enterEditing();
            text.selectAll();
            currentCanvas.renderAll();
        }

        deleteSelected() {
            const currentCanvas = this.canvas[this.currentSide];
            if (!currentCanvas) return;

            const activeObject = currentCanvas.getActiveObject();
            if (activeObject) {
                currentCanvas.remove(activeObject);
                currentCanvas.renderAll();
            }
        }

        zoom(delta) {
            this.zoomLevel += delta;
            this.zoomLevel = Math.max(0.5, Math.min(2, this.zoomLevel));
            
            const $canvas = this.$block.find('.tshirt-canvas');
            $canvas.css('transform', 'scale(' + this.zoomLevel + ')');
            
            const percentage = Math.round(this.zoomLevel * 100);
            this.$block.find('.zoom-level').text(percentage + '%');
        }

        resetZoom() {
            this.zoomLevel = 1;
            this.$block.find('.tshirt-canvas').css('transform', 'scale(1)');
            this.$block.find('.zoom-level').text('100%');
        }

        updateTShirtColor() {
            const color = this.options.tshirtColor || '#FFFFFF';
            this.$block.find('.tshirt-body, .tshirt-sleeve-left, .tshirt-sleeve-right, .tshirt-neck').attr('fill', color);
        }

        saveDesign() {
            const designs = {
                front: this.canvas.front ? this.canvas.front.toJSON() : null,
                back: this.canvas.back ? this.canvas.back.toJSON() : null,
                frontImage: this.canvas.front ? this.canvas.front.toDataURL() : null,
                backImage: this.canvas.back ? this.canvas.back.toDataURL() : null,
                tshirtColor: this.options.tshirtColor
            };

            console.log('Saving design:', designs);
            
            // Здесь можно отправить данные на сервер через AJAX
            // $.ajax({
            //     url: '/wp-admin/admin-ajax.php',
            //     method: 'POST',
            //     data: {
            //         action: 'save_tshirt_design',
            //         design: JSON.stringify(designs)
            //     },
            //     success: function(response) {
            //         alert('Дизайн сохранен!');
            //     }
            // });

            alert('Design saved! (Check browser console for data)');
        }
    }

    // Инициализация при загрузке страницы
    $(document).ready(function() {
        if (typeof window.tshirtDesignerData !== 'undefined') {
            $.each(window.tshirtDesignerData, function(blockId, options) {
                new TShirtDesigner(blockId, options);
            });
        }
    });

})(jQuery);
