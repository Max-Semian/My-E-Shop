<?php
/**
 * FAQ Block Template
 */

$items = $attributes['items'] ?? [];

if (empty($items)) {
    return;
}
?>

<div class="faq-block">
    <?php foreach ($items as $index => $item) : 
        $question = $item['question'] ?? '';
        $answer = $item['answer'] ?? '';
    ?>
        <div class="faq-item" data-index="<?php echo esc_attr($index); ?>">
            <div class="faq-question">
                <?php echo esc_html($question); ?>
            </div>
            <div class="faq-answer">
                <?php 
                if (!empty($answer)) {
                    echo nl2br(esc_html($answer));
                } else {
                    echo '<p style="color: #999; font-style: italic;">Ответ не заполнен</p>';
                }
                ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
