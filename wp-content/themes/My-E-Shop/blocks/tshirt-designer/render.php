<?php
/**
 * T-Shirt Designer Block Template
 */

$background_color = !empty($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#F5F5F0';
$tshirt_color = !empty($attributes['tshirtColor']) ? $attributes['tshirtColor'] : '#FFFFFF';
$print_area_width = !empty($attributes['printAreaWidth']) ? $attributes['printAreaWidth'] : 280;
$print_area_height = !empty($attributes['printAreaHeight']) ? $attributes['printAreaHeight'] : 350;
$enable_text = !empty($attributes['enableTextTool']) ? $attributes['enableTextTool'] : true;
$enable_graphics = !empty($attributes['enableGraphicsTool']) ? $attributes['enableGraphicsTool'] : true;
$enable_upload = !empty($attributes['enableUploadTool']) ? $attributes['enableUploadTool'] : true;
$allowed_graphics = !empty($attributes['allowedGraphics']) ? $attributes['allowedGraphics'] : [];

$block_id = 'tshirt-designer-' . wp_rand(1000, 9999);
?>

<section class="tshirt-designer-block" id="<?php echo esc_attr($block_id); ?>" style="background-color: <?php echo esc_attr($background_color); ?>;">
    <div class="container py-5">
        <div class="tshirt-designer-wrapper">
            <!-- Left tools panel -->
            <div class="designer-tools-sidebar">
                <div class="tools-header">
                    <h4>Tools</h4>
                </div>
                
                <?php if ($enable_upload): ?>
                <div class="tool-item" data-tool="upload">
                    <div class="tool-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                        </svg>
                    </div>
                    <div class="tool-label">Upload</div>
                </div>
                <?php endif; ?>

                <?php if ($enable_text): ?>
                <div class="tool-item" data-tool="text">
                    <div class="tool-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="4 7 4 4 20 4 20 7"/>
                            <line x1="9" y1="20" x2="15" y2="20"/>
                            <line x1="12" y1="4" x2="12" y2="20"/>
                        </svg>
                    </div>
                    <div class="tool-label">Add text</div>
                </div>
                <?php endif; ?>

                <?php if ($enable_graphics): ?>
                <div class="tool-item" data-tool="graphics">
                    <div class="tool-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"/>
                            <rect x="14" y="3" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/>
                            <rect x="3" y="14" width="7" height="7"/>
                        </svg>
                    </div>
                    <div class="tool-label">Graphics</div>
                </div>
                <?php endif; ?>

                <div class="tool-item" data-tool="library">
                    <div class="tool-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="9"/>
                            <rect x="3" y="15" width="7" height="6"/>
                            <rect x="13" y="3" width="8" height="6"/>
                            <rect x="13" y="12" width="8" height="9"/>
                        </svg>
                    </div>
                    <div class="tool-label">My library</div>
                </div>

                <div class="tool-item" data-tool="templates">
                    <div class="tool-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="2" width="20" height="8" rx="2" ry="2"/>
                            <rect x="2" y="14" width="20" height="8" rx="2" ry="2"/>
                        </svg>
                    </div>
                    <div class="tool-label">My templates</div>
                </div>

                <div class="tool-item" data-tool="shutterstock">
                    <div class="tool-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                            <line x1="9" y1="9" x2="9.01" y2="9"/>
                            <line x1="15" y1="9" x2="15.01" y2="9"/>
                        </svg>
                    </div>
                    <div class="tool-label">Shutterstock</div>
                </div>

                <div class="tool-item" data-tool="fiverr">
                    <div class="tool-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 6v12M6 12h12"/>
                        </svg>
                    </div>
                    <div class="tool-label">Fiverr</div>
                </div>
            </div>

            <!-- Central area with the T-shirt -->
            <div class="designer-canvas-area">
                <div class="canvas-controls">
                    <div class="zoom-controls">
                        <button class="zoom-btn" data-action="zoom-out">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </button>
                        <span class="zoom-level">11%</span>
                        <button class="zoom-btn" data-action="zoom-in">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </button>
                        <button class="zoom-btn" data-action="fit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="tshirt-canvas" data-tshirt-color="<?php echo esc_attr($tshirt_color); ?>">
                    <div class="tshirt-view" data-side="front">
                        <!-- T-shirt SVG -->
                        <svg class="tshirt-svg" viewBox="0 0 400 500" xmlns="http://www.w3.org/2000/svg">
                            <!-- T-shirt body -->
                            <path class="tshirt-body" d="M 100,80 Q 80,85 75,100 L 75,400 Q 75,440 100,470 L 300,470 Q 325,440 325,400 L 325,100 Q 320,85 300,80 L 300,80 Q 280,75 250,70 Q 230,50 200,45 Q 170,50 150,70 Q 120,75 100,80 Z" fill="<?php echo esc_attr($tshirt_color); ?>" stroke="#e0e0e0" stroke-width="2"/>
                            
                            <!-- Sleeves -->
                            <path class="tshirt-sleeve-left" d="M 100,80 L 50,100 L 50,180 L 75,165 L 75,100" fill="<?php echo esc_attr($tshirt_color); ?>" stroke="#e0e0e0" stroke-width="2"/>
                            <path class="tshirt-sleeve-right" d="M 300,80 L 350,100 L 350,180 L 325,165 L 325,100" fill="<?php echo esc_attr($tshirt_color); ?>" stroke="#e0e0e0" stroke-width="2"/>
                            
                            <!-- Neckline -->
                            <ellipse class="tshirt-neck" cx="200" cy="60" rx="35" ry="20" fill="<?php echo esc_attr($tshirt_color); ?>" stroke="#e0e0e0" stroke-width="2"/>
                        </svg>

                        <!-- Print area -->
                        <div class="print-area" style="
                            width: <?php echo esc_attr($print_area_width); ?>px;
                            height: <?php echo esc_attr($print_area_height); ?>px;
                        " data-width="<?php echo esc_attr($print_area_width); ?>" data-height="<?php echo esc_attr($print_area_height); ?>">
                            <div class="print-area-border"></div>
                            <canvas class="design-canvas" width="<?php echo esc_attr($print_area_width); ?>" height="<?php echo esc_attr($print_area_height); ?>"></canvas>
                        </div>
                    </div>

                    <div class="tshirt-view" data-side="back" style="display: none;">
                        <!-- Back side - same structure -->
                        <svg class="tshirt-svg" viewBox="0 0 400 500" xmlns="http://www.w3.org/2000/svg">
                            <path class="tshirt-body" d="M 100,80 Q 80,85 75,100 L 75,400 Q 75,440 100,470 L 300,470 Q 325,440 325,400 L 325,100 Q 320,85 300,80 L 300,80 Q 280,75 250,70 Q 230,50 200,45 Q 170,50 150,70 Q 120,75 100,80 Z" fill="<?php echo esc_attr($tshirt_color); ?>" stroke="#e0e0e0" stroke-width="2"/>
                            <path class="tshirt-sleeve-left" d="M 100,80 L 50,100 L 50,180 L 75,165 L 75,100" fill="<?php echo esc_attr($tshirt_color); ?>" stroke="#e0e0e0" stroke-width="2"/>
                            <path class="tshirt-sleeve-right" d="M 300,80 L 350,100 L 350,180 L 325,165 L 325,100" fill="<?php echo esc_attr($tshirt_color); ?>" stroke="#e0e0e0" stroke-width="2"/>
                        </svg>

                        <div class="print-area" style="
                            width: <?php echo esc_attr($print_area_width); ?>px;
                            height: <?php echo esc_attr($print_area_height); ?>px;
                        " data-width="<?php echo esc_attr($print_area_width); ?>" data-height="<?php echo esc_attr($print_area_height); ?>">
                            <div class="print-area-border"></div>
                            <canvas class="design-canvas" width="<?php echo esc_attr($print_area_width); ?>" height="<?php echo esc_attr($print_area_height); ?>"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Side switcher -->
                <div class="side-switcher">
                    <button class="side-btn active" data-side="front">Front side</button>
                    <button class="side-btn" data-side="back">Back side</button>
                </div>

                <!-- Save button -->
                <div class="designer-actions">
                    <button class="btn-save-design">Save product</button>
                </div>
            </div>

            <!-- Graphics panel (shown when the Graphics tool is selected) -->
            <?php if ($enable_graphics && !empty($allowed_graphics)): ?>
            <div class="graphics-panel" style="display: none;">
                <div class="graphics-header">
                    <h4>Graphics Library</h4>
                    <button class="close-panel">×</button>
                </div>
                <div class="graphics-grid">
                    <?php foreach ($allowed_graphics as $graphic): ?>
                    <div class="graphic-item" data-url="<?php echo esc_attr($graphic['url']); ?>">
                        <img src="<?php echo esc_url($graphic['url']); ?>" alt="<?php echo esc_attr($graphic['alt']); ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Hidden data for JS -->
    <script>
    if (typeof window.tshirtDesignerData === 'undefined') {
        window.tshirtDesignerData = {};
    }
    window.tshirtDesignerData['<?php echo esc_js($block_id); ?>'] = {
        printAreaWidth: <?php echo (int) $print_area_width; ?>,
        printAreaHeight: <?php echo (int) $print_area_height; ?>,
        tshirtColor: '<?php echo esc_js($tshirt_color); ?>',
        enableText: <?php echo $enable_text ? 'true' : 'false'; ?>,
        enableGraphics: <?php echo $enable_graphics ? 'true' : 'false'; ?>,
        enableUpload: <?php echo $enable_upload ? 'true' : 'false'; ?>,
        graphics: <?php echo wp_json_encode($allowed_graphics); ?>
    };
    </script>
</section>
