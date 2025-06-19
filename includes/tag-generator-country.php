<?php
// Template for country tag generator
?>
<div class="control-box">
    <fieldset>
        <legend><?php echo esc_html($description); ?></legend>
        
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><?php echo esc_html(__('Field type', 'cf7cs-country-city-selector')); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><?php echo esc_html(__('Field type', 'cf7cs-country-city-selector')); ?></legend>
                            <label><input type="checkbox" name="required" /> <?php echo esc_html(__('Required field', 'cf7cs-country-city-selector')); ?></label>
                        </fieldset>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><label for="<?php echo esc_attr($args['content'] . '-name'); ?>"><?php echo esc_html(__('Name', 'cf7cs-country-city-selector')); ?></label></th>
                    <td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr($args['content'] . '-name'); ?>" /></td>
                </tr>
                
                <tr>
                    <th scope="row"><label for="<?php echo esc_attr($args['content'] . '-id'); ?>"><?php echo esc_html(__('Id attribute', 'cf7cs-country-city-selector')); ?></label></th>
                    <td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr($args['content'] . '-id'); ?>" /></td>
                </tr>
                
                <tr>
                    <th scope="row"><label for="<?php echo esc_attr($args['content'] . '-class'); ?>"><?php echo esc_html(__('Class attribute', 'cf7cs-country-city-selector')); ?></label></th>
                    <td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr($args['content'] . '-class'); ?>" /></td>
                </tr>
            </tbody>
        </table>
    </fieldset>
</div>

<div class="insert-box">
    <input type="text" name="<?php echo esc_attr($type); ?>" class="tag code" readonly="readonly" onfocus="this.select()" />
    
    <div class="submitbox">
        <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr(__('Insert Tag', 'cf7cs-country-city-selector')); ?>" />
    </div>
    
    <br class="clear" />
    
    <p class="description mail-tag">
        <label for="<?php echo esc_attr($args['content'] . '-mailtag'); ?>">
            <?php echo sprintf(esc_html(__("To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'cf7cs-country-city-selector')), '<strong><span class="mail-tag"></span></strong>'); ?>
            <input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr($args['content'] . '-mailtag'); ?>" />
        </label>
    </p>
</div>
